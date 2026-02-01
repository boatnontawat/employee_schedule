<?php
include 'config.php';

if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$department_id = $_SESSION['department_id'];

// ดึงข้อมูลการตั้งค่าวันหยุด
$leave_settings_sql = "SELECT yearly_sick_leave, yearly_personal_leave, yearly_vacation_leave, max_concurrent_leave FROM schedule_rules WHERE department_id = ?";
$leave_stmt = mysqli_prepare($conn, $leave_settings_sql);
mysqli_stmt_bind_param($leave_stmt, "i", $department_id);
mysqli_stmt_execute($leave_stmt);
mysqli_stmt_bind_result($leave_stmt, $yearly_sick, $yearly_personal, $yearly_vacation, $max_concurrent_leave);
mysqli_stmt_fetch($leave_stmt);
mysqli_stmt_close($leave_stmt);

// คำนวณวันหยุดที่ใช้ไปแล้ว
$used_leave_sql = "SELECT request_type, SUM(DATEDIFF(end_date, start_date) + 1) as used_days 
                   FROM future_leave_requests 
                   WHERE user_id = ? 
                   AND status IN ('approved', 'pending') 
                   AND YEAR(start_date) = YEAR(CURDATE())
                   GROUP BY request_type";
$used_leave_stmt = mysqli_prepare($conn, $used_leave_sql);
mysqli_stmt_bind_param($used_leave_stmt, "i", $user_id);
mysqli_stmt_execute($used_leave_stmt);
$used_result = mysqli_stmt_get_result($used_leave_stmt);

$used_data = [
    'vacation' => 0,
    'personal' => 0,
    'sick' => 0
];

while ($row = mysqli_fetch_assoc($used_result)) {
    if (isset($used_data[$row['request_type']])) {
        $used_data[$row['request_type']] = $row['used_days'];
    }
}
mysqli_stmt_close($used_leave_stmt);

// คำนวณวันคงเหลือ
$remaining = [
    'vacation' => $yearly_vacation - $used_data['vacation'],
    'personal' => $yearly_personal - $used_data['personal'],
    'sick' => $yearly_sick - $used_data['sick']
];

// ส่งค่าไปใช้ใน JavaScript
$json_limits = json_encode([
    'vacation' => $remaining['vacation'],
    'personal' => $remaining['personal'],
    'sick' => $remaining['sick'],
    'max_vacation' => $yearly_vacation,
    'max_personal' => $yearly_personal,
    'max_sick' => $yearly_sick
]);

