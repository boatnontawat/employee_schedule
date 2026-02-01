<?php
include 'config.php';

// ถ้าล็อกอินอยู่แล้ว ให้ redirect ไปยังหน้า dashboard
if (isLoggedIn()) {
    $level = $_SESSION['user_level'] ?? '';
    if ($level == 'super_admin') {
        header("location: ward_created.php");
    } elseif ($level == 'admin') {
        header("location: admin_dashboard.php");
    } else {
        header("location: user_dashboard.php");
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = sanitizeInput($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // ตรวจสอบ Brute Force
    if (!$security->checkBruteForce($input_username, $ip_address)) {
        $login_err = "บัญชีนี้ถูกระงับชั่วคราวเนื่องจากพยายามล็อกอินผิดหลายครั้ง";
    } else {
        // [แก้ไข 1] เพิ่ม employee_level ในคำสั่ง SQL
        $sql = "SELECT id, username, password, full_name, level, department_id, employee_level, is_active, is_locked, locked_until, login_attempts, must_change_password, password_changed_at 
                FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $input_username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // [แก้ไข 2] เพิ่มตัวรับค่า employee_level ใน bind_result
                mysqli_stmt_bind_result($stmt, $id, $db_username, $hashed_password, $full_name, $level, $department_id, $employee_level, $is_active, $is_locked, $locked_until, $login_attempts, $must_change_password, $password_changed_at);
                mysqli_stmt_fetch($stmt);
                
                // ตรวจสอบสถานะบัญชี
                if (!$is_active) {
                    $login_err = "บัญชีนี้ถูกปิดใช้งาน";
                } elseif ($is_locked && strtotime((string)$locked_until) > time()) {
                    $login_err = "บัญชีนี้ถูกระงับชั่วคราวจนถึง " . date('d/m/Y H:i', strtotime((string)$locked_until));
                } else {
                    if (password_verify($password, (string)$hashed_password)) {
                        // ล็อกอินสำเร็จ
                        $security->clearFailedAttempts((string)$db_username, $ip_address);
                        
                        $update_sql = "UPDATE users SET login_attempts = 0, is_locked = FALSE, locked_until = NULL, last_login = NOW() WHERE id = ?";
                        $update_stmt = mysqli_prepare($conn, $update_sql);
                        mysqli_stmt_bind_param($update_stmt, "i", $id);
                        mysqli_stmt_execute($update_stmt);
                        mysqli_stmt_close($update_stmt);
                        
                        // [แก้ไข 3] บันทึก employee_level ลง Session
                        $_SESSION['user_id'] = $id;
                        $_SESSION['username'] = $db_username;
                        $_SESSION['full_name'] = $full_name;
                        $_SESSION['user_level'] = $level;
                        $_SESSION['department_id'] = $department_id;
                        $_SESSION['employee_level'] = $employee_level; // <--- สำคัญมากสำหรับการเช็คสิทธิ์
                        $_SESSION['last_activity'] = time();
                        
                        $logger->logUserAction('login', 'User logged in successfully', $id, 'users', $id, null, null, 'low');
                        
                        if ($security->isPasswordExpired($password_changed_at) || $must_change_password) {
                            $_SESSION['must_change_password'] = true;
                            header("location: change_password.php");
                            exit;
                        }
                        
                        if ($level == 'super_admin') {
                            header("location: ward_created.php");
                        } elseif ($level == 'admin') {
                            header("location: admin_dashboard.php");
                        } else {
                            header("location: user_dashboard.php");
                        }
                        exit;
                        
                    } else {
                        // รหัสผ่านผิด (Logic เดิม)
                        $login_attempts++;
                        $max_attempts = $security->getMaxLoginAttempts();
                        $update_sql = "UPDATE users SET login_attempts = ?, last_login_attempt = NOW() WHERE id = ?";
                        $update_stmt = mysqli_prepare($conn, $update_sql);
                        mysqli_stmt_bind_param($update_stmt, "ii", $login_attempts, $id);
                        mysqli_stmt_execute($update_stmt);
                        mysqli_stmt_close($update_stmt);
                        $security->recordFailedLogin($input_username, $ip_address);
                        
                        if ($login_attempts >= $max_attempts) {
                            $lockout_duration = $security->getLockoutDuration();
                            $locked_until = date('Y-m-d H:i:s', strtotime("+$lockout_duration minutes"));
                            $lock_sql = "UPDATE users SET is_locked = TRUE, locked_until = ? WHERE id = ?";
                            $lock_stmt = mysqli_prepare($conn, $lock_sql);
                            mysqli_stmt_bind_param($lock_stmt, "si", $locked_until, $id);
                            mysqli_stmt_execute($lock_stmt);
                            mysqli_stmt_close($lock_stmt);
                            $login_err = "บัญชีนี้ถูกระงับชั่วคราวเนื่องจากพยายามล็อกอินผิดหลายครั้ง";
                        } else {
                            $remaining = $max_attempts - $login_attempts;
                            $login_err = "รหัสผ่านไม่ถูกต้อง (เหลือ {$remaining} ครั้ง)";
                        }
                    }
                }
            } else {
                $login_err = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
                $security->recordFailedLogin($input_username, $ip_address);
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบจัดการเวรพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS เพิ่มเติมเล็กน้อยสำหรับลิงก์ที่เพิ่มเข้าไป */
        .auth-links {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }
        .auth-links a {
            font-size: 0.85rem;
            color: #64748b;
            text-decoration: none;
        }
        .auth-links a:hover {
            color: var(--primary, #2563eb);
            text-decoration: underline;
        }
        .register-prompt {
            margin-top: 25px;
            text-align: center;
            font-size: 0.9rem;
            color: #64748b;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
        }
        .register-prompt a {
            color: var(--primary, #2563eb);
            text-decoration: none;
            font-weight: 600;
        }
        .register-prompt a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-card-body">
                <div class="auth-header">
                    <h2>เข้าสู่ระบบ</h2>
                    <p>ระบบจัดการเวรพนักงาน</p>
                </div>
                <?php if (!empty($login_err)) { ?>
                    <div class="alert alert-danger" style="font-size:0.9rem; padding:10px; margin-bottom:20px; color:#ef4444; background:#fef2f2; border:1px solid #fecaca; border-radius:8px;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $login_err; ?>
                    </div>
                <?php } ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="username">ชื่อผู้ใช้งาน</label>
                        <input type="text" id="username" name="username" class="auth-input" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">รหัสผ่าน</label>
                        <input type="password" id="password" name="password" class="auth-input" required>
                    </div>

                    <div class="auth-links">
                        <a href="forgot_password.php">
                            <i class="fas fa-key"></i> ลืมรหัสผ่าน?
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                    </button>

                    <div class="register-prompt">
                        ยังไม่มีบัญชีใช่ไหม? 
                        <a href="register.php">สมัครสมาชิก</a>
                    </div>
                </form>
            </div>
            
            <div class="auth-card-logo-area">
                <i class="fas fa-hospital-user logo-icon-large"></i>
            </div>
        </div>
    </div>
</body>
</html>