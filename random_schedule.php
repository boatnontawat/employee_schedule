<?php
include 'config.php';

if (!isLoggedIn() || $_SESSION['user_level'] != 'admin') {
    header("location: login.php");
    exit;
}

$department_id = $_SESSION['department_id'];
// แก้ไข: ดักค่า null ให้เป็นค่าปัจจุบันถ้าไม่มีส่งมา
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$selected_month_num = date('m', strtotime($selected_month));
$selected_year = date('Y', strtotime($selected_month));

// --- HELPER FUNCTION (ป้องกัน Error ฟังก์ชันหาย) ---
if (!function_exists('getShiftTypeThaiShort')) {
    function getShiftTypeThaiShort($shift) {
        $mapping = [
            'morning' => 'เช้า', 'afternoon' => 'บ่าย', 'night' => 'ดึก',
            'day' => 'D', 'night_shift' => 'N',
            'morning_afternoon' => 'ช-บ', 'morning_night' => 'ช-ด', 'afternoon_night' => 'บ-ด'
        ];
        return $mapping[$shift ?? ''] ?? ($shift ?? '-');
    }
}

if (!function_exists('month_to_thai_name')) {
    function month_to_thai_name($month_num) {
        $months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];
        return $months[(int)$month_num] ?? '';
    }
}

$selected_month_thai = month_to_thai_name($selected_month_num);
$selected_year_thai = $selected_year + 543;

// Initialize variables
$success_msg = "";
$error_msg = "";

// --- ฟังก์ชันดึง User ID ที่ "ลา" (Approved) ---
function getUsersOnLeave($conn, $target_date, $department_id) {
    $excluded_ids = [];
    
    // 1. เช็คจาก future_leave_requests (ลาล่วงหน้า)
    $sql = "SELECT user_id FROM future_leave_requests 
            WHERE department_id = ? 
            AND status = 'approved' 
            AND ? BETWEEN start_date AND end_date";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "is", $department_id, $target_date);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $excluded_ids[] = $row['user_id'];
            }
        }
        mysqli_stmt_close($stmt);
    }

    // 2. เช็คจาก leave_requests (ลาป่วย/กิจปกติ)
    $sql2 = "SELECT user_id FROM leave_requests 
             WHERE department_id = ? 
             AND status = 'approved' 
             AND ? BETWEEN start_date AND end_date";
    if ($stmt2 = mysqli_prepare($conn, $sql2)) {
        mysqli_stmt_bind_param($stmt2, "is", $department_id, $target_date);
        if (mysqli_stmt_execute($stmt2)) {
            $result2 = mysqli_stmt_get_result($stmt2);
            while ($row = mysqli_fetch_assoc($result2)) {
                $excluded_ids[] = $row['user_id'];
            }
        }
        mysqli_stmt_close($stmt2);
    }
    
    return array_unique($excluded_ids);
}

// Fetch Rules
$rules = null;
$rules_sql = "SELECT * FROM schedule_rules WHERE department_id = ?";
if ($rules_stmt = mysqli_prepare($conn, $rules_sql)) {
    mysqli_stmt_bind_param($rules_stmt, "i", $department_id);
    mysqli_stmt_execute($rules_stmt);
    $rules_result = mysqli_stmt_get_result($rules_stmt);
    $rules = mysqli_fetch_assoc($rules_result);
    mysqli_stmt_close($rules_stmt);
}

// Fetch Users
$users = [];
$users_sql = "SELECT id, full_name FROM users WHERE department_id = ? AND is_active = TRUE AND level != 'super_admin'";
if ($users_stmt = mysqli_prepare($conn, $users_sql)) {
    mysqli_stmt_bind_param($users_stmt, "i", $department_id);
    mysqli_stmt_execute($users_stmt);
    $users_result = mysqli_stmt_get_result($users_stmt);
    while ($user = mysqli_fetch_assoc($users_result)) {
        $users[] = $user;
    }
    mysqli_stmt_close($users_stmt);
}