$error_msg = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = sanitizeInput($conn, $_POST['start_date']);
    $end_date = sanitizeInput($conn, $_POST['end_date']);
    $reason = sanitizeInput($conn, $_POST['reason']);
    $request_type = sanitizeInput($conn, $_POST['request_type']);
    
    // ป้องกันการแอบส่งค่า sick เข้ามา
    if ($request_type == 'sick') {
        $error_msg = "กรุณาใช้เมนู 'ขอลาป่วย' สำหรับการลาป่วย";
    }
    // ตรวจสอบวันที่พื้นฐาน
    elseif (!Validation::validateDateRange($start_date, $end_date)) {
        $error_msg = "วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น";
    } else {
        $max_future_date = date('Y-m-d', strtotime('+1 year'));
        if ($start_date > $max_future_date) {
            $error_msg = "ไม่สามารถขอลาหยุดล่วงหน้าเกิน 1 ปี";
        } else {
            
            // [Check Overlap] ตรวจสอบการลาซ้ำซ้อน
            $overlap_sql = "SELECT start_date, end_date, request_type FROM future_leave_requests 
                            WHERE user_id = ? 
                            AND status IN ('approved', 'pending') 
                            AND (start_date <= ? AND end_date >= ?)";
            
            $overlap_stmt = mysqli_prepare($conn, $overlap_sql);
            mysqli_stmt_bind_param($overlap_stmt, "iss", $user_id, $end_date, $start_date);
            mysqli_stmt_execute($overlap_stmt);
            mysqli_stmt_store_result($overlap_stmt);
            
            if (mysqli_stmt_num_rows($overlap_stmt) > 0) {
                // ถ้าเจอข้อมูลซ้ำ
                $ol_start = null; $ol_end = null; $ol_type = null;
                mysqli_stmt_bind_result($overlap_stmt, $ol_start, $ol_end, $ol_type);
                mysqli_stmt_fetch($overlap_stmt);
                
                // แปลงเป็น string ก่อนส่งเข้า strtotime เพื่อป้องกัน Error
                $ol_start_th = date('d/m/Y', strtotime((string)$ol_start));
                $ol_end_th = date('d/m/Y', strtotime((string)$ol_end));
                
                $error_msg = "คุณมีรายการลาในช่วงวันที่ $ol_start_th - $ol_end_th อยู่แล้ว ไม่สามารถขอลาซ้ำซ้อนได้";
            } 
            else {
                // ถ้าไม่ซ้ำ ให้ทำขั้นตอนเดิมต่อ
                
                $start = new DateTime($start_date);
                $end = new DateTime($end_date);
                $days_requested = $end->diff($start)->days + 1;
                
                // ตรวจสอบโควตาตามประเภทที่เลือก
                $current_remaining = $remaining[$request_type] ?? 0;
                
                if ($days_requested > $current_remaining) {
                    $type_labels = ['vacation' => 'ลาพักร้อน', 'personal' => 'ลากิจ'];
                    $label = $type_labels[$request_type] ?? $request_type;
                    $error_msg = "วัน{$label}คงเหลือไม่เพียงพอ (เหลือ {$current_remaining} วัน แต่ขอ {$days_requested} วัน)";
                } else {
                    // เช็คว่ามีเพื่อนร่วมงานลาพร้อมกันเกินกำหนดหรือไม่
                    $concurrent_check_sql = "SELECT COUNT(DISTINCT user_id) as user_count 
                                            FROM future_leave_requests 
                                            WHERE department_id = ? 
                                            AND status = 'approved' 
                                            AND start_date <= ? AND end_date >= ?";
                    
                    $concurrent_stmt = mysqli_prepare($conn, $concurrent_check_sql);
                    mysqli_stmt_bind_param($concurrent_stmt, "iss", $department_id, $end_date, $start_date);
                    mysqli_stmt_execute($concurrent_stmt);
                    mysqli_stmt_bind_result($concurrent_stmt, $concurrent_count);
                    mysqli_stmt_fetch($concurrent_stmt);
                    mysqli_stmt_close($concurrent_stmt);
                    
                    if ($concurrent_count >= $max_concurrent_leave) {
                        $error_msg = "มีผู้ลาพร้อมกันครบจำนวนสูงสุด ({$max_concurrent_leave} คน) ในช่วงวันที่เลือก";
                    } else {
                        $insert_sql = "INSERT INTO future_leave_requests (user_id, department_id, request_type, start_date, end_date, reason) 
                                       VALUES (?, ?, ?, ?, ?, ?)";
                        $insert_stmt = mysqli_prepare($conn, $insert_sql);
                        mysqli_stmt_bind_param($insert_stmt, "iissss", $user_id, $department_id, $request_type, $start_date, $end_date, $reason);
                        
                        if (mysqli_stmt_execute($insert_stmt)) {
                            $request_id = mysqli_insert_id($conn);
                            
                            // Log and Notify
                            $logger->logUserAction(
                                'future_leave_request', 
                                "Requested future leave from {$start_date} to {$end_date} ({$request_type})", 
                                $user_id,
                                'future_leave_requests',
                                $request_id,
                                null,
                                ['start_date' => $start_date, 'end_date' => $end_date, 'request_type' => $request_type, 'days' => $days_requested],
                                'medium'
                            );
                            
                            // Alert Admin
                            $admin_notification_sql = "SELECT id FROM users WHERE department_id = ? AND level = 'admin'";
                            $admin_stmt = mysqli_prepare($conn, $admin_notification_sql);
                            mysqli_stmt_bind_param($admin_stmt, "i", $department_id);
                            mysqli_stmt_execute($admin_stmt);
                            mysqli_stmt_bind_result($admin_stmt, $admin_id);
                            $admin_ids = [];
                            while (mysqli_stmt_fetch($admin_stmt)) {
                                $admin_ids[] = $admin_id;
                            }
                            mysqli_stmt_close($admin_stmt);

                            foreach ($admin_ids as $aid) {
                                sendNotification($conn, $aid, "มีคำขอลาหยุดล่วงหน้าใหม่จาก {$_SESSION['full_name']} ({$days_requested} วัน)", 'warning');
                            }
                            
                            header("Location: request_future_holiday.php?status=success&days={$days_requested}");
                            exit;

                        } else {
                            $error_msg = "เกิดข้อผิดพลาดในการส่งคำขอ";
                        }
                        mysqli_stmt_close($insert_stmt);
                    }
                }
            }
            mysqli_stmt_close($overlap_stmt); 
        }
    }
}

