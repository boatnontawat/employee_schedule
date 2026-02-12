<?php
include 'config.php';

// ถ้าล็อกอินอยู่แล้ว ให้ redirect ไปยังหน้า dashboard
if (isLoggedIn()) {
    header("location: " . ($_SESSION['user_level'] == 'super_admin' ? 'ward_created.php' : 
                          ($_SESSION['user_level'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php')));
    exit;
}

// 1. รับค่า Token (รองรับทั้งจาก URL และจากการ Submit Form)
$token = $_POST['token'] ?? $_GET['token'] ?? '';
$error = '';
$token_valid = false;
$email = '';

// 2. ตรวจสอบ Token ว่าถูกต้องและยังไม่หมดอายุหรือไม่
if (empty($token)) {
    $error = "ไม่พบ Token หรือลิงก์ไม่ถูกต้อง";
} else {
    $sql = "SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) == 0) {
        $error = "ลิงก์รีเซ็ตรหัสผ่านนี้ไม่ถูกต้องหรือหมดอายุแล้ว กรุณาขอลิงก์ใหม่";
    } else {
        mysqli_stmt_bind_result($stmt, $email);
        mysqli_stmt_fetch($stmt);
        $token_valid = true; // Token ถูกต้อง
    }
    mysqli_stmt_close($stmt);
}

// 3. ประมวลผลเมื่อมีการ Submit Form (เฉพาะเมื่อ Token ถูกต้อง)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valid) {
    // ตรวจสอบ CSRF Token
    if (!isset($_POST['csrf_token']) || !$security->validateCSRFToken($_POST['csrf_token'])) {
        $error = "Token ความปลอดภัยไม่ถูกต้อง กรุณาลองใหม่";
    } else {
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            $error = "รหัสผ่านยืนยันไม่ตรงกัน";
        } else {
            // ตรวจสอบความยากง่ายของรหัสผ่าน
            $password_errors = $security->validatePassword($new_password);
            
            if (!empty($password_errors)) {
                $error = implode("<br>", $password_errors);
            } else {
                // ดึง User ID ก่อนอัปเดต (เพื่อใช้บันทึก Log)
                $user_id = 0;
                $user_sql = "SELECT id FROM users WHERE email = ?";
                $user_stmt = mysqli_prepare($conn, $user_sql);
                mysqli_stmt_bind_param($user_stmt, "s", $email);
                mysqli_stmt_execute($user_stmt);
                mysqli_stmt_bind_result($user_stmt, $user_id);
                mysqli_stmt_fetch($user_stmt);
                mysqli_stmt_close($user_stmt);

                if ($user_id) {
                    // อัพเดตรหัสผ่าน
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_sql = "UPDATE users SET password = ?, password_changed_at = NOW(), must_change_password = 0 WHERE email = ?";
                    $update_stmt = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $email);
                    
                    if (mysqli_stmt_execute($update_stmt)) {
                        // ลบ token หลังจากใช้งานแล้ว
                        $delete_sql = "DELETE FROM password_resets WHERE token = ?";
                        $delete_stmt = mysqli_prepare($conn, $delete_sql);
                        mysqli_stmt_bind_param($delete_stmt, "s", $token);
                        mysqli_stmt_execute($delete_stmt);
                        mysqli_stmt_close($delete_stmt);
                        
                        // บันทึก Log
                        if (isset($logger)) {
                            $logger->logUserAction(
                                'password_reset', 
                                "Password reset successfully via reset link", 
                                $user_id,
                                'users',
                                $user_id,
                                null,
                                null,
                                'medium'
                            );
                        }
                        
                        $success_msg = "รีเซ็ตรหัสผ่านเรียบร้อยแล้ว! กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่";
                        // ทำให้ Token เป็น invalid เพื่อซ่อนฟอร์ม
                        $token_valid = false; 
                    } else {
                        $error = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($update_stmt);
                } else {
                    $error = "ไม่พบข้อมูลผู้ใช้";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งรหัสผ่านใหม่ - ระบบจัดการเวรพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="logo">
                        <i class="fas fa-key"></i>
                        <h1>ตั้งรหัสผ่านใหม่</h1>
                    </div>
                    <p>กรุณาตั้งรหัสผ่านใหม่สำหรับบัญชีของคุณ</p>
                </div>
                
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                    </div>
                    <div class="auth-footer">
                        <a href="login.php" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> ไปหน้าเข้าสู่ระบบ
                        </a>
                    </div>

                <?php elseif (!empty($error) && !$token_valid): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                    <div class="auth-footer">
                        <a href="forgot_password.php" class="back-link">
                            <i class="fas fa-arrow-left"></i> ขอลิงก์รีเซ็ตรหัสผ่านใหม่
                        </a>
                    </div>

                <?php else: ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                        
                        <div class="form-group">
                            <label for="password">รหัสผ่านใหม่</label>
                            <div class="input-group">
                                <span class="input-icon"><i class="fas fa-lock"></i></span>
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="กรอกรหัสผ่านใหม่" required minlength="8">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">ยืนยันรหัสผ่านใหม่</label>
                            <div class="input-group">
                                <span class="input-icon"><i class="fas fa-lock"></i></span>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                       placeholder="ยืนยันรหัสผ่านใหม่" required minlength="8">
                            </div>
                        </div>
                        
                        <div class="password-requirements">
                            <h4>ข้อกำหนดรหัสผ่าน:</h4>
                            <ul>
                                <li>ความยาวอย่างน้อย 8 ตัวอักษร</li>
                                <li>ต้องมีตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว</li>
                                <li>ต้องมีตัวพิมพ์เล็กอย่างน้อย 1 ตัว</li>
                                <li>ต้องมีตัวเลขอย่างน้อย 1 ตัว</li>
                                <li>ต้องมีอักขระพิเศษอย่างน้อย 1 ตัว</li>
                            </ul>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> ตั้งรหัสผ่านใหม่
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
