<?php
include 'config.php';

// ถ้าล็อกอินอยู่แล้ว ให้ redirect ไปยังหน้า dashboard
if (isLoggedIn()) {
    header("location: " . ($_SESSION['user_level'] == 'super_admin' ? 'ward_created.php' : 
                          ($_SESSION['user_level'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php')));
    exit;
}

$token = $_GET['token'] ?? '';
$error = '';

// ตรวจสอบ token
if (empty($token)) {
    $error = "Token ไม่ถูกต้อง";
} else {
    $sql = "SELECT email, expires_at FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) == 0) {
        $error = "Token ไม่ถูกต้องหรือหมดอายุแล้ว";
    } else {
        mysqli_stmt_bind_result($stmt, $email, $expires_at);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error = "รหัสผ่านไม่ตรงกัน";
    } else {
        $password_errors = $security->validatePassword($new_password);
        if (!empty($password_errors)) {
            $error = implode("<br>", $password_errors);
        } else {
            // อัพเดตรหัสผ่าน
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ?, password_changed_at = NOW(), must_change_password = FALSE WHERE email = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $email);
            
            if (mysqli_stmt_execute($update_stmt)) {
                // ดึงข้อมูลผู้ใช้สำหรับบันทึก Log
                $user_sql = "SELECT id, full_name FROM users WHERE email = ?";
                $user_stmt = mysqli_prepare($conn, $user_sql);
                mysqli_stmt_bind_param($user_stmt, "s", $email);
                mysqli_stmt_execute($user_stmt);
                mysqli_stmt_bind_result($user_stmt, $user_id, $full_name);
                mysqli_stmt_fetch($user_stmt);
                mysqli_stmt_close($user_stmt);
                
                // ลบ token หลังจากใช้งานแล้ว
                $delete_sql = "DELETE FROM password_resets WHERE token = ?";
                $delete_stmt = mysqli_prepare($conn, $delete_sql);
                mysqli_stmt_bind_param($delete_stmt, "s", $token);
                mysqli_stmt_execute($delete_stmt);
                mysqli_stmt_close($delete_stmt);
                
                // บันทึก Log
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
                
                $success_msg = "รีเซ็ตรหัสผ่านเรียบร้อยแล้ว! กรุณาเข้าสู่ระบบอีกครั้ง";
            } else {
                $error = "เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน";
            }
            mysqli_stmt_close($update_stmt);
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
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                    <div class="auth-footer">
                        <a href="forgot_password.php" class="back-link">
                            <i class="fas fa-arrow-left"></i> กลับไปหน้าลืมรหัสผ่าน
                        </a>
                    </div>
                <?php elseif (isset($success_msg)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                    </div>
                    <div class="auth-footer">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                        </a>
                    </div>
                <?php else: ?>
                    <form method="post">
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