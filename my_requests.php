<?php
include 'config.php';

if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$department_id = $_SESSION['department_id'];

// --- จัดการการยกเลิกคำขอ (Cancel Request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_request'])) {
    $req_id = filter_input(INPUT_POST, 'req_id', FILTER_SANITIZE_NUMBER_INT);
    
    // ** แก้ไขตรงนี้: เปลี่ยน FILTER_SANITIZE_STRING เป็น FILTER_SANITIZE_FULL_SPECIAL_CHARS **
    $req_type = filter_input(INPUT_POST, 'req_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 

    if ($req_id && $req_type) {
        $table = '';
        if ($req_type === 'leave') $table = 'leave_requests';
        elseif ($req_type === 'future') $table = 'future_leave_requests';
        elseif ($req_type === 'swap') $table = 'swap_requests';

        if ($table) {
            // ลบได้เฉพาะสถานะ pending และเป็นของตัวเองเท่านั้น
            $del_sql = "DELETE FROM $table WHERE id = ? AND user_id = ? AND status = 'pending'";
            $del_stmt = mysqli_prepare($conn, $del_sql);
            mysqli_stmt_bind_param($del_stmt, "ii", $req_id, $user_id);
            if (mysqli_stmt_execute($del_stmt)) {
                $msg = "ยกเลิกคำขอเรียบร้อยแล้ว";
                $msg_type = "success";
            } else {
                $msg = "ไม่สามารถยกเลิกคำขอได้ (อาจได้รับการอนุมัติไปแล้ว)";
                $msg_type = "danger";
            }
            mysqli_stmt_close($del_stmt);
        }
    }
}

// --- ดึงข้อมูลประวัติการลา (รวมป่วย/กิจ/พักร้อน/ล่วงหน้า) ---
$leave_sql = "
    (SELECT id, 'leave' as source, request_type, start_date, end_date, reason, status, created_at, approved_by 
     FROM leave_requests 
     WHERE user_id = ?)
    UNION ALL
    (SELECT id, 'future' as source, request_type, start_date, end_date, reason, status, created_at, approved_by 
     FROM future_leave_requests 
     WHERE user_id = ?)
    ORDER BY created_at DESC";
