<?php
// ไฟล์: api/respond_swap.php
include '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) { 
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); 
    exit; 
}

$user_id = $_SESSION['user_id']; // User B (ผู้อนุมัติ)
$request_id = $_POST['request_id'] ?? 0;
$action = $_POST['action'] ?? '';

if (!$request_id || !in_array($action, ['accept', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// 1. ตรวจสอบคำขอ
$check_sql = "SELECT * FROM swap_requests WHERE id = ? AND target_user_id = ? AND status = 'pending'";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $request_id, $user_id);
mysqli_stmt_execute($check_stmt);
$req_result = mysqli_stmt_get_result($check_stmt);
$request_data = mysqli_fetch_assoc($req_result);

if (!$request_data) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบคำขอ หรือคำขอถูกดำเนินการไปแล้ว']);
    exit;
}

$requester_id = $request_data['user_id']; // User A

if ($action === 'reject') {
    // --- กรณีปฏิเสธ ---
    $update_sql = "UPDATE swap_requests SET status = 'rejected' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "i", $request_id);
    
    if (mysqli_stmt_execute($stmt)) {
        if (function_exists('sendNotification')) {
            sendNotification($conn, $requester_id, "คำขอสลับเวรของคุณถูกปฏิเสธโดยเพื่อนร่วมงาน", 'danger');
        }
        echo json_encode(['success' => true, 'message' => 'ปฏิเสธคำขอเรียบร้อย']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }

} else {
    // --- กรณีตอบรับ (Accept) ---
    mysqli_begin_transaction($conn);
    try {
        // 1. อัพเดตสถานะ
        $upd_req = mysqli_query($conn, "UPDATE swap_requests SET status = 'approved', approved_by = $user_id WHERE id = $request_id");
        
        // 2. สลับเจ้าของเวร
        $swap1 = mysqli_query($conn, "UPDATE schedules SET user_id = {$request_data['target_user_id']} WHERE id = {$request_data['original_schedule_id']}");
        $swap2 = mysqli_query($conn, "UPDATE schedules SET user_id = {$request_data['user_id']} WHERE id = {$request_data['target_schedule_id']}");
        
        // 3. บันทึก History
        $hist_sql = "INSERT INTO swap_history (request_id, user1_id, user2_id, original_schedule_id, target_schedule_id, swap_date) 
                     VALUES (?, ?, ?, ?, ?, CURDATE())";
        $hist_stmt = mysqli_prepare($conn, $hist_sql);
        mysqli_stmt_bind_param($hist_stmt, "iiiii", $request_id, $requester_id, $request_data['target_user_id'], $request_data['original_schedule_id'], $request_data['target_schedule_id']);
        mysqli_stmt_execute($hist_stmt);

        if ($upd_req && $swap1 && $swap2) {
            mysqli_commit($conn);
            
            // 4. แจ้งเตือน (ดึงชื่อ-สกุล มาใส่ในข้อความ)
            if (function_exists('sendNotification')) {
                // แจ้งคนขอ
                sendNotification($conn, $requester_id, "คำขอสลับเวรได้รับการอนุมัติแล้ว!", 'success');
                
                // ดึงชื่อ User A และ User B
                $names_sql = "SELECT id, full_name FROM users WHERE id IN (?, ?)";
                $n_stmt = mysqli_prepare($conn, $names_sql);
                mysqli_stmt_bind_param($n_stmt, "ii", $requester_id, $user_id);
                mysqli_stmt_execute($n_stmt);
                $n_res = mysqli_stmt_get_result($n_stmt);
                
                $names = [];
                while($r = mysqli_fetch_assoc($n_res)) {
                    $names[$r['id']] = $r['full_name'];
                }
                
                $nameA = $names[$requester_id] ?? 'Unknown';
                $nameB = $names[$user_id] ?? 'Unknown';

                // แจ้ง Admin (ระบุชื่อ)
                $dept_id = $_SESSION['department_id'];
                $admin_sql = "SELECT id FROM users WHERE department_id = ? AND level = 'admin'";
                $adm_stmt = mysqli_prepare($conn, $admin_sql);
                mysqli_stmt_bind_param($adm_stmt, "i", $dept_id);
                mysqli_stmt_execute($adm_stmt);
                $adm_res = mysqli_stmt_get_result($adm_stmt);
                
                // ข้อความแจ้งเตือนแบบระบุชื่อ
                $msg = "มีการสลับเวรระหว่าง: คุณ $nameA และ คุณ $nameB (อนุมัติกันเอง)";
                
                while($adm = mysqli_fetch_assoc($adm_res)) {
                    sendNotification($conn, $adm['id'], $msg, 'warning');
                }
            }

            echo json_encode(['success' => true, 'message' => 'สลับเวรสำเร็จเรียบร้อย']);
        } else {
            throw new Exception("Update failed");
        }

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
}

mysqli_close($conn);
?>