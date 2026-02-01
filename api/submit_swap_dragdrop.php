<?php
include '../config.php';

// บอก Browser ว่าเราจะคืนค่าเป็น JSON เท่านั้น
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อนทำรายการ']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าและป้องกัน SQL Injection
    $original_schedule_id = sanitizeInput($conn, $_POST['original_schedule_id']);
    $target_user_id = sanitizeInput($conn, $_POST['target_user_id']);
    $target_schedule_id = sanitizeInput($conn, $_POST['target_schedule_id']);
    $reason = isset($_POST['reason']) ? sanitizeInput($conn, $_POST['reason']) : '';

    // 1. ตรวจสอบว่าเป็นเจ้าของเวรจริงไหม
    $check_sql = "SELECT id, schedule_date FROM schedules WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "ii", $original_schedule_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $s_id, $s_date);
    
    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลเวรของคุณ หรือคุณไม่มีสิทธิ์แก้ไข']);
        exit;
    }
    mysqli_stmt_close($stmt);

    // 2. ตรวจสอบเวรปลายทางว่ามีจริงไหม
    $target_sql = "SELECT id FROM schedules WHERE id = ? AND user_id = ?";
    $t_stmt = mysqli_prepare($conn, $target_sql);
    mysqli_stmt_bind_param($t_stmt, "ii", $target_schedule_id, $target_user_id);
    mysqli_stmt_execute($t_stmt);
    
    if (!mysqli_stmt_fetch($t_stmt)) {
        mysqli_stmt_close($t_stmt);
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลเวรปลายทาง หรือข้อมูลไม่ถูกต้อง']);
        exit;
    }
    mysqli_stmt_close($t_stmt);

    // 3. บันทึกลงฐานข้อมูล (swap_requests)
    $ins_sql = "INSERT INTO swap_requests (user_id, target_user_id, original_schedule_id, target_schedule_id, reason, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
    $ins = mysqli_prepare($conn, $ins_sql);
    mysqli_stmt_bind_param($ins, "iiiss", $user_id, $target_user_id, $original_schedule_id, $target_schedule_id, $reason);

    if (mysqli_stmt_execute($ins)) {
        // --- ส่งการแจ้งเตือน ---
        if (function_exists('sendNotification')) {
            // 1. แจ้ง User ปลายทาง (คนที่เราขอแลกด้วย)
            sendNotification($conn, $target_user_id, "มีคำขอสลับเวรใหม่เข้ามา", "warning");

            // [เพิ่ม] ดึงชื่อคนที่ถูกขอแลก (Target User) เพื่อมาแสดงในแจ้งเตือน Admin
            $q_target = "SELECT full_name FROM users WHERE id = ?";
            $stmt_t = mysqli_prepare($conn, $q_target);
            mysqli_stmt_bind_param($stmt_t, "i", $target_user_id);
            mysqli_stmt_execute($stmt_t);
            mysqli_stmt_bind_result($stmt_t, $target_full_name);
            mysqli_stmt_fetch($stmt_t);
            mysqli_stmt_close($stmt_t);
            
            // ถ้าดึงชื่อไม่ได้ (กันเหนียว) ให้ใช้คำว่า "เพื่อนร่วมงาน" แทน
            if (empty($target_full_name)) $target_full_name = "เพื่อนร่วมงาน";

            // 2. แจ้งเตือน Admin และ Super Admin ทุกคนในแผนก
            $dept_id = $_SESSION['department_id']; 
            $sender_name = $_SESSION['full_name'];
            $current_user_id = $_SESSION['user_id']; // ID ของคนที่ทำรายการ (ตัวเรา)

            $adm_sql = "SELECT id FROM users WHERE department_id = ? AND (level = 'admin' OR level = 'super_admin')";
            $adm_stmt = mysqli_prepare($conn, $adm_sql);
            mysqli_stmt_bind_param($adm_stmt, "i", $dept_id);
            mysqli_stmt_execute($adm_stmt);
            $adm_res = mysqli_stmt_get_result($adm_stmt);

            while ($adm = mysqli_fetch_assoc($adm_res)) {
                // ป้องกันการแจ้งเตือนตัวเอง
                if ($adm['id'] == $current_user_id) continue; 
                // ป้องกันแจ้งเตือนซ้ำคนที่เป็นคู่กรณี (เพราะได้แจ้งในข้อ 1 แล้ว)
                if ($adm['id'] == $target_user_id) continue;

                // [แก้ไข] สร้างข้อความระบุชื่อ "ใคร แลกกับ ใคร"
                $msg = "มีการขอสลับเวรระหว่าง: คุณ $sender_name ⇄ คุณ $target_full_name";
                
                sendNotification($conn, $adm['id'], $msg, "warning");
            }
            mysqli_stmt_close($adm_stmt);
        }

        // ✅ ส่งค่า JSON กลับไปให้ JavaScript
        echo json_encode([
            'success' => true, 
            'message' => 'ส่งคำขอเรียบร้อยแล้ว รอเพื่อนร่วมงานอนุมัติ'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'เกิดข้อผิดพลาดฐานข้อมูล: ' . mysqli_error($conn)
        ]);
    }
    mysqli_stmt_close($ins);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request Method']);
}

mysqli_close($conn);
?>