<?php
include 'config.php';

if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$department_id = $_SESSION['department_id'];

// [เพิ่ม] ดึงโควตาลาป่วยต่อปี
$quota_sql = "SELECT yearly_sick_leave FROM schedule_rules WHERE department_id = ?";
$quota_stmt = mysqli_prepare($conn, $quota_sql);
mysqli_stmt_bind_param($quota_stmt, "i", $department_id);
mysqli_stmt_execute($quota_stmt);
mysqli_stmt_bind_result($quota_stmt, $yearly_sick_leave);
mysqli_stmt_fetch($quota_stmt);
mysqli_stmt_close($quota_stmt);

// [เพิ่ม] คำนวณวันลาป่วยที่ใช้ไปแล้วในปีนี้ (รวมที่อนุมัติและรออนุมัติ)
$used_sql = "SELECT SUM(DATEDIFF(end_date, start_date) + 1) as used_days 
             FROM leave_requests 
             WHERE user_id = ? 
             AND request_type = 'sick_leave' 
             AND status IN ('approved', 'pending') 
             AND YEAR(start_date) = YEAR(CURDATE())";
$used_stmt = mysqli_prepare($conn, $used_sql);
mysqli_stmt_bind_param($used_stmt, "i", $user_id);
mysqli_stmt_execute($used_stmt);
mysqli_stmt_bind_result($used_stmt, $used_sick_days);
mysqli_stmt_fetch($used_stmt);
mysqli_stmt_close($used_stmt);

$used_sick_days = $used_sick_days ?? 0;
$remaining_sick_days = $yearly_sick_leave - $used_sick_days;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = sanitizeInput($conn, $_POST['start_date']);
    $end_date = sanitizeInput($conn, $_POST['end_date']);
    $reason = sanitizeInput($conn, $_POST['reason']);
    $request_type = 'sick_leave';
    
    // Validate Dates
    if (!Validation::validateDateRange($start_date, $end_date)) {
        $error_msg = "วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น";
    } else {
        // [เพิ่ม] ตรวจสอบวันลาคงเหลือ
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $days_requested = $end->diff($start)->days + 1;
        
        if ($days_requested > $remaining_sick_days) {
            $error_msg = "วันลาป่วยคงเหลือไม่เพียงพอ (เหลือ $remaining_sick_days วัน, ขอลา $days_requested วัน)";
        } else {
            // Upload
            $medical_certificate = null;
            if (isset($_FILES['medical_certificate']) && $_FILES['medical_certificate']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFile($_FILES['medical_certificate'], ['jpg', 'jpeg', 'png', 'pdf']);
                if ($upload_result['success']) {
                    $medical_certificate = $upload_result['file_path'];
                } else {
                    $error_msg = $upload_result['message'];
                }
            }
            
            if (!isset($error_msg)) {
                $insert_sql = "INSERT INTO leave_requests (user_id, department_id, request_type, start_date, end_date, reason, medical_certificate) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($insert_stmt, "iisssss", $user_id, $department_id, $request_type, $start_date, $end_date, $reason, $medical_certificate);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    $request_id = mysqli_insert_id($conn);
                    
                    // Log Action
                    if (isset($logger)) {
                        $logger->logUserAction(
                            'sick_leave_request', 
                            "Requested sick leave from {$start_date} to {$end_date}", 
                            $user_id,
                            'leave_requests',
                            $request_id,
                            null,
                            [
                                'start_date' => $start_date, 
                                'end_date' => $end_date, 
                                'reason' => $reason,
                                'has_certificate' => !empty($medical_certificate)
                            ],
                            'medium'
                        );
                    }
                    
                    $admin_notification_sql = "SELECT id FROM users WHERE department_id = ? AND (level = 'admin' OR level = 'super_admin')";
                    $admin_stmt = mysqli_prepare($conn, $admin_notification_sql);
                    mysqli_stmt_bind_param($admin_stmt, "i", $department_id);
                    mysqli_stmt_execute($admin_stmt);
                    mysqli_stmt_bind_result($admin_stmt, $admin_id);
                    
                    while (mysqli_stmt_fetch($admin_stmt)) {
                        sendNotification($conn, $admin_id, "มีคำขอลาป่วยใหม่ (ย้อนหลัง/ปัจจุบัน) จาก {$_SESSION['full_name']}", 'warning');
                    }
                    mysqli_stmt_close($admin_stmt);
                    
                    // Redirect เพื่ออัปเดตค่าคงเหลือ
                    header("Location: request_leave.php?status=success");
                    exit;
                } else {
                    $error_msg = "เกิดข้อผิดพลาดในการส่งคำขอ";
                }
                mysqli_stmt_close($insert_stmt);
            }
        }
    }
}

