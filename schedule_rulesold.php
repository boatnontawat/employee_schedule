<?php
include 'config.php';

if (!isLoggedIn() || ($_SESSION['user_level'] != 'admin' && $_SESSION['user_level'] != 'super_admin')) {
    header("location: login.php");
    exit;
}

$department_id = $_SESSION['department_id'];
$current_user_level = $_SESSION['user_level'];

// 1. ดึงข้อมูลกฏเดิม
$rules_sql = "SELECT * FROM schedule_rules WHERE department_id = ?";
$rules_stmt = mysqli_prepare($conn, $rules_sql);
mysqli_stmt_bind_param($rules_stmt, "i", $department_id);
mysqli_stmt_execute($rules_stmt);
$rules_result = mysqli_stmt_get_result($rules_stmt);
$current_rules = mysqli_fetch_assoc($rules_result);

// แปลงเวลาให้พร้อมแสดง (HH:mm)
if ($current_rules) {
    $current_rules['work_start_time'] = !empty($current_rules['work_start_time']) ? date('H:i', strtotime($current_rules['work_start_time'])) : '08:00';
    $current_rules['work_end_time'] = !empty($current_rules['work_end_time']) ? date('H:i', strtotime($current_rules['work_end_time'])) : '17:00';
}

// 2. ดึงข้อมูลกฏระดับพนักงาน
$level_rules_sql = "SELECT * FROM employee_level_rules WHERE department_id = ?";
$level_rules_stmt = mysqli_prepare($conn, $level_rules_sql);
mysqli_stmt_bind_param($level_rules_stmt, "i", $department_id);
mysqli_stmt_execute($level_rules_stmt);
$level_rules_result = mysqli_stmt_get_result($level_rules_stmt);
$current_level_rules = [];
while ($row = mysqli_fetch_assoc($level_rules_result)) {
    $current_level_rules[$row['employee_level']] = $row;
}

// 3. ดึงข้อมูลกฏวันหยุด
$holiday_rules_sql = "SELECT * FROM holiday_rules WHERE department_id = ?";
$holiday_rules_stmt = mysqli_prepare($conn, $holiday_rules_sql);
mysqli_stmt_bind_param($holiday_rules_stmt, "i", $department_id);
mysqli_stmt_execute($holiday_rules_stmt);
$holiday_rules_result = mysqli_stmt_get_result($holiday_rules_stmt);
$current_holiday_rules = mysqli_fetch_assoc($holiday_rules_result);

