<?php
include 'config.php';

if (!isLoggedIn() || $_SESSION['user_level'] != 'admin') {
    header("location: login.php");
    exit;
}

$department_id = $_SESSION['department_id'];
$user_id = $_SESSION['user_id']; 

$success_msg = null;
$error_msg = null;

// --- [เพิ่ม] Helper Function แปลงชื่อเวรเป็นไทย ---
if (!function_exists('getShiftTypeThai')) {
    function getShiftTypeThai($shift) {
        $mapping = [
            'morning' => 'เช้า', 
            'afternoon' => 'บ่าย', 
            'night' => 'ดึก',
            'day' => 'Day', 
            'night_shift' => 'Night',
            'morning_afternoon' => 'เช้า-บ่าย', 
            'morning_night' => 'เช้า-ดึก', 
            'afternoon_night' => 'บ่าย-ดึก'
        ];
        return $mapping[$shift] ?? $shift;
    }
}

// อนุมัติ/ปฏิเสธ คำขอ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // **[1. ตรวจสอบ CSRF Token]**
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_msg = "เกิดข้อผิดพลาดด้านความปลอดภัย: CSRF Token ไม่ถูกต้อง กรุณาลองใหม่";
    } else {
        
        // --- [1] จัดการคำขอลา (leave_requests) ---
        if (isset($_POST['approve_leave']) || isset($_POST['reject_leave'])) {
            mysqli_begin_transaction($conn);
            try {
                $request_id = sanitizeInput($conn, $_POST['request_id']);
                $action = isset($_POST['approve_leave']) ? 'approved' : 'rejected';
                $notes = sanitizeInput($conn, $_POST['notes'] ?? '');
                
                $update_sql = "UPDATE leave_requests SET status = ?, approved_by = ?, notes = ?, updated_at = NOW() WHERE id = ? AND department_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                if (!$update_stmt) throw new Exception("SQL Prep Error (Leave Update): " . mysqli_error($conn));
                
                mysqli_stmt_bind_param($update_stmt, "ssiii", $action, $user_id, $notes, $request_id, $department_id);
                
                if (!mysqli_stmt_execute($update_stmt)) throw new Exception("SQL Execute Error (Leave Update): " . mysqli_stmt_error($update_stmt));
                
                // ดึงข้อมูลคำขอสำหรับบันทึก Log และส่งการแจ้งเตือน
                $request_sql = "SELECT user_id FROM leave_requests WHERE id = ?";
                $request_stmt = mysqli_prepare($conn, $request_sql);
                mysqli_stmt_bind_param($request_stmt, "i", $request_id);
                mysqli_stmt_execute($request_stmt);
                mysqli_stmt_bind_result($request_stmt, $requester_id);
                mysqli_stmt_fetch($request_stmt);
                mysqli_stmt_close($request_stmt);
                
                // บันทึก Log
                if (isset($logger)) {
                    $logger->logUserAction(
                        'leave_request_' . $action, 
                        "{$action} leave request ID: {$request_id}", 
                        $user_id,
                        'leave_requests',
                        $request_id,
                        ['status' => 'pending'],
                        ['status' => $action, 'notes' => $notes],
                        'medium'
                    );
                }
                
                // ส่งการแจ้งเตือนไปยังผู้ขอ
                $action_th = $action == 'approved' ? 'อนุมัติ' : 'ปฏิเสธ';
                if (function_exists('sendNotification')) {
                    sendNotification($conn, $requester_id, "คำขอลาของคุณได้รับการ{$action_th} โดยผู้ดูแล", $action == 'approved' ? 'success' : 'danger');
                }
                
                mysqli_commit($conn);
                $success_msg = "{$action_th} คำขอลาเรียบร้อยแล้ว";

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error_msg = "เกิดข้อผิดพลาดในการประมวลผลคำขอลา: " . $e->getMessage();
            }
        }
        
        // --- [2] จัดการคำขอสลับเวร (swap_requests) ---
        if (isset($_POST['approve_swap']) || isset($_POST['reject_swap'])) {
            mysqli_begin_transaction($conn);
            try {
                $request_id = sanitizeInput($conn, $_POST['request_id']);
                $action = isset($_POST['approve_swap']) ? 'approved' : 'rejected';
                $notes = sanitizeInput($conn, $_POST['notes'] ?? '');
                
                $update_sql = "UPDATE swap_requests SET status = ?, approved_by = ?, notes = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                if (!$update_stmt) throw new Exception("SQL Prep Error (Swap Update): " . mysqli_error($conn));
                
                mysqli_stmt_bind_param($update_stmt, "sssi", $action, $user_id, $notes, $request_id);
                
                if (!mysqli_stmt_execute($update_stmt)) throw new Exception("SQL Execute Error (Swap Update): " . mysqli_stmt_error($update_stmt));
                
                // ดึงข้อมูลคำขอ
                $request_sql = "SELECT user_id, target_user_id, original_schedule_id, target_schedule_id 
                               FROM swap_requests WHERE id = ?";
                $request_stmt = mysqli_prepare($conn, $request_sql);
                mysqli_stmt_bind_param($request_stmt, "i", $request_id);
                mysqli_stmt_execute($request_stmt);
                mysqli_stmt_bind_result($request_stmt, $requester_id, $target_user_id, $original_schedule_id, $target_schedule_id);
                mysqli_stmt_fetch($request_stmt);
                mysqli_stmt_close($request_stmt);
                
                if ($action == 'approved') {
                    // สลับเวรจริงในตาราง schedules
                    $swap1_sql = "UPDATE schedules SET user_id = ? WHERE id = ?";
                    $swap1_stmt = mysqli_prepare($conn, $swap1_sql);
                    mysqli_stmt_bind_param($swap1_stmt, "ii", $target_user_id, $original_schedule_id);
                    if (!mysqli_stmt_execute($swap1_stmt)) throw new Exception("SQL Execute Error (Swap 1): " . mysqli_stmt_error($swap1_stmt));
                    mysqli_stmt_close($swap1_stmt);
                    
                    $swap2_sql = "UPDATE schedules SET user_id = ? WHERE id = ?";
                    $swap2_stmt = mysqli_prepare($conn, $swap2_sql);
                    mysqli_stmt_bind_param($swap2_stmt, "ii", $requester_id, $target_schedule_id);
                    if (!mysqli_stmt_execute($swap2_stmt)) throw new Exception("SQL Execute Error (Swap 2): " . mysqli_stmt_error($swap2_stmt));
                    mysqli_stmt_close($swap2_stmt);
                    
                    // บันทึกประวัติการสลับเวร
                    $history_sql = "INSERT INTO swap_history (request_id, user1_id, user2_id, original_schedule_id, target_schedule_id, swap_date) 
                                   VALUES (?, ?, ?, ?, ?, CURDATE())";
                    $history_stmt = mysqli_prepare($conn, $history_sql);
                    mysqli_stmt_bind_param($history_stmt, "iiiii", $request_id, $requester_id, $target_user_id, $original_schedule_id, $target_schedule_id);
                    if (!mysqli_stmt_execute($history_stmt)) throw new Exception("SQL Execute Error (Swap History): " . mysqli_stmt_error($history_stmt));
                    mysqli_stmt_close($history_stmt);
                }
                
                // บันทึก Log
                if (isset($logger)) {
                    $logger->logUserAction(
                        'swap_request_' . $action, 
                        "{$action} swap request ID: {$request_id}", 
                        $user_id,
                        'swap_requests',
                        $request_id,
                        ['status' => 'pending'],
                        ['status' => $action, 'notes' => $notes],
                        'medium'
                    );
                }
                
                // ส่งการแจ้งเตือนไปยังผู้ใช้ทั้งสอง
                $action_th = $action == 'approved' ? 'อนุมัติ' : 'ปฏิเสธ';
                if (function_exists('sendNotification')) {
                    sendNotification($conn, $requester_id, "คำขอสลับเวรของคุณได้รับการ{$action_th} โดยผู้ดูแล", $action == 'approved' ? 'success' : 'danger');
                    sendNotification($conn, $target_user_id, "คำขอสลับเวรที่เกี่ยวข้องกับคุณได้รับการ{$action_th} โดยผู้ดูแล", $action == 'approved' ? 'success' : 'danger');
                }
                
                mysqli_commit($conn);
                $success_msg = "{$action_th} คำขอสลับเวรเรียบร้อยแล้ว";

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error_msg = "เกิดข้อผิดพลาดในการประมวลผลคำขอสลับเวร: " . $e->getMessage();
            }
        }
        
        // --- [3] จัดการคำขอลาล่วงหน้า (future_leave_requests) ---
        if (isset($_POST['approve_future_leave']) || isset($_POST['reject_future_leave'])) {
            mysqli_begin_transaction($conn);
            try {
                $request_id = sanitizeInput($conn, $_POST['request_id']);
                $action = isset($_POST['approve_future_leave']) ? 'approved' : 'rejected';
                $notes = sanitizeInput($conn, $_POST['notes'] ?? '');
                
                $update_sql = "UPDATE future_leave_requests SET status = ?, approved_by = ?, notes = ?, updated_at = NOW() WHERE id = ? AND department_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                if (!$update_stmt) throw new Exception("SQL Prep Error (Future Leave Update): " . mysqli_error($conn));
                
                mysqli_stmt_bind_param($update_stmt, "ssiii", $action, $user_id, $notes, $request_id, $department_id);
                
                if (!mysqli_stmt_execute($update_stmt)) throw new Exception("SQL Execute Error (Future Leave Update): " . mysqli_stmt_error($update_stmt));
                
                // ดึงข้อมูลคำขอ
                $request_sql = "SELECT user_id FROM future_leave_requests WHERE id = ?";
                $request_stmt = mysqli_prepare($conn, $request_sql);
                mysqli_stmt_bind_param($request_stmt, "i", $request_id);
                mysqli_stmt_execute($request_stmt);
                mysqli_stmt_bind_result($request_stmt, $requester_id);
                mysqli_stmt_fetch($request_stmt);
                mysqli_stmt_close($request_stmt);
                
                // บันทึก Log
                if (isset($logger)) {
                    $logger->logUserAction(
                        'future_leave_' . $action, 
                        "{$action} future leave request ID: {$request_id}", 
                        $user_id,
                        'future_leave_requests',
                        $request_id,
                        ['status' => 'pending'],
                        ['status' => $action, 'notes' => $notes],
                        'medium'
                    );
                }
                
                // ส่งการแจ้งเตือนไปยังผู้ขอ
                $action_th = $action == 'approved' ? 'อนุมัติ' : 'ปฏิเสธ';
                if (function_exists('sendNotification')) {
                    sendNotification($conn, $requester_id, "คำขอลาล่วงหน้าของคุณได้รับการ{$action_th} โดยผู้ดูแล", $action == 'approved' ? 'success' : 'danger');
                }
                
                mysqli_commit($conn);
                $success_msg = "{$action_th} คำขอลาล่วงหน้าเรียบร้อยแล้ว";

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error_msg = "เกิดข้อผิดพลาดในการประมวลผลคำขอลาล่วงหน้า: " . $e->getMessage();
            }
        }
    }
}

