<?php
// ไฟล์: report_management.php
require_once 'config.php';

// 1. ตรวจสอบสิทธิ์การเข้าถึง
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบระดับ (อนุญาต Admin, Super Admin และพนักงานระดับ 3 ขึ้นไป)
$allow_access = ($_SESSION['user_level'] == 'admin' || $_SESSION['user_level'] == 'super_admin' || $_SESSION['employee_level'] >= 3);
if (!$allow_access) {
    echo "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    exit;
}

// 2. ตั้งค่าตัวแปร Filter
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // วันแรกของเดือนปัจจุบัน
$end_date   = $_GET['end_date'] ?? date('Y-m-t');   // วันสุดท้ายของเดือน
$report_type = $_GET['report_type'] ?? 'attendance';

// --- LOGIC: การจำกัดสิทธิ์ดูแผนก ---
$is_super_admin = ($_SESSION['user_level'] == 'super_admin');
$my_dept_id = $_SESSION['department_id'];
$dept_name_display = "แผนกของคุณ";

if ($is_super_admin) {
    // ถ้าเป็น Super Admin ให้เลือกแผนกได้ (ถ้าไม่เลือก = ดูทั้งหมด)
    $filter_dept_id = isset($_GET['department_id']) ? $_GET['department_id'] : 'all';
    $dept_sql_condition = ($filter_dept_id != 'all') ? "AND u.department_id = '$filter_dept_id'" : "";
} else {
    // ถ้าเป็นคนทั่วไป บังคับดูแค่แผนกตัวเองเท่านั้น
    $filter_dept_id = $my_dept_id;
    $dept_sql_condition = "AND u.department_id = '$my_dept_id'";
    
    // หาชื่อแผนกเพื่อมาแสดงหัวข้อ
    $d_query = mysqli_query($conn, "SELECT name FROM departments WHERE id = '$my_dept_id'");
    $d_res = mysqli_fetch_assoc($d_query);
    $dept_name_display = $d_res['name'];
}

// --- ARRAY แปลงภาษาไทย ---
$shift_mapping = [
    'morning' => 'เช้า',
    'afternoon' => 'บ่าย',
    'night' => 'ดึก',
    'day' => 'เช้า (Day)',
    'night_shift' => 'ดึก (Night)',
    'morning_afternoon' => 'เช้า-บ่าย',
    'morning_night' => 'เช้า-ดึก',
    'afternoon_night' => 'บ่าย-ดึก'
];

$leave_type_mapping = [
    'sick' => 'ลาป่วย',
    'sick_leave' => 'ลาป่วย',
    'personal' => 'ลากิจ',
    'vacation' => 'พักร้อน',
    'holiday' => 'ลาพักร้อน'
];

$status_mapping = [
    'approved' => 'อนุมัติแล้ว',
    'rejected' => 'ปฏิเสธ',
    'pending' => 'รออนุมัติ',
    'cancelled' => 'ยกเลิก'
];

// 3. ดึงข้อมูลรายงาน (Query)
$data = [];
$summary = [
    'total_records' => 0,
    'late_count' => 0,
    'leave_count' => 0,
    'swap_count' => 0
];