// 4. บันทึกข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_rules'])) {
    
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_msg = "Invalid CSRF Token";
    } else {
        $rule_name = sanitizeInput($conn, $_POST['rule_name'] ?? '');
        $rule_description = sanitizeInput($conn, $_POST['rule_description'] ?? '');
        
        // รับค่าจำนวนคน (8 ตัวแปร)
        $morning_count = (int)($_POST['morning_count'] ?? 0);
        $afternoon_count = (int)($_POST['afternoon_count'] ?? 0);
        $night_count = (int)($_POST['night_count'] ?? 0);
        $day_count = (int)($_POST['day_count'] ?? 0);
        $night_shift_count = (int)($_POST['night_shift_count'] ?? 0);
        $morning_afternoon_count = (int)($_POST['morning_afternoon_count'] ?? 0);
        $morning_night_count = (int)($_POST['morning_night_count'] ?? 0);
        $afternoon_night_count = (int)($_POST['afternoon_night_count'] ?? 0);
        
        // รับค่าวันหยุด (8 ตัวแปร)
        $holiday_morning_count = (int)($_POST['holiday_morning_count'] ?? 0);
        $holiday_afternoon_count = (int)($_POST['holiday_afternoon_count'] ?? 0);
        $holiday_night_count = (int)($_POST['holiday_night_count'] ?? 0);
        $holiday_day_count = (int)($_POST['holiday_day_count'] ?? 0);
        $holiday_night_shift_count = (int)($_POST['holiday_night_shift_count'] ?? 0);
        $holiday_morning_afternoon_count = (int)($_POST['holiday_morning_afternoon_count'] ?? 0);
        $holiday_morning_night_count = (int)($_POST['holiday_morning_night_count'] ?? 0);
        $holiday_afternoon_night_count = (int)($_POST['holiday_afternoon_night_count'] ?? 0);
        
        // กฏการลา (3 ตัวแปร)
        $max_concurrent_leave = (int)($_POST['max_concurrent_leave'] ?? 3);
        $work_days_before_leave = (int)($_POST['work_days_before_leave'] ?? 5);
        $monthly_leave_days = (int)($_POST['monthly_leave_days'] ?? 8);
        
        // เวลาทำงาน (2 ตัวแปร)
        $work_start_time = $_POST['work_start_time'] ?? '08:00';
        $work_end_time = $_POST['work_end_time'] ?? '17:00';

        // รวมจำนวน Integer ทั้งหมด: 8(ปกติ) + 8(หยุด) + 3(ลา) = 19 ตัว
        
        // Level Rules
        $level1_min = (int)($_POST['level1_min'] ?? 0);
        $level1_max = (int)($_POST['level1_max'] ?? 0);
        $level2_min = (int)($_POST['level2_min'] ?? 0);
        $level2_max = (int)($_POST['level2_max'] ?? 0);
        $level3_min = (int)($_POST['level3_min'] ?? 0);
        $level3_max = (int)($_POST['level3_max'] ?? 0);
        $holiday_min_level = (int)($_POST['holiday_min_level'] ?? 1);
        
        mysqli_begin_transaction($conn);
        
        try {
            if ($current_rules) {
                // === UPDATE ===
                $update_sql = "UPDATE schedule_rules SET 
                              rule_name = ?, rule_description = ?,
                              morning_count = ?, afternoon_count = ?, night_count = ?,
                              day_count = ?, night_shift_count = ?, morning_afternoon_count = ?,
                              morning_night_count = ?, afternoon_night_count = ?,
                              holiday_morning_count = ?, holiday_afternoon_count = ?, holiday_night_count = ?,
                              holiday_day_count = ?, holiday_night_shift_count = ?,
                              holiday_morning_afternoon_count = ?, holiday_morning_night_count = ?, holiday_afternoon_night_count = ?,
                              max_concurrent_leave = ?, work_days_before_leave = ?, monthly_leave_days = ?,
                              work_start_time = ?, work_end_time = ?,
                              updated_at = NOW()
                              WHERE department_id = ?";
                
                $stmt = mysqli_prepare($conn, $update_sql);
                
                // สร้าง Type String: ss + 19i + ss + i = 24 chars
                $types = "ss" . str_repeat("i", 19) . "ssi";
                
                mysqli_stmt_bind_param($stmt, $types, 
                    $rule_name, $rule_description, // ss
                    $morning_count, $afternoon_count, $night_count, // 3i
                    $day_count, $night_shift_count, $morning_afternoon_count, // 3i
                    $morning_night_count, $afternoon_night_count, // 2i
                    $holiday_morning_count, $holiday_afternoon_count, $holiday_night_count, // 3i
                    $holiday_day_count, $holiday_night_shift_count, // 2i
                    $holiday_morning_afternoon_count, $holiday_morning_night_count, $holiday_afternoon_night_count, // 3i
                    $max_concurrent_leave, $work_days_before_leave, $monthly_leave_days, // 3i
                    $work_start_time, $work_end_time, // ss
                    $department_id // i
                );
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                // === INSERT ===
                $insert_sql = "INSERT INTO schedule_rules 
                              (department_id, rule_name, rule_description, 
                               morning_count, afternoon_count, night_count,
                               day_count, night_shift_count, morning_afternoon_count,
                               morning_night_count, afternoon_night_count,
                               holiday_morning_count, holiday_afternoon_count, holiday_night_count,
                               holiday_day_count, holiday_night_shift_count,
                               holiday_morning_afternoon_count, holiday_morning_night_count, holiday_afternoon_night_count,
                               max_concurrent_leave, work_days_before_leave, monthly_leave_days, 
                               work_start_time, work_end_time, created_by)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $insert_sql);
                
                // สร้าง Type String: iss + 19i + ss + i = 25 chars
                $types = "iss" . str_repeat("i", 19) . "ssi";
                
                mysqli_stmt_bind_param($stmt, $types, 
                    $department_id, $rule_name, $rule_description, // iss
                    $morning_count, $afternoon_count, $night_count,
                    $day_count, $night_shift_count, $morning_afternoon_count,
                    $morning_night_count, $afternoon_night_count,
                    $holiday_morning_count, $holiday_afternoon_count, $holiday_night_count,
                    $holiday_day_count, $holiday_night_shift_count,
                    $holiday_morning_afternoon_count, $holiday_morning_night_count, $holiday_afternoon_night_count,
                    $max_concurrent_leave, $work_days_before_leave, $monthly_leave_days, // 19i
                    $work_start_time, $work_end_time, // ss
                    $_SESSION['user_id'] // i
                );
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            
            // Save Employee Levels
            $levels = [1, 2, 3];
            $min_values = [$level1_min, $level2_min, $level3_min];
            $max_values = [$level1_max, $level2_max, $level3_max];
            
            foreach ($levels as $index => $level) {
                $min_val = $min_values[$index];
                $max_val = $max_values[$index];
                
                if (isset($current_level_rules[$level])) {
                    $sql = "UPDATE employee_level_rules SET min_per_day = ?, max_per_day = ?, updated_at = NOW() WHERE department_id = ? AND employee_level = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "iiii", $min_val, $max_val, $department_id, $level);
                } else {
                    $sql = "INSERT INTO employee_level_rules (department_id, employee_level, min_per_day, max_per_day, created_by) VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "iiiii", $department_id, $level, $min_val, $max_val, $_SESSION['user_id']);
                }
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            
            // Save Holiday Rules
            if ($current_holiday_rules) {
                $sql = "UPDATE holiday_rules SET min_employee_level = ?, updated_at = NOW() WHERE department_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $holiday_min_level, $department_id);
            } else {
                $sql = "INSERT INTO holiday_rules (department_id, min_employee_level, created_by) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iii", $department_id, $holiday_min_level, $_SESSION['user_id']);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            mysqli_commit($conn);
            $success_msg = "บันทึกกฏระเบียบเรียบร้อยแล้ว";
            
            header("Location: schedule_rules.php");
            exit;
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error_msg = "Error saving rules: " . $e->getMessage();
        }
    }
}