// **[เพิ่ม: การเปลี่ยนเส้นทางเมื่อดำเนินการสำเร็จ]**
if (isset($success_msg)) {
    header("Location: approve_requests.php?status=success");
    exit;
}

// **[ปรับ: แสดงผล Success message จาก URL]**
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $success_msg = "ดำเนินการสำเร็จเรียบร้อยแล้ว";
}

// ดึงคำขอที่รออนุมัติทั้งหมด
$pending_leave_sql = "SELECT lr.id, lr.user_id, u.full_name, lr.request_type, lr.start_date, lr.end_date, 
                             lr.reason, lr.medical_certificate, lr.created_at
                      FROM leave_requests lr
                      JOIN users u ON lr.user_id = u.id
                      WHERE lr.department_id = ? AND lr.status = 'pending'
                      ORDER BY lr.created_at DESC";
$pending_leave_stmt = mysqli_prepare($conn, $pending_leave_sql);
mysqli_stmt_bind_param($pending_leave_stmt, "i", $department_id);
mysqli_stmt_execute($pending_leave_stmt);
$pending_leave_result = mysqli_stmt_get_result($pending_leave_stmt);

$pending_swap_sql = "SELECT sr.id, sr.user_id, u1.full_name as requester_name, sr.target_user_id, 
                            u2.full_name as target_name, s1.schedule_date, s1.shift_type as original_shift,
                            s2.shift_type as target_shift, sr.reason, sr.created_at
                     FROM swap_requests sr
                     JOIN users u1 ON sr.user_id = u1.id
                     JOIN users u2 ON sr.target_user_id = u2.id
                     JOIN schedules s1 ON sr.original_schedule_id = s1.id
                     JOIN schedules s2 ON sr.target_schedule_id = s2.id
                     WHERE sr.status = 'pending'
                     AND (u1.department_id = ? OR u2.department_id = ?)
                     ORDER BY sr.created_at DESC";
