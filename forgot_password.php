<?php
include 'config.php';

if (isLoggedIn()) {
    $level = $_SESSION['user_level'] ?? '';
    if ($level == 'super_admin') header("location: ward_created.php");
    elseif ($level == 'admin') header("location: admin_dashboard.php");
    else header("location: user_dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($conn, $_POST['email']);
    
    $sql = "SELECT id, full_name FROM users WHERE email = ? AND is_active = TRUE";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $user_id, $full_name);
            mysqli_stmt_fetch($stmt);
            
            $token = bin2hex(random_bytes(50));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // (Logic เดิม: ลบ Token เก่า, สร้างใหม่, ส่งเมล/แสดงลิงก์...)
            // ... ขอละไว้เพื่อความกระชับ (ใช้โค้ดเดิมของคุณในส่วนนี้) ...
            
            $success_msg = "ส่งลิงค์รีเซ็ตรหัสผ่านเรียบร้อยแล้ว! กรุณาตรวจสอบอีเมล";
            // สำหรับ Demo แสดงลิงก์เลย
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            $debug_info = "<div style='margin-top:10px; padding:10px; background:#f0f9ff; border:1px dashed #bae6fd; border-radius:4px; font-size:0.85rem;'>
                              <strong>[Debug Mode]</strong><br>
                              <a href='{$reset_link}' target='_blank' style='color:#0284c7; word-break:break-all;'>คลิกที่นี่เพื่อรีเซ็ต</a>
                           </div>";
        } else {
            $error_msg = "ไม่พบอีเมลนี้ในระบบ";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน - ระบบจัดการเวรพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="auth-wrapper">
        <div class="auth-card">
            
            <div class="auth-card-body">
                <div class="auth-header">
                    <h2><i class="fas fa-lock" style="color: var(--accent);"></i> ลืมรหัสผ่าน</h2>
                    <p>กรอกอีเมลของคุณเพื่อรับลิงค์รีเซ็ต</p>
                </div>
                
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success" style="background:#dcfce7; color:#166534; padding:10px; border-radius:8px; font-size:0.9rem; border:1px solid #bbf7d0;">
                        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                        <?php echo $debug_info ?? ''; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger" style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px; font-size:0.9rem; border:1px solid #fecaca;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label for="email">อีเมล</label>
                        <input type="email" id="email" name="email" class="auth-input" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               placeholder="example@email.com" required>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-paper-plane"></i> ส่งลิงค์รีเซ็ต
                    </button>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="login.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">
                            <i class="fas fa-arrow-left"></i> กลับไปหน้าเข้าสู่ระบบ
                        </a>
                    </div>
                </form>

                <div class="auth-footer">
                    Employee Schedule System v1.0
                </div>
            </div>
            
            <div class="auth-card-logo-area">
                <i class="fas fa-key logo-icon-large"></i>
            </div>

        </div>
    </div>

</body>
</html>
<?php mysqli_close($conn); ?>