// Fetch Department Name
$current_dept_name = "Unknown";
$dept_sql = "SELECT name FROM departments WHERE id = ?";
$dept_stmt = mysqli_prepare($conn, $dept_sql);
mysqli_stmt_bind_param($dept_stmt, "i", $department_id);
mysqli_stmt_execute($dept_stmt);
mysqli_stmt_bind_result($dept_stmt, $current_dept_name);
mysqli_stmt_fetch($dept_stmt);
mysqli_stmt_close($dept_stmt);

// Super Admin Logic
if ($current_user_level == 'super_admin') {
    $dept_list_sql = "SELECT id, name FROM departments";
    $dept_list_result = mysqli_query($conn, $dept_list_sql);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กำหนดระเบียบเวร - ระบบจัดการเวรพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        input[type="time"] {
            font-family: 'Sarabun', sans-serif;
            color: #334155;
            background-color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?></span>
                <a href="admin_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <?php if ($current_user_level == 'super_admin'): ?>
                    <li><a href="ward_created.php"><i class="fas fa-building"></i>จัดการหน่วยงาน</a></li>
                    <?php endif; ?>
                    <li><a href="user_management.php"><i class="fas fa-users"></i>จัดการพนักงาน</a></li>
                    <li><a href="schedule_rules.php" class="active"><i class="fas fa-cog"></i>กำหนดระเบียบเวร</a></li>
                    <li><a href="random_schedule.php"><i class="fas fa-random"></i>สุ่มเวรอัตโนมัติ</a></li>
                    <li><a href="report_management.php"><i class="fas fa-chart-bar"></i>รายงาน</a></li>
                </ul>
            </nav>
            
            <main class="content-area">
                <div class="page-header">
                    <h2><i class="fas fa-cog"></i> กำหนดระเบียบเวร (<?php echo htmlspecialchars($current_dept_name); ?>)</h2>
                </div>
                
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                <?php endif; ?>
                
                <?php if ($current_user_level == 'super_admin'): ?>
                <div class="card">
                    <div class="card-body">
                        <form method="get">
                            <label>เลือกแผนก:</label>
                            <select name="department_id" class="form-control" onchange="this.form.submit()">
                                <?php while($d = mysqli_fetch_assoc($dept_list_result)): ?>
                                    <option value="<?php echo $d['id']; ?>" <?php echo $department_id == $d['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($d['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header"><h3>ตั้งค่ากฏระเบียบ</h3></div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="card" style="background-color: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                                <div class="card-body" style="padding: 15px;">
                                    <h4 style="margin-bottom: 15px; color: #334155;"><i class="fas fa-clock"></i> เวลาทำงานปกติ</h4>
                                    <div class="form-row">
                                        <div class="form-group" style="flex: 1;">
                                            <label style="font-weight: 600;">เวลาเข้างาน (Start)</label>
                                            <input type="time" name="work_start_time" class="form-control" 
                                                   value="<?php echo $current_rules['work_start_time'] ?? '08:00'; ?>" required>
                                            <small class="text-muted">* ใช้คำนวณสถานะ "สาย"</small>
                                        </div>
                                        <div class="form-group" style="flex: 1;">
                                            <label style="font-weight: 600;">เวลาเลิกงาน (End)</label>
                                            <input type="time" name="work_end_time" class="form-control" 
                                                   value="<?php echo $current_rules['work_end_time'] ?? '17:00'; ?>" required>
                                            <small class="text-muted">* ใช้คำนวณสถานะ "ออกก่อน"</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>ชื่อกฏระเบียบ</label>
                                <input type="text" name="rule_name" class="form-control" value="<?php echo htmlspecialchars($current_rules['rule_name'] ?? 'กฏระเบียบพื้นฐาน'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>คำอธิบาย</label>
                                <textarea name="rule_description" class="form-control" rows="2"><?php echo htmlspecialchars($current_rules['rule_description'] ?? ''); ?></textarea>
                            </div>

                            <h4 class="section-title">จำนวนคนต่อเวร (วันปกติ)</h4>
                            <div class="rules-grid">
                                <?php 
                                $shifts = [
                                    'morning_count' => 'เวรเช้า', 'afternoon_count' => 'เวรบ่าย', 'night_count' => 'เวรดึก',
                                    'day_count' => 'เวรเดย์', 'night_shift_count' => 'เวรไนท์',
                                    'morning_afternoon_count' => 'เช้า-บ่าย', 'morning_night_count' => 'เช้า-ดึก', 'afternoon_night_count' => 'บ่าย-ดึก'
                                ];
                                foreach($shifts as $key => $label): 
                                ?>
                                <div class="rule-item">
                                    <label><?php echo $label; ?></label>
                                    <input type="number" name="<?php echo $key; ?>" class="form-control" min="0" max="50" 
                                           value="<?php echo $current_rules[$key] ?? 0; ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <h4 class="section-title">จำนวนคนต่อเวร (วันหยุด)</h4>
                            <div class="rules-grid">
                                <?php 
                                $h_shifts = [
                                    'holiday_morning_count' => 'เวรเช้า', 'holiday_afternoon_count' => 'เวรบ่าย', 'holiday_night_count' => 'เวรดึก',
                                    'holiday_day_count' => 'เวรเดย์', 'holiday_night_shift_count' => 'เวรไนท์',
                                    'holiday_morning_afternoon_count' => 'เช้า-บ่าย', 'holiday_morning_night_count' => 'เช้า-ดึก', 'holiday_afternoon_night_count' => 'บ่าย-ดึก'
                                ];
                                foreach($h_shifts as $key => $label): 
                                ?>
                                <div class="rule-item">
                                    <label><?php echo $label; ?></label>
                                    <input type="number" name="<?php echo $key; ?>" class="form-control" min="0" max="50" 
                                           value="<?php echo $current_rules[$key] ?? 0; ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <h4 class="section-title">กฏการลา</h4>
                            <div class="rules-grid">
                                <div class="rule-item">
                                    <label>ลาพร้อมกันสูงสุด (คน)</label>
                                    <input type="number" name="max_concurrent_leave" class="form-control" min="1" value="<?php echo $current_rules['max_concurrent_leave'] ?? 3; ?>">
                                </div>
                                <div class="rule-item">
                                    <label>วันหยุดต่อเดือน</label>
                                    <input type="number" name="monthly_leave_days" class="form-control" min="0" value="<?php echo $current_rules['monthly_leave_days'] ?? 8; ?>">
                                </div>
                                <div class="rule-item">
                                    <label>ทำงานขั้นต่ำก่อนลา (วัน)</label>
                                    <input type="number" name="work_days_before_leave" class="form-control" min="0" value="<?php echo $current_rules['work_days_before_leave'] ?? 5; ?>">
                                </div>
                            </div>

                            <h4 class="section-title">เงื่อนไขระดับพนักงาน</h4>
                            <div class="rules-grid">
                                <?php for($i=1; $i<=3; $i++): ?>
                                <div class="rule-item">
                                    <label>ระดับ <?php echo $i; ?> (Min-Max)</label>
                                    <div style="display:flex; gap:5px;">
                                        <input type="number" name="level<?php echo $i; ?>_min" class="form-control" placeholder="Min" 
                                               value="<?php echo $current_level_rules[$i]['min_per_day'] ?? 0; ?>">
                                        <input type="number" name="level<?php echo $i; ?>_max" class="form-control" placeholder="Max" 
                                               value="<?php echo $current_level_rules[$i]['max_per_day'] ?? 0; ?>">
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>

                            <h4 class="section-title">อื่นๆ</h4>
                            <div class="form-group">
                                <label>ระดับพนักงานขั้นต่ำสำหรับวันหยุด</label>
                                <select name="holiday_min_level" class="form-control">
                                    <?php 
                                    $min_lvl = $current_holiday_rules['min_employee_level'] ?? 1; 
                                    ?>
                                    <option value="1" <?php echo $min_lvl == 1 ? 'selected' : ''; ?>>ระดับ 1</option>
                                    <option value="2" <?php echo $min_lvl == 2 ? 'selected' : ''; ?>>ระดับ 2</option>
                                    <option value="3" <?php echo $min_lvl == 3 ? 'selected' : ''; ?>>ระดับ 3</option>
                                </select>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="save_rules" class="btn btn-primary"><i class="fas fa-save"></i> บันทึกข้อมูล</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>