$leave_stmt = mysqli_prepare($conn, $leave_sql);
mysqli_stmt_bind_param($leave_stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($leave_stmt);
$leave_result = mysqli_stmt_get_result($leave_stmt);

// --- ดึงข้อมูลประวัติการสลับเวร ---
$swap_sql = "SELECT sr.id, sr.status, sr.created_at, sr.reason, sr.approved_by,
                    u.full_name as target_name,
                    s1.schedule_date as my_date, s1.shift_type as my_shift,
                    s2.schedule_date as target_date, s2.shift_type as target_shift
             FROM swap_requests sr
             JOIN users u ON sr.target_user_id = u.id
             JOIN schedules s1 ON sr.original_schedule_id = s1.id
             JOIN schedules s2 ON sr.target_schedule_id = s2.id
             WHERE sr.user_id = ?
             ORDER BY sr.created_at DESC";
$swap_stmt = mysqli_prepare($conn, $swap_sql);
mysqli_stmt_bind_param($swap_stmt, "i", $user_id);
mysqli_stmt_execute($swap_stmt);
$swap_result = mysqli_stmt_get_result($swap_stmt);

// Helper Function: แปลงสถานะเป็น Badge
function getStatusBadge($status) {
    switch ($status) {
        case 'pending': return '<span class="badge badge-warning">รออนุมัติ</span>';
        case 'approved': return '<span class="badge badge-success">อนุมัติแล้ว</span>';
        case 'rejected': return '<span class="badge badge-danger">ถูกปฏิเสธ</span>';
        default: return '<span class="badge badge-secondary">'.$status.'</span>';
    }
}

// Helper Function: แปลงกะเป็นไทย
function getShiftThai($shift) {
    $map = ['morning'=>'เช้า', 'afternoon'=>'บ่าย', 'night'=>'ดึก', 'day'=>'Day', 'night_shift'=>'Night'];
    return $map[$shift] ?? $shift;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติคำขอ</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .request-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 15px;
            border-left: 5px solid #ccc;
            padding: 15px;
            transition: transform 0.2s;
        }
        .request-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        
        .request-card.status-pending { border-left-color: #f6c23e; }
        .request-card.status-approved { border-left-color: #1cc88a; }
        .request-card.status-rejected { border-left-color: #e74a3b; }

        .req-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .req-title { font-weight: bold; font-size: 1.1rem; color: #4e73df; }
        .req-date { font-size: 0.85rem; color: #858796; }
        .req-body { font-size: 0.95rem; color: #5a5c69; line-height: 1.6; }
        .req-footer { margin-top: 10px; padding-top: 10px; border-top: 1px solid #eaecf4; display: flex; justify-content: space-between; align-items: center; }

        .nav-tabs { display: flex; border-bottom: 2px solid #eaecf4; margin-bottom: 20px; }
        .nav-item { padding: 10px 20px; cursor: pointer; color: #858796; font-weight: 600; border-bottom: 2px solid transparent; margin-bottom: -2px; }
        .nav-item.active { color: #4e73df; border-bottom-color: #4e73df; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="btn btn-secondary btn-sm"><i class="fas fa-sign-out-alt"></i> ออก</a>
            </div>
        </header>

        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="user_dashboard.php"><i class="fas fa-home"></i>แดชบอร์ด</a></li>
                    <li><a href="request_leave.php"><i class="fas fa-stethoscope"></i>ขอลาป่วย</a></li>
                    <li><a href="request_swap.php"><i class="fas fa-exchange-alt"></i>ขอสลับเวร</a></li>
                    <li><a href="request_holiday.php"><i class="fas fa-umbrella-beach"></i>ขอวันหยุด</a></li>
                    <li><a href="request_future_holiday.php"><i class="fas fa-calendar-plus"></i>ขอลาหยุดล่วงหน้า</a></li>
                    <li><a href="my_requests.php" class="active"><i class="fas fa-history"></i>ประวัติคำขอ</a></li>
                </ul>
            </nav>

            <main class="content-area">
                <div class="page-header">
                    <h2><i class="fas fa-history"></i> ประวัติคำขอของคุณ</h2>
                </div>

                <?php if (isset($msg)): ?>
                    <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
                <?php endif; ?>

                <div class="nav-tabs">
                    <div class="nav-item active" onclick="switchTab('tab-leave', this)">
                        <i class="fas fa-file-medical"></i> ประวัติการลา
                    </div>
                    <div class="nav-item" onclick="switchTab('tab-swap', this)">
                        <i class="fas fa-exchange-alt"></i> ประวัติสลับเวร
                    </div>
                </div>

                <div id="tab-leave" class="tab-pane active">
                    <?php if (mysqli_num_rows($leave_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($leave_result)): ?>
                            <div class="request-card status-<?php echo $row['status']; ?>">
                                <div class="req-header">
                                    <div>
                                        <div class="req-title">
                                            <?php 
                                            $icon = ($row['request_type'] == 'sick') ? 'fa-procedures' : 'fa-suitcase';
                                            $type_th = '';
                                            if($row['request_type'] == 'sick') $type_th = 'ลาป่วย';
                                            elseif($row['request_type'] == 'vacation') $type_th = 'ลาพักร้อน';
                                            elseif($row['request_type'] == 'personal') $type_th = 'ลากิจ';
                                            else $type_th = $row['request_type'];
                                            echo "<i class='fas $icon'></i> ขอ$type_th";
                                            ?>
                                        </div>
                                        <div class="req-date"><i class="far fa-clock"></i> ยื่นเมื่อ: <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></div>
                                    </div>
                                    <div><?php echo getStatusBadge($row['status']); ?></div>
                                </div>
                                <div class="req-body">
                                    <p><strong>วันที่ลา:</strong> <?php echo date('d/m/Y', strtotime($row['start_date'])); ?> ถึง <?php echo date('d/m/Y', strtotime($row['end_date'])); ?></p>
                                    <p><strong>เหตุผล:</strong> <?php echo $row['reason'] ? $row['reason'] : '-'; ?></p>
                                </div>
                                <?php if ($row['status'] == 'pending'): ?>
                                <div class="req-footer">
                                    <small class="text-muted">รอผู้ดูแลอนุมัติ</small>
                                    <form method="post" onsubmit="return confirm('ยืนยันที่จะยกเลิกคำขอนี้?');">
                                        <input type="hidden" name="req_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="req_type" value="<?php echo $row['source']; ?>">
                                        <button type="submit" name="cancel_request" class="btn btn-danger btn-sm">ยกเลิก</button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">ไม่มีประวัติการลา</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="tab-swap" class="tab-pane">
                    <?php if (mysqli_num_rows($swap_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($swap_result)): ?>
                            <div class="request-card status-<?php echo $row['status']; ?>">
                                <div class="req-header">
                                    <div>
                                        <div class="req-title"><i class="fas fa-exchange-alt"></i> ขอสลับเวร</div>
                                        <div class="req-date"><i class="far fa-clock"></i> ยื่นเมื่อ: <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></div>
                                    </div>
                                    <div><?php echo getStatusBadge($row['status']); ?></div>
                                </div>
                                <div class="req-body">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                        <div style="text-align: center;">
                                            <small>เวรของคุณ</small><br>
                                            <strong class="text-danger"><?php echo date('d/m/y', strtotime($row['my_date'])); ?></strong><br>
                                            <span class="badge badge-secondary"><?php echo getShiftThai($row['my_shift']); ?></span>
                                        </div>
                                        <i class="fas fa-arrow-right text-muted"></i>
                                        <div style="text-align: center;">
                                            <small>แลกกับ <?php echo $row['target_name']; ?></small><br>
                                            <strong class="text-success"><?php echo date('d/m/y', strtotime($row['target_date'])); ?></strong><br>
                                            <span class="badge badge-secondary"><?php echo getShiftThai($row['target_shift']); ?></span>
                                        </div>
                                    </div>
                                    <p><strong>เหตุผล:</strong> <?php echo $row['reason'] ? $row['reason'] : '-'; ?></p>
                                </div>
                                <?php if ($row['status'] == 'pending'): ?>
                                <div class="req-footer">
                                    <small class="text-muted">รอเพื่อนร่วมงานอนุมัติ</small>
                                    <form method="post" onsubmit="return confirm('ยืนยันที่จะยกเลิกคำขอนี้?');">
                                        <input type="hidden" name="req_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="req_type" value="swap">
                                        <button type="submit" name="cancel_request" class="btn btn-danger btn-sm">ยกเลิก</button>
                                    </form>
                                </div>
                                <?php elseif ($row['status'] == 'approved'): ?>
                                    <div class="req-footer">
                                        <small class="text-success"><i class="fas fa-check-circle"></i> อนุมัติแล้ว</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">ไม่มีประวัติการสลับเวร</p>
                        </div>
                    <?php endif; ?>
                </div>

            </main>
        </div>
    </div>

    <script>
    function switchTab(tabId, navItem) {
        // ซ่อนทุก Tab
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
        
        // แสดง Tab ที่เลือก
        document.getElementById(tabId).classList.add('active');
        navItem.classList.add('active');
    }
    </script>
</body>
</html>
<?php
mysqli_stmt_close($leave_stmt);
mysqli_stmt_close($swap_stmt);
mysqli_close($conn);
?>