// Fetch Holidays
$holidays = [];
$holidays_sql = "SELECT holiday_date FROM holiday_settings WHERE department_id = ? AND MONTH(holiday_date) = ? AND YEAR(holiday_date) = ?";
if ($holidays_stmt = mysqli_prepare($conn, $holidays_sql)) {
    mysqli_stmt_bind_param($holidays_stmt, "iii", $department_id, $selected_month_num, $selected_year);
    mysqli_stmt_execute($holidays_stmt);
    $holidays_result = mysqli_stmt_get_result($holidays_stmt);
    while ($holiday = mysqli_fetch_assoc($holidays_result)) {
        $holidays[] = $holiday['holiday_date'];
    }
    mysqli_stmt_close($holidays_stmt);
}

// ดึงข้อมูลตารางเวรเพื่อตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
$schedule_check_sql = "SELECT id FROM schedules WHERE department_id = ? AND MONTH(schedule_date) = ? AND YEAR(schedule_date) = ? LIMIT 1";
$has_schedule = false;
if ($check_stmt = mysqli_prepare($conn, $schedule_check_sql)) {
    mysqli_stmt_bind_param($check_stmt, "iii", $department_id, $selected_month_num, $selected_year);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    $has_schedule = mysqli_stmt_num_rows($check_stmt) > 0;
    mysqli_stmt_close($check_stmt);
}

