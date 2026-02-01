<?php
// เริ่มการดักจับ Output เพื่อป้องกัน Error แปลกปลอมแทรกใน JSON
ob_start();
ini_set('display_errors', 0); // ปิดการแสดง Error หน้าเว็บ
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

try {
    // 1. ตรวจสอบไฟล์ Config
    $config_path = __DIR__ . '/../../config.php';
    if (!file_exists($config_path)) {
        throw new Exception("หาไฟล์ config.php ไม่เจอ (Path: $config_path)");
    }
    require_once $config_path;

    // 2. ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if (!isset($conn) || !$conn) {
        throw new Exception("เชื่อมต่อฐานข้อมูลไม่สำเร็จ");
    }

    if (!isLoggedIn()) {
        throw new Exception("Session หมดอายุ กรุณาล็อกอินใหม่");
    }

    // รับค่าจาก Client
    $token = $_POST['token'] ?? '';
    $action = $_POST['action'] ?? '';
    $user_lat = $_POST['lat'] ?? 0;
    $user_lng = $_POST['lng'] ?? 0;
    $accuracy = $_POST['accuracy'] ?? 0;
    $user_id = $_SESSION['user_id'];

    // --- [STEP 1] ตรวจสอบ Token ---
    $sql = "SELECT * FROM active_qr_tokens WHERE token = ? AND expires_at > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) throw new Exception("SQL Error (Check Token): " . mysqli_error($conn));
    
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $token_data = mysqli_fetch_assoc($result);

    if (!$token_data) {
        throw new Exception("QR Code หมดอายุหรือไม่ถูกต้อง กรุณาสแกนใหม่");
    }

    // --- [STEP 2] ตรวจสอบระยะทาง (ถ้ามีพิกัดจาก QR) ---
    if (!empty($token_data['latitude']) && !empty($token_data['longitude'])) {
        $distance = calculateDistance($user_lat, $user_lng, $token_data['latitude'], $token_data['longitude']);
        if ($distance > 100) { // รัศมี 100 เมตร
            throw new Exception("คุณอยู่นอกพื้นที่จุดลงเวลา (ห่างไป ".round($distance)." ม.)");
        }
    }

    // --- [STEP 3] ป้องกันสแกนซ้ำ (แก้ไขชื่อคอลัมน์ตรงนี้) ---
    // ใช้ 'scan_time' ตามฐานข้อมูลของคุณ
    $check_sql = "SELECT id FROM attendance_logs 
                  WHERE user_id = ? 
                  AND action_type = ? 
                  AND DATE(scan_time) = CURDATE() 
                  LIMIT 1";
                  
    $check_stmt = mysqli_prepare($conn, $check_sql);
    if (!$check_stmt) throw new Exception("SQL Error (Check Duplicate): " . mysqli_error($conn));
    
    mysqli_stmt_bind_param($check_stmt, "is", $user_id, $action);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $msg_action_thai = ($action == 'check_in') ? "เข้างาน" : "ออกงาน";
        throw new Exception("วันนี้คุณได้ลงเวลา '{$msg_action_thai}' ไปแล้ว ไม่สามารถทำรายการซ้ำได้");
    }
    mysqli_stmt_close($check_stmt);

    // --- [STEP 4] บันทึกข้อมูลลง DB ---
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $device_info = $_SERVER['HTTP_USER_AGENT'];

    // ใช้ 'scan_time' หรือปล่อยให้เป็น default current_timestamp
    $ins_sql = "INSERT INTO attendance_logs (user_id, action_type, latitude, longitude, accuracy, ip_address, device_info, qr_token_ref) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $ins_stmt = mysqli_prepare($conn, $ins_sql);
    if (!$ins_stmt) throw new Exception("SQL Error (Insert Log): " . mysqli_error($conn));

    mysqli_stmt_bind_param($ins_stmt, "isdddsss", $user_id, $action, $user_lat, $user_lng, $accuracy, $ip_address, $device_info, $token);

    // ... (โค้ดเดิม)

    if (mysqli_stmt_execute($ins_stmt)) {
        $msg_action = ($action == 'check_in') ? "เข้างาน" : "ออกงาน";
        
        // --- [STEP 5] แจ้งเตือน Admin และตรวจสอบความผิดปกติ ---
        
        if (function_exists('sendNotification')) {
            $full_name = $_SESSION['full_name'] ?? 'พนักงาน';
            $department_id = $_SESSION['department_id'] ?? 0;
            $current_scan_time = date('H:i'); // เวลาที่ลงจริง

            $alert_type = 'info';
            $noti_message = "พนักงาน {$full_name} ได้ลงเวลา {$msg_action} ({$current_scan_time})";

            // ***** START: LOGIC ตรวจสอบการเข้างานสาย *****
            if ($action == 'check_in') {
                // 1. ดึงข้อมูลกะงานของพนักงานวันนี้ (จากตาราง schedules)
                $schedule_sql = "SELECT shift_type FROM schedules WHERE user_id = ? AND schedule_date = CURDATE() LIMIT 1";
                $schedule_stmt = mysqli_prepare($conn, $schedule_sql);
                mysqli_stmt_bind_param($schedule_stmt, "i", $user_id);
                mysqli_stmt_execute($schedule_stmt);
                $shift_type = null;
                mysqli_stmt_bind_result($schedule_stmt, $shift_type);
                mysqli_stmt_fetch($schedule_stmt);
                mysqli_stmt_close($schedule_stmt);

                // 2. กำหนดเวลาเข้างานที่ยอมรับได้ (Hardcoded หรือดึงจาก Rules/Shifts)
                // *** สมมติ: Morning Shift ควรเข้าก่อน 8:05, อื่นๆ 17:05 ***
                $scheduled_start = null;
                if ($shift_type == 'morning' || $shift_type == 'morning_afternoon' || $shift_type == 'morning_night') {
                    $scheduled_start = '08:05:00'; // ยอมรับได้ไม่เกิน 8:05
                } 
                // สามารถเพิ่มเงื่อนไขสำหรับกะอื่นๆ (day/night/afternoon) ตาม Rules ของคุณ

                if ($scheduled_start) {
                    $shift_start_dt = new DateTime(date('Y-m-d') . ' ' . $scheduled_start);
                    $scan_dt = new DateTime(); // เวลาลงจริง
                    
                    if ($scan_dt > $shift_start_dt) {
                        $diff = $scan_dt->diff($shift_start_dt);
                        $late_minutes = ($diff->h * 60) + $diff->i;
                        
                        // ตั้ง Alert เป็น Danger ถ้าสาย
                        $alert_type = 'danger';
                        $noti_message = "🚨 พนักงาน {$full_name} เข้างานสาย! ({$current_scan_time}) - สายไป {$late_minutes} นาที";
                    }
                }
            }
            // ***** END: LOGIC ตรวจสอบการเข้างานสาย *****

            // 3. ส่ง Notification (รวมถึง Alert สาย/ปกติ)
            $admin_sql = "SELECT id FROM users WHERE (level = 'admin' AND department_id = ?) OR level = 'super_admin'";
            $admin_stmt = mysqli_prepare($conn, $admin_sql);
            if ($admin_stmt) {
                mysqli_stmt_bind_param($admin_stmt, "i", $department_id);
                mysqli_stmt_execute($admin_stmt);
                $admins = mysqli_stmt_get_result($admin_stmt);
                while ($row = mysqli_fetch_assoc($admins)) {
                    if ($row['id'] != $user_id) {
                        @sendNotification($conn, $row['id'], $noti_message, $alert_type);
                    }
                }
                mysqli_stmt_close($admin_stmt);
            }
        }

        // ล้าง Buffer แล้วส่งผลลัพธ์
        ob_clean();
        echo json_encode(['success' => true, 'message' => "บันทึกเวลา{$msg_action}เรียบร้อยแล้ว"]);
    } else {
        throw new Exception("บันทึกข้อมูลล้มเหลว: " . mysqli_stmt_error($ins_stmt));
    }

} catch (Exception $e) {
    // ล้าง Buffer ที่อาจมี Error ของ PHP ปนเปื้อน
    ob_end_clean();
    // ส่ง JSON Error กลับไปให้หน้าเว็บแสดงผล
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// ฟังก์ชันคำนวณระยะทาง
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; 
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}
?>