$pending_swap_stmt = mysqli_prepare($conn, $pending_swap_sql);
mysqli_stmt_bind_param($pending_swap_stmt, "ii", $department_id, $department_id);
mysqli_stmt_execute($pending_swap_stmt);
$pending_swap_result = mysqli_stmt_get_result($pending_swap_stmt);

$pending_future_leave_sql = "SELECT flr.id, flr.user_id, u.full_name, flr.request_type, 
                                    flr.start_date, flr.end_date, flr.reason, flr.created_at
                             FROM future_leave_requests flr
                             JOIN users u ON flr.user_id = u.id
                             WHERE flr.department_id = ? AND flr.status = 'pending'
                             ORDER BY flr.created_at DESC";
$pending_future_leave_stmt = mysqli_prepare($conn, $pending_future_leave_sql);
mysqli_stmt_bind_param($pending_future_leave_stmt, "i", $department_id);
mysqli_stmt_execute($pending_future_leave_stmt);
$pending_future_leave_result = mysqli_stmt_get_result($pending_future_leave_stmt);

// ดึงข้อมูลแผนก
$dept_sql = "SELECT name FROM departments WHERE id = ?";
$dept_stmt = mysqli_prepare($conn, $dept_sql);
mysqli_stmt_bind_param($dept_stmt, "i", $department_id);
mysqli_stmt_execute($dept_stmt);
mysqli_stmt_bind_result($dept_stmt, $dept_name);
mysqli_stmt_fetch($dept_stmt);
mysqli_stmt_close($dept_stmt);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อนุมัติคำขอ - ระบบจัดการเวรพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS Badge Notification */
        .badge-notification {
            background-color: #ef4444; 
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            padding: 0 5px;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
            vertical-align: middle;
        }
        .tapbar-menu li a { display: flex; align-items: center; justify-content: flex-start; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?> (Admin)</span>
                <span>แผนก: <?php echo $dept_name; ?></span>
                <a href="admin_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="admin_dashboard.php">
                        <i class="fas fa-home"></i>แดชบอร์ด
                    </a></li>
                    <li><a href="user_management.php">
                        <i class="fas fa-users"></i>จัดการพนักงาน
                    </a></li>
                    <li><a href="schedule_rules.php">
                        <i class="fas fa-cog"></i>กำหนดระเบียบเวร
                    </a></li>
                    <li><a href="random_schedule.php">
                        <i class="fas fa-random"></i>สุ่มเวรอัตโนมัติ
                    </a></li>
                    <li><a href="approve_requests.php" class="active">
                        <i class="fas fa-check-circle"></i>อนุมัติคำขอ
                        <span id="adminPendingBadge" class="badge-notification" style="display:none;">0</span>
                    </a></li>
                    <li><a href="report_management.php">
                        <i class="fas fa-chart-bar"></i>รายงาน
                    </a></li>
                    <li>
                        <a href="admin_notifications.php">
                            <i class="fas fa-bell"></i> การแจ้งเตือน
                            <span id="adminNotificationBadge" class="badge-notification" style="display:none;">0</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <main class="content-area">
                <div class="page-header">
                    <h2><i class="fas fa-check-circle"></i> อนุมัติคำขอ</h2>
                    <p>จัดการคำขอต่างๆ จากพนักงานในแผนก<?php echo $dept_name; ?></p>
                </div>
                
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger">**ข้อผิดพลาด:** <?php echo $error_msg; ?></div>
                <?php endif; ?>
                
                <div class="quick-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <div class="stat-info">
                            <h3>ขอลาป่วย</h3>
                            <div class="stat-number"><?php echo mysqli_num_rows($pending_leave_result); ?> รายการ</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>ขอสลับเวร</h3>
                            <div class="stat-number"><?php echo mysqli_num_rows($pending_swap_result); ?> รายการ</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h3>ขอลาล่วงหน้า</h3>
                            <div class="stat-number"><?php echo mysqli_num_rows($pending_future_leave_result); ?> รายการ</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>รออนุมัติทั้งหมด</h3>
                            <div class="stat-number">
                                <?php echo mysqli_num_rows($pending_leave_result) + mysqli_num_rows($pending_swap_result) + mysqli_num_rows($pending_future_leave_result); ?> รายการ
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-stethoscope"></i> คำขอลาป่วย</h3>
                        <span class="badge badge-warning"><?php echo mysqli_num_rows($pending_leave_result); ?> รายการ</span>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($pending_leave_result) > 0): ?>
                            <div class="requests-grid">
                                <?php while($request = mysqli_fetch_assoc($pending_leave_result)): 
                                    $days = (new DateTime($request['start_date']))->diff(new DateTime($request['end_date']))->days + 1;
                                ?>
                                    <div class="request-card">
                                        <div class="request-header">
                                            <div class="requester-info">
                                                <h4><?php echo $request['full_name']; ?></h4>
                                                <span class="request-date">ขอเมื่อ: <?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?></span>
                                            </div>
                                            <div class="request-badge">
                                                <span class="badge badge-info"><?php echo $days; ?> วัน</span>
                                            </div>
                                        </div>
                                        
                                        <div class="request-details">
                                            <div class="detail-item">
                                                <label>ช่วงวันที่ลา:</label>
                                                <span>
                                                    <?php echo date('d/m/Y', strtotime($request['start_date'])); ?> - 
                                                    <?php echo date('d/m/Y', strtotime($request['end_date'])); ?>
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <label>เหตุผล:</label>
                                                <span><?php echo $request['reason']; ?></span>
                                            </div>
                                            <?php if ($request['medical_certificate']): ?>
                                            <div class="detail-item">
                                                <label>ใบรับรองแพทย์:</label>
                                                <a href="<?php echo $request['medical_certificate']; ?>" target="_blank" class="btn btn-sm btn-outline">
                                                    <i class="fas fa-file-medical"></i> ดูไฟล์
                                                </a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="request-actions">
                                            <form method="post" class="action-form">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                
                                                <div class="form-group">
                                                    <textarea name="notes" class="form-control" rows="2" placeholder="หมายเหตุ (ถ้ามี)"></textarea>
                                                </div>
                                                
                                                <div class="action-buttons">
                                                    <button type="submit" name="approve_leave" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> อนุมัติ
                                                    </button>
                                                    <button type="submit" name="reject_leave" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i> ปฏิเสธ
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle"></i>
                                <p>ไม่มีคำขอลาป่วยที่รออนุมัติ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-exchange-alt"></i> คำขอสลับเวร</h3>
                        <span class="badge badge-warning"><?php echo mysqli_num_rows($pending_swap_result); ?> รายการ</span>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($pending_swap_result) > 0): ?>
                            <div class="requests-grid">
                                <?php while($request = mysqli_fetch_assoc($pending_swap_result)): ?>
                                    <div class="request-card">
                                        <div class="request-header">
                                            <div class="requester-info">
                                                <h4><?php echo $request['requester_name']; ?></h4>
                                                <span class="request-date">ขอเมื่อ: <?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?></span>
                                            </div>
                                            <div class="request-badge">
                                                <span class="badge badge-info">สลับเวร</span>
                                            </div>
                                        </div>
                                        
                                        <div class="request-details">
                                            <div class="detail-item">
                                                <label>ต้องการสลับกับ:</label>
                                                <span><?php echo $request['target_name']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <label>วันที่:</label>
                                                <span><?php echo date('d/m/Y', strtotime($request['schedule_date'])); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <label>การสลับ:</label>
                                                <span class="shift-change">
                                                    <?php echo getShiftTypeThai($request['original_shift']); ?> 
                                                    <i class="fas fa-arrow-right"></i> 
                                                    <?php echo getShiftTypeThai($request['target_shift']); ?>
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <label>เหตุผล:</label>
                                                <span><?php echo $request['reason']; ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="request-actions">
                                            <form method="post" class="action-form">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                
                                                <div class="form-group">
                                                    <textarea name="notes" class="form-control" rows="2" placeholder="หมายเหตุ (ถ้ามี)"></textarea>
                                                </div>
                                                
                                                <div class="action-buttons">
                                                    <button type="submit" name="approve_swap" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> อนุมัติ
                                                    </button>
                                                    <button type="submit" name="reject_swap" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i> ปฏิเสธ
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle"></i>
                                <p>ไม่มีคำขอสลับเวรที่รออนุมัติ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-plus"></i> คำขอลาล่วงหน้า</h3>
                        <span class="badge badge-warning"><?php echo mysqli_num_rows($pending_future_leave_result); ?> รายการ</span>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($pending_future_leave_result) > 0): ?>
                            <div class="requests-grid">
                                <?php while($request = mysqli_fetch_assoc($pending_future_leave_result)): 
                                    $days = (new DateTime($request['start_date']))->diff(new DateTime($request['end_date']))->days + 1;
                                    $type_names = [
                                        'vacation' => 'พักร้อน',
                                        'personal' => 'ลากิจ',
                                        'sick' => 'ลาป่วย'
                                    ];
                                ?>
                                    <div class="request-card">
                                        <div class="request-header">
                                            <div class="requester-info">
                                                <h4><?php echo $request['full_name']; ?></h4>
                                                <span class="request-date">ขอเมื่อ: <?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?></span>
                                            </div>
                                            <div class="request-badge">
                                                <span class="badge badge-info"><?php echo $days; ?> วัน</span>
                                                <span class="badge badge-secondary"><?php echo $type_names[$request['request_type']]; ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="request-details">
                                            <div class="detail-item">
                                                <label>ประเภท:</label>
                                                <span><?php echo $type_names[$request['request_type']]; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <label>ช่วงวันที่ลา:</label>
                                                <span>
                                                    <?php echo date('d/m/Y', strtotime($request['start_date'])); ?> - 
                                                    <?php echo date('d/m/Y', strtotime($request['end_date'])); ?>
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <label>เหตุผล:</label>
                                                <span><?php echo $request['reason']; ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="request-actions">
                                            <form method="post" class="action-form">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                
                                                <div class="form-group">
                                                    <textarea name="notes" class="form-control" rows="2" placeholder="หมายเหตุ (ถ้ามี)"></textarea>
                                                </div>
                                                
                                                <div class="action-buttons">
                                                    <button type="submit" name="approve_future_leave" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> อนุมัติ
                                                    </button>
                                                    <button type="submit" name="reject_future_leave" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i> ปฏิเสธ
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-check-circle"></i>
                                <p>ไม่มีคำขอลาล่วงหน้าที่รออนุมัติ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
    // ฟังก์ชันอัปเดตตัวเลขคำขอรออนุมัติ
    function updateAdminBadge() {
        fetch('api/get_pending_requests_count.php')
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('adminPendingBadge');
                if (badge) {
                    if (data.count > 0) {
                        badge.innerText = data.count;
                        badge.style.display = 'inline-flex'; 
                    } else {
                        badge.style.display = 'none'; 
                    }
                }
            })
            .catch(err => console.error(err));
    }

    // ฟังก์ชันอัปเดตตัวเลขการแจ้งเตือน
    function updateNotificationBadge() {
        fetch('api/get_unread_notification_count.php')
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('adminNotificationBadge');
                if (badge) {
                    if (data.count > 0) {
                        badge.innerText = data.count;
                        badge.style.display = 'inline-flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(err => console.error(err));
    }

    // สั่งให้ทำงานทันทีเมื่อเปิดหน้าเว็บ + ทำซ้ำทุก 3 วินาที
    document.addEventListener('DOMContentLoaded', function() {
        updateAdminBadge();         
        updateNotificationBadge();  
        
        setInterval(updateAdminBadge, 3000);        
        setInterval(updateNotificationBadge, 3000); 
    });
    </script>
</body>
</html>

<?php
mysqli_stmt_close($pending_leave_stmt);
mysqli_stmt_close($pending_swap_stmt);
mysqli_stmt_close($pending_future_leave_stmt);
mysqli_close($conn);
?>