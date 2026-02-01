<?php
include 'config.php';

// ถ้าล็อกอินอยู่แล้ว ให้ redirect ไปยังหน้า dashboard
if (isLoggedIn()) {
    header("location: " . ($_SESSION['user_level'] == 'super_admin' ? 'ward_created.php' : 
                          ($_SESSION['user_level'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php')));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitizeInput($conn, $_POST['full_name']);
    $email = sanitizeInput($conn, $_POST['email']);
    $phone = sanitizeInput($conn, $_POST['phone']);
    $department_id = sanitizeInput($conn, $_POST['department_id']);
    $employee_level = sanitizeInput($conn, $_POST['employee_level']);
    
    // ตรวจสอบความถูกต้องของข้อมูล
    $errors = [];
    
    // [แก้ไขส่วนนี้] ปลดล็อกการตรวจสอบรหัสผ่านยาก
    if ($password !== $confirm_password) {
        $errors[] = "รหัสผ่านไม่ตรงกัน";
    } else {
        // คอมเมนต์บรรทัดตรวจสอบความยากออก
        // $password_errors = $security->validatePassword($password);
        // $errors = array_merge($errors, $password_errors);

        // ใส่การตรวจสอบพื้นฐานง่ายๆ แทน (เช่น ขั้นต่ำ 4 ตัวอักษร)
        if (strlen($password) < 4) {
            $errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 4 ตัวอักษร";
        }
    }
    
    // ตรวจสอบอีเมล
    if (!Validation::validateEmail($email)) {
        $errors[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }
    
    // ตรวจสอบเบอร์โทร
    if (!Validation::validatePhone($phone)) {
        $errors[] = "รูปแบบเบอร์โทรไม่ถูกต้อง";
    }
    
    // ตรวจสอบชื่อผู้ใช้
    if (!Validation::validateUsername($username)) {
        $errors[] = "ชื่อผู้ใช้ต้องมีความยาว 3-50 ตัวอักษร และใช้ได้แค่ a-z, 0-9, _";
    }
    
    // ตรวจสอบชื่อ-นามสกุล
    if (!Validation::validateFullName($full_name)) {
        $errors[] = "ชื่อ-นามสกุลต้องมีความยาว 2-100 ตัวอักษร";
    }
    
    // ตรวจสอบระดับพนักงาน
    if (!in_array($employee_level, [1, 2, 3])) {
        $errors[] = "กรุณาเลือกระดับพนักงานที่ถูกต้อง";
    }
    
    if (empty($errors)) {
        // ตรวจสอบว่ามี username นี้แล้วหรือไม่
        $check_sql = "SELECT id FROM users WHERE username = ?";
        if ($check_stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($check_stmt, "s", $username);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $errors[] = "ชื่อผู้ใช้นี้มีอยู่แล้ว";
            } else {
                // ตรวจสอบว่ามีอีเมลนี้แล้วหรือไม่
                $email_sql = "SELECT id FROM users WHERE email = ?";
                if ($email_stmt = mysqli_prepare($conn, $email_sql)) {
                    mysqli_stmt_bind_param($email_stmt, "s", $email);
                    mysqli_stmt_execute($email_stmt);
                    mysqli_stmt_store_result($email_stmt);
                    
                    if (mysqli_stmt_num_rows($email_stmt) > 0) {
                        $errors[] = "อีเมลนี้มีอยู่แล้วในระบบ";
                    }
                    mysqli_stmt_close($email_stmt);
                }
            }
            mysqli_stmt_close($check_stmt);
        }
    }
    
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $level = 'user'; // เริ่มต้นเป็น user เสมอ
        
        // เพิ่มผู้ใช้ใหม่
        $insert_sql = "INSERT INTO users (username, password, full_name, email, phone, department_id, level, employee_level) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
            mysqli_stmt_bind_param($insert_stmt, "sssssisi", $username, $hashed_password, $full_name, $email, $phone, $department_id, $level, $employee_level);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $new_user_id = mysqli_insert_id($conn);
                
                // บันทึก Log
                $logger->logUserAction(
                    'user_register', 
                    "User registered: {$username} ({$full_name}) - Level: {$employee_level}", 
                    $new_user_id,
                    'users',
                    $new_user_id,
                    null,
                    [
                        'username' => $username, 
                        'full_name' => $full_name, 
                        'email' => $email,
                        'employee_level' => $employee_level
                    ],
                    'medium'
                );
                
                $success_msg = "สมัครสมาชิกเรียบร้อยแล้ว! กรุณาเข้าสู่ระบบ";
                
                // Clear form
                $_POST = array();
            } else {
                $errors[] = "เกิดข้อผิดพลาดในการสมัครสมาชิก กรุณาลองอีกครั้ง";
            }
            mysqli_stmt_close($insert_stmt);
        }
    }
    
    if (!empty($errors)) {
        $error_msg = implode("<br>", $errors);
    }
}

// ดึงข้อมูลแผนก
$departments_sql = "SELECT id, name FROM departments";
$departments_result = mysqli_query($conn, $departments_sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - ระบบจัดการเวรพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Override เฉพาะหน้านี้ให้การ์ดกว้างขึ้น */
        .auth-card.register-card {
            max-width: 600px;
            flex-direction: column;
            min-height: auto;
        }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        @media (max-width: 600px) { .form-row { flex-direction: column; gap: 0; } }
        
        .level-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; color: #fff; font-weight: 600; margin-right: 5px; }
        .level-1 { background: #3b82f6; } .level-2 { background: #10b981; } .level-3 { background: #f59e0b; }
    </style>
</head>
<body>
    
    <div class="auth-wrapper">
        <div class="auth-card register-card">
            
            <div class="auth-card-body" style="padding: 40px;">
                <div class="auth-header" style="text-align: center;">
                    <h2><i class="fas fa-user-plus" style="color: var(--accent);"></i> สมัครสมาชิกใหม่</h2>
                    <p>กรอกข้อมูลเพื่อเริ่มใช้งานระบบ</p>
                </div>
                
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success" style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; text-align:center; border:1px solid #bbf7d0;">
                        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                        <br><br>
                        <a href="login.php" class="btn-login" style="display:inline-block; width:auto; padding:8px 20px; text-decoration:none;">เข้าสู่ระบบทันที</a>
                    </div>
                <?php else: ?>
                
                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger" style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px; margin-bottom:20px; border:1px solid #fecaca;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>ชื่อผู้ใช้ *</label>
                            <input type="text" name="username" class="auth-input" value="<?php echo $_POST['username'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>ชื่อ-นามสกุล *</label>
                            <input type="text" name="full_name" class="auth-input" value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>รหัสผ่าน * <small style="color:#666; font-weight:normal;">(ขั้นต่ำ 4 ตัวอักษร)</small></label>
                            <input type="password" name="password" class="auth-input" required>
                        </div>
                        <div class="form-group">
                            <label>ยืนยันรหัสผ่าน *</label>
                            <input type="password" name="confirm_password" class="auth-input" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>อีเมล *</label>
                            <input type="email" name="email" class="auth-input" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>เบอร์โทร *</label>
                            <input type="text" name="phone" class="auth-input" value="<?php echo $_POST['phone'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>แผนก *</label>
                            <select name="department_id" class="auth-input" required>
                                <option value="">-- เลือกแผนก --</option>
                                <?php 
                                mysqli_data_seek($departments_result, 0);
                                while($dept = mysqli_fetch_assoc($departments_result)): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php echo (($_POST['department_id'] ?? '') == $dept['id']) ? 'selected' : ''; ?>>
                                        <?php echo $dept['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ระดับพนักงาน *</label>
                            <select name="employee_level" class="auth-input" required>
                                <option value="">-- เลือกระดับ --</option>
                                <option value="1" <?php echo (($_POST['employee_level'] ?? '') == '1') ? 'selected' : ''; ?>>ระดับ 1 (เริ่มต้น)</option>
                                <option value="2" <?php echo (($_POST['employee_level'] ?? '') == '2') ? 'selected' : ''; ?>>ระดับ 2 (ประสบการณ์)</option>
                                <option value="3" <?php echo (($_POST['employee_level'] ?? '') == '3') ? 'selected' : ''; ?>>ระดับ 3 (หัวหน้า)</option>
                            </select>
                        </div>
                    </div>

                    <div style="background:#f8fafc; padding:15px; border-radius:8px; margin-bottom:20px; font-size:0.85rem; color:#64748b;">
                        <div style="margin-bottom:5px;"><span class="level-badge level-1">L1</span> พนักงานใหม่ / เริ่มงาน</div>
                        <div style="margin-bottom:5px;"><span class="level-badge level-2">L2</span> พนักงานประจำ / มีประสบการณ์</div>
                        <div><span class="level-badge level-3">L3</span> หัวหน้างาน / ผู้ดูแล</div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-check"></i> ยืนยันการสมัคร
                    </button>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="login.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">
                            มีบัญชีแล้ว? เข้าสู่ระบบ
                        </a>
                    </div>
                </form>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>