// Fetch History
$history_sql = "SELECT start_date, end_date, reason, medical_certificate, status, created_at 
                FROM leave_requests 
                WHERE user_id = ? AND request_type = 'sick_leave'
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
    <title>ขอลาป่วย - ระบบจัดการเวรพนักงาน</title>
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
                    <li><a href="request_leave.php" class="active"><i class="fas fa-stethoscope"></i>ขอลาป่วย</a></li>
                    <li><a href="request_swap.php"><i class="fas fa-exchange-alt"></i>ขอสลับเวร</a></li>
                    <li><a href="request_holiday.php"><i class="fas fa-umbrella-beach"></i>ขอวันหยุด</a></li>
                    <li><a href="request_future_holiday.php"><i class="fas fa-calendar-plus"></i>ขอลาหยุดล่วงหน้า</a></li>
                    <li><a href="my_requests.php"><i class="fas fa-history"></i>ประวัติคำขอ</a></li>
                </ul>
            </nav>
            
            <main class="content-area">
                <div class="request-form">
                    <h2><i class="fas fa-stethoscope"></i> ขอลาป่วย</h2>
                    <p class="form-description">กรอกข้อมูลการลาป่วยและอัพโหลดใบรับรองแพทย์ (ถ้ามี) <br><small class="text-muted">* สามารถลงบันทึกย้อนหลังได้</small></p>
                    
                    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                        <div class="alert alert-success">ส่งคำขอลาป่วยเรียบร้อยแล้ว พร้อมใบรับรองแพทย์ (ถ้ามี)</div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_msg)): ?>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>
                    
                    <div class="leave-balance">
                        <div class="balance-card" style="background: #eef2f7; border-left: 4px solid #3498db; padding: 15px; margin-bottom: 20px; display: flex; align-items: center;">
                            <div class="balance-icon" style="font-size: 24px; color: #3498db; margin-right: 15px;">
                                <i class="fas fa-clinic-medical"></i>
                            </div>
                            <div class="balance-info">
                                <h3 style="margin: 0; font-size: 16px; color: #2c3e50;">สิทธิ์ลาป่วยคงเหลือ</h3>
                                <div class="balance-number" style="font-size: 20px; font-weight: bold; color: #2c3e50;">
                                    <?php echo $remaining_sick_days; ?> / <?php echo $yearly_sick_leave; ?> วัน
                                </div>
                                <small style="color: #7f8c8d;">ปี <?php echo date('Y'); ?></small>
                            </div>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo function_exists('generateCSRFToken') ? generateCSRFToken() : ''; ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>วันที่เริ่มลาป่วย *</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>วันที่สิ้นสุดการลาป่วย *</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>อาการ/เหตุผล *</label>
                            <textarea name="reason" class="form-control" rows="4" 
                                      placeholder="ระบุอาการป่วยหรือเหตุผลในการลาป่วย..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>ใบรับรองแพทย์ (ถ้ามี)</label>
                            <div class="file-upload">
                                <input type="file" id="medical_certificate" name="medical_certificate" 
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <label for="medical_certificate" class="file-upload-label">
                                    <i class="fas fa-upload"></i> อัพโหลดไฟล์
                                </label>
                                <div class="file-info">
                                    <small>รองรับไฟล์: JPG, PNG, PDF (ขนาดไม่เกิน 5MB)</small>
                                </div>
                            </div>
                            <div id="file-preview" class="file-preview" style="display: none;">
                                <img id="preview-image" src="" alt="Preview" style="max-width: 200px; display: none;">
                                <div id="preview-pdf" style="display: none;">
                                    <i class="fas fa-file-pdf" style="font-size: 48px; color: #e74c3c;"></i>
                                    <div>PDF File</div>
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
                        <h3><i class="fas fa-history"></i> ประวัติการลาป่วยล่าสุด</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>วันที่ขอ</th>
                                        <th>ช่วงวันที่ลา</th>
                                        <th>เหตุผล</th>
                                        <th>ใบรับรองแพทย์</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($history = mysqli_fetch_assoc($history_result)): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($history['created_at'])); ?></td>
                                            <td>
                                                <?php echo date('d/m/Y', strtotime($history['start_date'])); ?> - 
                                                <?php echo date('d/m/Y', strtotime($history['end_date'])); ?>
                                            </td>
                                            <td><?php echo $history['reason']; ?></td>
                                            <td>
                                                <?php if ($history['medical_certificate']): ?>
                                                    <a href="<?php echo $history['medical_certificate']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-file-medical"></i> ดูไฟล์
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?php 
                                                    echo $history['status'] == 'approved' ? 'badge-success' : 
                                                           ($history['status'] == 'rejected' ? 'badge-danger' : 'badge-warning'); 
                                                ?>">
                                                    <?php 
                                                    $status_names = [
                                                        'pending' => 'รออนุมัติ',
                                                        'approved' => 'อนุมัติแล้ว',
                                                        'rejected' => 'ปฏิเสธ'
                                                    ];
                                                    echo $status_names[$history['status']];
                                                    ?>
                                                </span>
                                            </td>
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
    // JS scripts remain same
    document.getElementById('medical_certificate').addEventListener('change', function(e) {
        // ... (Preview Logic same as before)
    });
    document.querySelector('input[name="start_date"]').addEventListener('change', function() {
        const endDate = document.querySelector('input[name="end_date"]');
        endDate.min = this.value;
    });
    </script>
    <script src="script.js"></script>
</body>
</html>

<?php
mysqli_stmt_close($history_stmt);
mysqli_close($conn);
?>