if ($report_type == 'attendance') {
    // --- รายงานการลงเวลา ---
    $sql = "SELECT u.full_name, d.name as dept_name, s.schedule_date, s.shift_type,
            MIN(CASE WHEN a.action_type = 'check_in' THEN a.scan_time END) as check_in,
            MAX(CASE WHEN a.action_type = 'check_out' THEN a.scan_time END) as check_out,
            al.severity as late_status
            FROM schedules s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN attendance_logs a ON s.user_id = a.user_id AND DATE(a.scan_time) = s.schedule_date
            LEFT JOIN audit_logs al ON al.user_id = u.id AND al.action_type = 'late_checkin' AND DATE(al.created_at) = s.schedule_date
            WHERE s.schedule_date BETWEEN '$start_date' AND '$end_date'
            $dept_sql_condition
            GROUP BY s.user_id, s.schedule_date
            ORDER BY s.schedule_date DESC, u.full_name ASC";
            
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
        // คำนวณสถิติเบื้องต้น
        if ($row['late_status']) $summary['late_count']++;
        if ($row['check_in']) $summary['total_records']++; 
    }

} elseif ($report_type == 'leave') {
    // --- รายงานการลา (รวมลาปกติ + ลาล่วงหน้า) ---
    // แก้ไข: ระบุ lr.created_at และ fl.created_at เพื่อไม่ให้ชื่อซ้ำกัน (Ambiguous)
    $sql = "SELECT u.full_name, d.name as dept_name, 'ลาปกติ' as source, request_type, start_date, end_date, reason, status, lr.created_at
            FROM leave_requests lr
            JOIN users u ON lr.user_id = u.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE (start_date BETWEEN '$start_date' AND '$end_date') $dept_sql_condition
            UNION ALL
            SELECT u.full_name, d.name as dept_name, 'ลาล่วงหน้า' as source, request_type, start_date, end_date, reason, status, fl.created_at
            FROM future_leave_requests fl
            JOIN users u ON fl.user_id = u.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE (start_date BETWEEN '$start_date' AND '$end_date') $dept_sql_condition
            ORDER BY start_date DESC";
            
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
        if($row['status'] == 'approved') $summary['leave_count']++;
        $summary['total_records']++;
    }

} elseif ($report_type == 'swap') {
    // --- รายงานการแลกเวร ---
    // แก้ไข: เปลี่ยนเงื่อนไขจาก u. เป็น u1. (อ้างอิงแผนกจากผู้ขอแลก)
    $swap_dept_condition = str_replace('u.department_id', 'u1.department_id', $dept_sql_condition);

    $sql = "SELECT sr.*, u1.full_name as requester, u2.full_name as target, u3.full_name as approver, d.name as dept_name
            FROM swap_requests sr
            JOIN users u1 ON sr.user_id = u1.id
            LEFT JOIN departments d ON u1.department_id = d.id
            JOIN users u2 ON sr.target_user_id = u2.id
            LEFT JOIN users u3 ON sr.approved_by = u3.id
            WHERE DATE(sr.created_at) BETWEEN '$start_date' AND '$end_date'
            $swap_dept_condition
            ORDER BY sr.created_at DESC";
            
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
        if($row['status'] == 'approved') $summary['swap_count']++;
        $summary['total_records']++;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard รายงาน - <?= htmlspecialchars($dept_name_display) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --bg-color: #f1f5f9;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--bg-color);
            color: #334155;
        }
        .main-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            border: none;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s;
            border-left: 5px solid transparent;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin-bottom: 10px;
        }
        
        /* Specific Card Colors */
        .card-blue { border-left-color: #3b82f6; }
        .card-blue .stat-icon { background: #eff6ff; color: #3b82f6; }
        
        .card-red { border-left-color: #ef4444; }
        .card-red .stat-icon { background: #fef2f2; color: #ef4444; }
        
        .card-green { border-left-color: #10b981; }
        .card-green .stat-icon { background: #ecfdf5; color: #10b981; }
        
        .card-purple { border-left-color: #8b5cf6; }
        .card-purple .stat-icon { background: #f5f3ff; color: #8b5cf6; }

        .filter-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .table thead th {
            background-color: #1e293b;
            color: white;
            font-weight: 500;
            border: none;
        }
        .badge-status {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .badge-shift {
            background-color: #e2e8f0;
            color: #334155;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.9em;
            font-weight: 500;
        }
        
        /* Override DataTables Buttons */
        .dt-buttons .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9em;
            margin-right: 5px;
            border: none;
        }
        .btn-excel { background-color: #10b981; color: white; }
        .btn-pdf { background-color: #ef4444; color: white; }
    </style>
</head>
<body>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">📊 รายงานระบบจัดเวร</h2>
            <p class="text-muted mb-0">
                <?= $is_super_admin ? 'มุมมองผู้ดูแลระบบสูงสุด (Super Admin)' : 'มุมมองหัวหน้าแผนก: ' . htmlspecialchars($dept_name_display) ?>
            </p>
        </div>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> กลับ Dashboard
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card card-blue">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase">รายการทั้งหมด</div>
                        <h3 class="fw-bold mt-1 mb-0"><?= number_format($summary['total_records']) ?></h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-list"></i></div>
                </div>
            </div>
        </div>

        <?php if($report_type == 'attendance'): ?>
        <div class="col-md-3">
            <div class="stat-card card-red">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase">เข้างานสาย</div>
                        <h3 class="fw-bold mt-1 mb-0"><?= number_format($summary['late_count']) ?></h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-user-clock"></i></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($report_type == 'leave'): ?>
        <div class="col-md-3">
            <div class="stat-card card-purple">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase">อนุมัติการลา</div>
                        <h3 class="fw-bold mt-1 mb-0"><?= number_format($summary['leave_count']) ?></h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-plane-departure"></i></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($report_type == 'swap'): ?>
        <div class="col-md-3">
            <div class="stat-card card-green">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase">แลกเวรสำเร็จ</div>
                        <h3 class="fw-bold mt-1 mb-0"><?= number_format($summary['swap_count']) ?></h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="main-card">
        
        <form method="GET" class="filter-box">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold"><i class="fas fa-filter me-1"></i> ประเภทรายงาน</label>
                    <select name="report_type" class="form-select" onchange="this.form.submit()">
                        <option value="attendance" <?= $report_type=='attendance'?'selected':''; ?>>⏰ รายงานการลงเวลา (Attendance)</option>
                        <option value="leave" <?= $report_type=='leave'?'selected':''; ?>>✈️ ประวัติการลา (Leave History)</option>
                        <option value="swap" <?= $report_type=='swap'?'selected':''; ?>>🔄 ประวัติการแลกเวร (Swap History)</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label text-muted">ตั้งแต่วันที่</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label text-muted">ถึงวันที่</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>

                <?php if ($is_super_admin): ?>
                <div class="col-md-2">
                    <label class="form-label text-muted">แผนก</label>
                    <select name="department_id" class="form-select">
                        <option value="all">ทุกแผนก</option>
                        <?php 
                        $d_q = mysqli_query($conn, "SELECT * FROM departments");
                        while($d = mysqli_fetch_assoc($d_q)): 
                        ?>
                            <option value="<?= $d['id'] ?>" <?= (isset($_GET['department_id']) && $_GET['department_id'] == $d['id']) ? 'selected' : '' ?>>
                                <?= $d['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table id="reportTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <?php if ($report_type == 'attendance'): ?>
                        <tr>
                            <th>วันที่</th>
                            <th>ชื่อ-สกุล</th>
                            <?php if($is_super_admin): ?><th>แผนก</th><?php endif; ?>
                            <th>กะงาน</th>
                            <th>เวลาเข้า</th>
                            <th>เวลาออก</th>
                            <th class="text-center">สถานะ</th>
                        </tr>
                    <?php elseif ($report_type == 'leave'): ?>
                        <tr>
                            <th>วันที่ลา</th>
                            <th>ชื่อ-สกุล</th>
                            <?php if($is_super_admin): ?><th>แผนก</th><?php endif; ?>
                            <th>ประเภท</th>
                            <th>จำนวนวัน</th>
                            <th>สถานะ</th>
                        </tr>
                    <?php elseif ($report_type == 'swap'): ?>
                        <tr>
                            <th>วันที่ทำรายการ</th>
                            <th>ผู้ขอ (Requester)</th>
                            <th>ผู้รับ (Target)</th>
                            <th>สถานะ</th>
                            <th>ผู้อนุมัติ</th>
                        </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php foreach($data as $row): ?>
                        <tr>
                            <?php if ($report_type == 'attendance'): ?>
                                <td><?= date('d/m/Y', strtotime($row['schedule_date'])) ?></td>
                                <td class="fw-bold"><?= $row['full_name'] ?></td>
                                <?php if($is_super_admin): ?><td><span class="badge bg-secondary"><?= $row['dept_name'] ?></span></td><?php endif; ?>
                                <td>
                                    <span class="badge-shift">
                                        <?= $shift_mapping[$row['shift_type']] ?? ucfirst($row['shift_type']) ?>
                                    </span>
                                </td>
                                <td class="text-success"><?= $row['check_in'] ? date('H:i', strtotime($row['check_in'])) : '-' ?></td>
                                <td class="text-danger"><?= $row['check_out'] ? date('H:i', strtotime($row['check_out'])) : '-' ?></td>
                                <td class="text-center">
                                    <?php if ($row['late_status']): ?>
                                        <span class="badge-status bg-danger text-white">สาย</span>
                                    <?php elseif ($row['check_in']): ?>
                                        <span class="badge-status bg-success text-white">ปกติ</span>
                                    <?php else: ?>
                                        <span class="badge-status bg-light text-muted">ขาด/ยังไม่ลง</span>
                                    <?php endif; ?>
                                </td>

                            <?php elseif ($report_type == 'leave'): ?>
                                <td>
                                    <?= date('d/m/y', strtotime($row['start_date'])) ?> - <?= date('d/m/y', strtotime($row['end_date'])) ?>
                                </td>
                                <td><?= $row['full_name'] ?></td>
                                <?php if($is_super_admin): ?><td><span class="badge bg-secondary"><?= $row['dept_name'] ?></span></td><?php endif; ?>
                                <td><?= $leave_type_mapping[$row['request_type']] ?? ucfirst($row['request_type']) ?></td>
                                <td>
                                    <?php 
                                        $d1 = new DateTime($row['start_date']);
                                        $d2 = new DateTime($row['end_date']);
                                        echo $d1->diff($d2)->days + 1 . " วัน";
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        $st = $row['status'];
                                        $status_color = match($st) { 'approved'=>'success', 'rejected'=>'danger', default=>'warning' };
                                        echo "<span class='badge bg-$status_color'>" . ($status_mapping[$st] ?? ucfirst($st)) . "</span>";
                                    ?>
                                </td>

                            <?php elseif ($report_type == 'swap'): ?>
                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td><?= $row['requester'] ?></td>
                                <td><?= $row['target'] ?></td>
                                <td>
                                    <?php 
                                        $st = $row['status'];
                                        $status_color = match($st) { 'approved'=>'success', 'rejected'=>'danger', default=>'warning' };
                                        echo "<span class='badge bg-$status_color'>" . ($status_mapping[$st] ?? ucfirst($st)) . "</span>";
                                    ?>
                                </td>
                                <td><?= $row['approver'] ?? '-' ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#reportTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'btn btn-success btn-sm btn-excel',
                title: 'Report_<?= $report_type ?>_<?= date("Ymd") ?>'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-file-pdf me-1"></i> Print / PDF',
                className: 'btn btn-danger btn-sm btn-pdf',
                title: 'รายงาน: <?= ucfirst($report_type) ?>',
                messageTop: 'ข้อมูลตั้งแต่วันที่ <?= date("d/m/Y", strtotime($start_date)) ?> ถึง <?= date("d/m/Y", strtotime($end_date)) ?>'
            }
        ],
        language: {
            search: "ค้นหา:",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            paginate: { first: "หน้าแรก", last: "หน้าสุดท้าย", next: "ถัดไป", previous: "ก่อนหน้า" },
            emptyTable: "ไม่พบข้อมูลในช่วงเวลาที่เลือก"
        },
        pageLength: 25
    });
});
</script>

</body>
</html>