// Fetch History
$history_sql = "SELECT start_date, end_date, request_type, reason, status, created_at 
                FROM future_leave_requests 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 10";
$history_stmt = mysqli_prepare($conn, $history_sql);
mysqli_stmt_bind_param($history_stmt, "i", $user_id);
mysqli_stmt_execute($history_stmt);
$history_result = mysqli_stmt_get_result($history_stmt);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ขอลาหยุดล่วงหน้า - ระบบจัดการเวรพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?></span>
                <a href="user_dashboard.php" class="btn btn-secondary">กลับสู่แดชบอร์ด</a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="user_dashboard.php"><i class="fas fa-home"></i>แดชบอร์ด</a></li>
                    <li><a href="request_leave.php"><i class="fas fa-stethoscope"></i>ขอลาป่วย</a></li>
                    <li><a href="request_swap.php"><i class="fas fa-exchange-alt"></i>ขอสลับเวร</a></li>
                    <li><a href="request_holiday.php"><i class="fas fa-umbrella-beach"></i>ขอวันหยุด</a></li>
                    <li><a href="request_future_holiday.php" class="active"><i class="fas fa-calendar-plus"></i>ขอลาหยุดล่วงหน้า</a></li>
                    <li><a href="my_requests.php"><i class="fas fa-history"></i>ประวัติคำขอ</a></li>
                </ul>
            </nav>
            
            <main class="content-area">
                <div class="request-form">
                    <h2><i class="fas fa-calendar-plus"></i> ขอลาหยุดล่วงหน้า</h2>
                    <p class="form-description">วางแผนวันหยุดล่วงหน้าได้สูงสุด 1 ปี</p>
                    
                    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                        <div class="alert alert-success">ส่งคำขอลาหยุดล่วงหน้าเรียบร้อยแล้ว (<?php echo $_GET['days'] ?? 0; ?> วัน)</div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_msg)): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                alert("<?php echo str_replace('"', '\"', $error_msg); ?>");
                            });
                        </script>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>
                    
                    <div class="leave-balance">
                        <div class="balance-card">
                            <div class="balance-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="balance-info">
                                <h3>วันหยุดคงเหลือ (<span id="type-label">ลาพักร้อน</span>)</h3>
                                <div class="balance-number"><span id="remaining-display"><?php echo $remaining['vacation']; ?></span> วัน</div>
                                <small>จากโควตา <span id="quota-display"><?php echo $yearly_vacation; ?></span> วัน/ปี</small>
                            </div>
                        </div>
                    </div>
                    
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>ประเภทการลา *</label>
                                <select name="request_type" id="request_type" class="form-control" required onchange="updateBalanceDisplay()">
                                    <option value="vacation">ลาพักร้อน</option>
                                    <option value="personal">ลากิจ</option>
                                </select>
                            </div>
                            </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>วันที่เริ่มลา *</label>
                                <input type="date" name="start_date" class="form-control" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                                       max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>วันที่สิ้นสุดการลา *</label>
                                <input type="date" name="end_date" class="form-control" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                                       max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>เหตุผล *</label>
                            <textarea name="reason" class="form-control" rows="4" 
                                      placeholder="ระบุเหตุผลในการลาล่วงหน้า..." required></textarea>
                        </div>
                        
                        <div id="date-info" class="date-info" style="display: none;">
                            <h4><i class="fas fa-calculator"></i> รายละเอียดการลา</h4>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>จำนวนวันลา:</label>
                                    <span id="info-days">0 วัน</span>
                                </div>
                                <div class="info-item">
                                    <label>วันหยุดคงเหลือหลังลา:</label>
                                    <span id="info-remaining">- วัน</span>
                                </div>
                                <div class="info-item">
                                    <label>ช่วงวันที่:</label>
                                    <span id="info-period">-</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> ส่งคำขอ
                            </button>
                            <a href="user_dashboard.php" class="btn btn-secondary">ยกเลิก</a>
                        </div>
                    </form>
                </div>
                
                <div class="card">
                     <div class="card-header">
                        <h3><i class="fas fa-history"></i> ประวัติคำขอลาล่วงหน้า</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>วันที่ขอ</th>
                                        <th>ประเภท</th>
                                        <th>ช่วงวันที่ลา</th>
                                        <th>จำนวนวัน</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($history = mysqli_fetch_assoc($history_result)): 
                                        $days = (new DateTime($history['start_date']))->diff(new DateTime($history['end_date']))->days + 1;
                                    ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($history['created_at'])); ?></td>
                                            <td>
                                                <?php 
                                                $type_names = ['vacation' => 'พักร้อน', 'personal' => 'ลากิจ', 'sick' => 'ลาป่วย'];
                                                echo $type_names[$history['request_type']] ?? $history['request_type'];
                                                ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($history['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($history['end_date'])); ?></td>
                                            <td><?php echo $days; ?> วัน</td>
                                            <td><span class="badge badge-<?php echo $history['status']=='approved'?'success':($history['status']=='rejected'?'danger':'warning'); ?>"><?php echo $history['status']; ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
    const leaveLimits = <?php echo $json_limits; ?>;
    
    function updateBalanceDisplay() {
        const type = document.getElementById('request_type').value;
        const remaining = leaveLimits[type];
        const max = leaveLimits['max_' + type];
        
        const labels = {'vacation': 'ลาพักร้อน', 'personal': 'ลากิจ'};
        document.getElementById('type-label').textContent = labels[type];
        document.getElementById('remaining-display').textContent = remaining;
        document.getElementById('quota-display').textContent = max;
        
        calculateLeaveDays();
    }

    function calculateLeaveDays() {
        const startDate = new Date(document.querySelector('input[name="start_date"]').value);
        const endDate = new Date(document.querySelector('input[name="end_date"]').value);
        const type = document.getElementById('request_type').value;
        const currentRemaining = leaveLimits[type];
        
        if (startDate && endDate && startDate <= endDate) {
            const timeDiff = endDate.getTime() - startDate.getTime();
            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
            
            document.getElementById('date-info').style.display = 'block';
            document.getElementById('info-days').textContent = daysDiff + ' วัน';
            document.getElementById('info-remaining').textContent = (currentRemaining - daysDiff) + ' วัน';
            document.getElementById('info-period').textContent = 
                startDate.toLocaleDateString('th-TH') + ' - ' + endDate.toLocaleDateString('th-TH');
                
            if (daysDiff > currentRemaining) {
                document.getElementById('info-remaining').style.color = 'red';
            } else {
                document.getElementById('info-remaining').style.color = 'inherit';
            }
        } else {
            document.getElementById('date-info').style.display = 'none';
        }
    }
    
    document.querySelector('input[name="start_date"]').addEventListener('change', function() {
        const endDate = document.querySelector('input[name="end_date"]');
        endDate.min = this.value;
        calculateLeaveDays();
    });
    
    document.querySelector('input[name="end_date"]').addEventListener('change', calculateLeaveDays);
    
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const maxDate = new Date();
        maxDate.setFullYear(today.getFullYear() + 1);
        const maxDateString = maxDate.toISOString().split('T')[0];
        document.querySelector('input[name="start_date"]').max = maxDateString;
        document.querySelector('input[name="end_date"]').max = maxDateString;
        
        updateBalanceDisplay();
    });
    </script>
    <script src="script.js"></script>
</body>
</html>
<?php
mysqli_stmt_close($history_stmt);
mysqli_close($conn);
?>