// --- GENERATE SCHEDULE LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_schedule'])) {
    if (!$rules) {
        $error_msg = "กรุณาตั้งค่ากฏระเบียบเวรก่อน (เมนู: กำหนดระเบียบเวร)";
    } else if (empty($users)) {
        $error_msg = "ไม่พบพนักงานในระบบ";
    } else {
        mysqli_begin_transaction($conn);
        try {
            $month = $selected_month_num;
            $year = $selected_year;

            // 1. ดึง ID ตารางเวรเก่าทั้งหมดในเดือนนี้ของแผนกนี้
            $get_old_ids_sql = "SELECT id FROM schedules 
                                WHERE department_id = ? 
                                AND MONTH(schedule_date) = ? 
                                AND YEAR(schedule_date) = ?";
            $old_ids = [];
            if ($get_ids_stmt = mysqli_prepare($conn, $get_old_ids_sql)) {
                mysqli_stmt_bind_param($get_ids_stmt, "iii", $department_id, $month, $year);
                mysqli_stmt_execute($get_ids_stmt);
                $result = mysqli_stmt_get_result($get_ids_stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    $old_ids[] = $row['id'];
                }
                mysqli_stmt_close($get_ids_stmt);
            }

            if (!empty($old_ids)) {
                $ids_count = count($old_ids);
                // Placeholder สำหรับ ID สองชุด (ใช้สำหรับ OR condition)
                $ids_placeholder = implode(',', array_fill(0, $ids_count, '?'));
                
                // เตรียม Parameters สำหรับ bind_param (ID สองชุด)
                $params = array_merge($old_ids, $old_ids); 
                $types = str_repeat('i', count($params)); 
                
                // 2. ลบรายการที่อ้างอิงในตาราง swap_history 
                // (ต้องลบก่อน swap_requests เพราะ swap_history อ้างถึง swap_requests)
                $del_swap_hist_sql = "DELETE FROM swap_history 
                                      WHERE original_schedule_id IN ($ids_placeholder) 
                                      OR target_schedule_id IN ($ids_placeholder)";
                
                if ($del_hist_stmt = mysqli_prepare($conn, $del_swap_hist_sql)) {
                    // สร้างอาร์เรย์สำหรับ bind_param โดยส่งค่าเป็น Reference (แก้ไข Warning)
                    $bind_array = [$types];
                    foreach ($params as $key => $value) {
                        $bind_array[] = &$params[$key];
                    }
                    
                    array_unshift($bind_array, $del_hist_stmt);
                    
                    if (!call_user_func_array('mysqli_stmt_bind_param', $bind_array)) {
                         throw new Exception("Bind param for swap_history failed.");
                    }
                    
                    if (!mysqli_stmt_execute($del_hist_stmt)) {
                        throw new Exception("Delete swap_history failed: " . mysqli_error($conn));
                    }
                    mysqli_stmt_close($del_hist_stmt);
                }

                // 3. ลบรายการที่อ้างอิงในตาราง swap_requests 
                // (ตอนนี้ swap_requests สามารถถูกลบได้โดยไม่มี swap_history อ้างถึงแล้ว)
                $del_swap_sql = "DELETE FROM swap_requests 
                                 WHERE original_schedule_id IN ($ids_placeholder) 
                                 OR target_schedule_id IN ($ids_placeholder)";
                
                if ($del_swap_stmt = mysqli_prepare($conn, $del_swap_sql)) {
                    // สร้างอาร์เรย์สำหรับ bind_param ใหม่และใช้ reference
                    $bind_array = [$types];
                    foreach ($params as $key => $value) {
                        $bind_array[] = &$params[$key]; 
                    }
                    
                    array_unshift($bind_array, $del_swap_stmt);
                    
                    if (!call_user_func_array('mysqli_stmt_bind_param', $bind_array)) {
                        throw new Exception("Bind param for swap_requests failed.");
                    }

                    if (!mysqli_stmt_execute($del_swap_stmt)) {
                        throw new Exception("Delete swap_requests failed: " . mysqli_error($conn));
                    }
                    mysqli_stmt_close($del_swap_stmt);
                }
            }

            // 4. ลบข้อมูลหลักในตาราง schedules
            $del_sql = "DELETE FROM schedules WHERE department_id = ? AND MONTH(schedule_date) = ? AND YEAR(schedule_date) = ?";
            $d_stmt = mysqli_prepare($conn, $del_sql);
            mysqli_stmt_bind_param($d_stmt, "iii", $department_id, $selected_month_num, $selected_year);
            if (!mysqli_stmt_execute($d_stmt)) {
                throw new Exception("Delete schedules failed: " . mysqli_error($conn));
            }
            mysqli_stmt_close($d_stmt);
            

            $days_in_month = date('t', strtotime("$selected_year-$selected_month_num-01"));
            $user_ids = array_column($users, 'id');
            $generated_count = 0;

            // Config กะงาน
            $shift_defs = [
                'morning' => ['normal' => 'morning_count', 'holiday' => 'holiday_morning_count'],
                'afternoon' => ['normal' => 'afternoon_count', 'holiday' => 'holiday_afternoon_count'],
                'night' => ['normal' => 'night_count', 'holiday' => 'holiday_night_count'],
                'day' => ['normal' => 'day_count', 'holiday' => 'holiday_day_count'],
                'night_shift' => ['normal' => 'night_shift_count', 'holiday' => 'holiday_night_shift_count'],
                'morning_afternoon' => ['normal' => 'morning_afternoon_count', 'holiday' => 'holiday_morning_afternoon_count'],
                'morning_night' => ['normal' => 'morning_night_count', 'holiday' => 'holiday_morning_night_count'],
                'afternoon_night' => ['normal' => 'afternoon_night_count', 'holiday' => 'holiday_afternoon_night_count'],
            ];

            for ($day = 1; $day <= $days_in_month; $day++) {
                $current_date = "$selected_year-$selected_month_num-" . sprintf("%02d", $day);
                $is_holiday_date = in_array($current_date, $holidays);
                $day_of_week = date('N', strtotime($current_date));
                if ($day_of_week >= 6) $is_holiday_date = true;

                // 1. ดึงคนลา
                $leavers = getUsersOnLeave($conn, $current_date, $department_id);
                
                // 2. ตัดคนลาออก
                $daily_users = array_diff($user_ids, $leavers);
                
                // 3. สุ่ม
                shuffle($daily_users); 

                foreach ($shift_defs as $shift_key => $counts) {
                    $rule_key = $is_holiday_date ? $counts['holiday'] : $counts['normal'];
                    $needed = $rules[$rule_key] ?? 0;

                    if ($needed > 0 && !empty($daily_users)) {
                        // ดึงคนมาลงเวร
                        $count_to_assign = min(count($daily_users), $needed);
                        $assigned = array_splice($daily_users, 0, $count_to_assign);
                        
                        foreach ($assigned as $uid) {
                            $ins_sql = "INSERT INTO schedules (user_id, department_id, schedule_date, shift_type) VALUES (?, ?, ?, ?)";
                            $ins_stmt = mysqli_prepare($conn, $ins_sql);
                            // แก้ไข: ไม่ใช้ is_holiday เพื่อความปลอดภัย
                            mysqli_stmt_bind_param($ins_stmt, "iiss", $uid, $department_id, $current_date, $shift_key);
                            if (!mysqli_stmt_execute($ins_stmt)) {
                                throw new Exception("Insert failed: " . mysqli_error($conn));
                            }
                            mysqli_stmt_close($ins_stmt);
                            $generated_count++;
                        }
                    }
                }
            }

            mysqli_commit($conn);
            $success_msg = "สร้างตารางเวรสำเร็จ ($generated_count กะ)";
            header("Location: random_schedule.php?month=$selected_month&success=1");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error_msg = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}

// ดึงข้อมูลแผนก
$dept_sql = "SELECT name FROM departments WHERE id = ?";
$dept_stmt = mysqli_prepare($conn, $dept_sql);
mysqli_stmt_bind_param($dept_stmt, "i", $department_id);
mysqli_stmt_execute($dept_stmt);
mysqli_stmt_bind_result($dept_stmt, $dept_name);
mysqli_stmt_fetch($dept_stmt);
mysqli_stmt_close($dept_stmt);

// ดึงข้อมูลตารางเวรมาแสดงผล
$schedule_sql = "SELECT s.*, u.full_name FROM schedules s JOIN users u ON s.user_id = u.id 
                 WHERE s.department_id = ? AND MONTH(s.schedule_date) = ? AND YEAR(s.schedule_date) = ? 
                 ORDER BY s.schedule_date, FIELD(s.shift_type, 'morning', 'afternoon', 'night', 'day', 'night_shift', 'morning_afternoon', 'morning_night', 'afternoon_night')";

$schedules_by_date = [];
$s_result = null;

if ($stmt = mysqli_prepare($conn, $schedule_sql)) {
    mysqli_stmt_bind_param($stmt, "iii", $department_id, $selected_month_num, $selected_year);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $schedules_by_date[$row['schedule_date']][] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สุ่มเวรอัตโนมัติ</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Styles for Modal (Confirmation Popup) */
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1050; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.5); 
            backdrop-filter: blur(2px); 
        }
        .modal-dialog { 
            position: relative; 
            width: auto; 
            margin: 15vh auto; 
            pointer-events: none; 
            max-width: 400px; 
        }
        .modal-content { 
            position: relative; 
            display: flex; 
            flex-direction: column; 
            width: 100%; 
            pointer-events: auto; 
            background-color: #fff; 
            background-clip: padding-box; 
            border: none;
            border-radius: 1rem; 
            outline: 0; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            animation: slideDown 0.3s ease-out; 
            padding: 20px;
            text-align: center;
        }
        @keyframes slideDown { 
            from {transform: translateY(-30px); opacity: 0;} 
            to {transform: translateY(0); opacity: 1;} 
        }
        .modal-icon {
            font-size: 4rem;
            color: #ff8000;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .modal-title-custom {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .modal-message {
            margin-bottom: 20px;
            color: #666;
            padding: 0 10px;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        /* Style for secondary button is defined in style.css */
        
        /* --- Shift Display Styles (PRIMARY FIX: Aligning with manual_schedule.php) --- */
        .schedule-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            border-radius: 4px;
            font-size: 0.8rem;
            color: #000;
            font-weight: 600;
            display: flex;
            flex-direction: column; 
            align-items: flex-start;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            background: #e9ecef;
            line-height: 1.2;
        }

        /* Specific Shift Colors (Aligning with manual_schedule.php based on provided style.css) */
        .schedule-item.morning { background-color: #d1fae5; border-left: 3px solid #10b981; }
        .schedule-item.afternoon { background-color: #e0f2fe; border-left: 3px solid #0ea5e9; }
        .schedule-item.night { background-color: #ede9fe; border-left: 3px solid #8b5cf6; color: #000; }
        .schedule-item.day { background-color: #ffedd5; border-left: 3px solid #f97316; }
        
        /* Combo Shifts */
        .schedule-item.morning_afternoon { 
            background: linear-gradient(90deg, #d1fae5 50%, #e0f2fe 50%) !important; 
            border-left: 3px solid #0ea5e9; 
        }
        .schedule-item.morning_night { 
            background: linear-gradient(90deg, #d1fae5 50%, #ede9fe 50%) !important; 
            border-left: 3px solid #8b5cf6; 
        }
        .schedule-item.afternoon_night { 
            background: linear-gradient(90deg, #e0f2fe 50%, #ede9fe 50%) !important; 
            border-left: 3px solid #8b5cf6; 
        }
        .schedule-item.night_shift { 
             background-color: #fee2e2; border-left: 3px solid #dc3545; color: #000; 
        }

        /* Fix for Inner Spans (Employee Name and Shift Name) */
        .schedule-item span:first-child {
            font-weight: 700; 
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            line-height: 1.1;
            color: var(--text-main); 
            font-size: 0.8rem;
        }

        .schedule-item span:last-child {
            font-size: 0.7rem; 
            font-weight: 500; 
            color: var(--text-muted);
            line-height: 1.1;
            margin-top: 0; 
            padding: 0;
            background: transparent !important;
        }


        /* --- Calendar Grid Layout --- */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            border: 1px solid #ddd;
        }
        .calendar-header {
            background-color: #f8f9fa;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        .calendar-day {
            min-height: 100px;
            padding: 5px;
            border-right: 1px solid #eee;
            border-bottom: 1px solid #eee;
            background-color: #fff;
            position: relative;
        }
        .calendar-day.other-month {
            background-color: #f8f9fa;
        }
        .calendar-date {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?> (Admin)</span>
                <span>แผนก: <?php echo htmlspecialchars($dept_name ?? ''); ?></span>
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="admin_dashboard.php"><i class="fas fa-home"></i>แดชบอร์ด</a></li>
                    <li><a href="user_management.php"><i class="fas fa-users"></i>จัดการพนักงาน</a></li>
                    <li><a href="schedule_rules.php"><i class="fas fa-cog"></i>กำหนดระเบียบเวร</a></li>
                    <li><a href="random_schedule.php" class="active"><i class="fas fa-random"></i>สุ่มเวรอัตโนมัติ</a></li>
                    <li><a href="manual_schedule.php"><i class="fas fa-edit"></i>แก้ไขตารางเวร</a></li>
                    <li><a href="approve_requests.php"><i class="fas fa-check-circle"></i>อนุมัติคำขอ</a></li>
                    <li><a href="report_management.php"><i class="fas fa-chart-bar"></i>รายงาน</a></li>
                </ul>
            </nav>

            <main class="content-area">
                <div class="page-header">
                    <h2><i class="fas fa-random"></i> สุ่มเวรอัตโนมัติ</h2>
                    <p>สร้างตารางเวรโดยระบบ (ยกเว้นพนักงานลา)</p>
                </div>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> สร้างตารางเวรเรียบร้อยแล้ว</div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo $error_msg; ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <form method="get" class="month-selector" style="display:flex; gap:10px; align-items: center;">
                             <label for="month" style="margin:0; font-weight:600;">เลือกเดือน:</label>
                            <input type="month" id="month" name="month" class="form-control" value="<?php echo $selected_month; ?>" style="width: auto;">
                            <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> แสดง</button>
                        </form>
                        
                        <div class="alert alert-info" style="margin-top: 15px;">
                            <i class="fas fa-info-circle"></i> ระบบจะสุ่มเวรให้อัตโนมัติ <strong>โดยไม่นำคนที่ลา (Approved) มาคำนวณ</strong>
                        </div>

                       <form method="post" style="margin-top: 20px;" id="generateForm">
                            <input type="hidden" name="csrf_token" value="<?php echo isset($security) ? $security->generateCSRFToken() : ''; ?>">
                            <input type="hidden" name="month" value="<?php echo $selected_month; ?>">
                            <input type="hidden" name="generate_schedule" value="1" id="generateScheduleInput">
                            
                            <?php if ($has_schedule): ?>
                                <button type="button" class="btn btn-primary btn-lg" onclick="openConfirmationModal()">
                                    <i class="fas fa-magic"></i> สุ่มใหม่ (ลบของเก่า)
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-magic"></i> เริ่มสุ่มเวร
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <?php if ($has_schedule || !empty($schedules_by_date)): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-alt"></i> ตารางเวรเดือน <?php echo $selected_month_thai . ' ' . $selected_year_thai; ?></h3>
                        <?php if($has_schedule) echo "<p class='text-danger'>*การสุ่มใหม่จะลบข้อมูลที่แสดงอยู่ออกทั้งหมด</p>"; ?>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="calendar-grid">
                            <div class="calendar-header">อาทิตย์</div>
                            <div class="calendar-header">จันทร์</div>
                            <div class="calendar-header">อังคาร</div>
                            <div class="calendar-header">พุธ</div>
                            <div class="calendar-header">พฤหัสบดี</div>
                            <div class="calendar-header">ศุกร์</div>
                            <div class="calendar-header">เสาร์</div>
                            
                            <?php
                            $first_day = "$selected_year-$selected_month_num-01";
                            $total_days = date('t', strtotime($first_day));
                            $start_day_of_week = date('w', strtotime($first_day)); // 0 (Sun) to 6 (Sat)
                            
                            // ช่องว่างก่อนวันแรกของเดือน
                            for ($i = 0; $i < $start_day_of_week; $i++) {
                                echo "<div class='calendar-day empty other-month'></div>";
                            }
                            
                            for ($day = 1; $day <= $total_days; $day++) {
                                $current_date = sprintf("%s-%s-%02d", $selected_year, $selected_month_num, $day);
                                $today_schedules = $schedules_by_date[$current_date] ?? [];
                                $is_today = ($current_date == date('Y-m-d')) ? 'today' : '';
                                
                                echo "<div class='calendar-day $is_today'>";
                                echo "<div class='calendar-date'>$day</div>";
                                
                                foreach ($today_schedules as $sched) {
                                    $shift_short = getShiftTypeThaiShort($sched['shift_type']);
                                    
                                    // *** Markup ภายในให้ตรงกับ manual_schedule.php ***
                                    echo "<div class='schedule-item {$sched['shift_type']}'>
                                            <span style='font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; line-height: 1.1; color: var(--text-main); font-size: 0.8rem;'>{$sched['full_name']}</span>
                                            <span style='font-size: 0.7rem; font-weight: 500; color: var(--text-muted); line-height: 1.1; margin-top: 0;'>$shift_short</span>
                                          </div>";
                                    // *** END FIX ***
                                }
                                echo "</div>";
                            }

                            // ช่องว่างหลังวันสุดท้ายของเดือน (ให้ครบ 7 วันในสัปดาห์)
                            $last_day_of_week = date('w', strtotime("$selected_year-$selected_month_num-$total_days"));
                            $trailing_days = 6 - $last_day_of_week;
                            for ($i = 0; $i < $trailing_days; $i++) {
                                echo "<div class='calendar-day empty other-month'></div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <div id="confirmationModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="modal-title-custom">พบข้อมูลเวรเดิม!</div>
                <div class="modal-message">
                    มีตารางเวรอยู่แล้ว ต้องการลบและสร้างใหม่หรือไม่?<br>
                    <small style="color: #dc3545;">(คำขอสลับเวรและประวัติที่เกี่ยวข้องจะถูกลบด้วย)</small>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('confirmationModal')">
                        <i class="fas fa-times-circle"></i> ยกเลิก
                    </button>
                    <button type="button" class="btn btn-danger" onclick="submitGenerateForm()">
                        <i class="fas fa-check-circle"></i> ยืนยัน, สร้างใหม่
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function submitGenerateForm() {
            document.getElementById('generateForm').submit();
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>
<?php 
mysqli_close($conn); 
?>