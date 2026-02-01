<?php
// ไฟล์: api/check_incoming_swaps.php
include '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) { 
    echo json_encode(['found' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ใช้ LEFT JOIN แทน JOIN ธรรมดา เพื่อกันพลาดกรณี ID เวรหายไป
$sql = "SELECT sr.id, sr.request_id, u.full_name as requester_name, 
        sr.reason, sr.original_schedule_id, sr.target_schedule_id, sr.user_id as requester_id,
        COALESCE(s1.schedule_date, 'ไม่พบวันที่') as my_date, 
        COALESCE(s1.shift_type, '') as my_shift,
        COALESCE(s2.schedule_date, 'ไม่พบวันที่') as their_date, 
        COALESCE(s2.shift_type, '') as their_shift
        FROM swap_requests sr
        LEFT JOIN users u ON sr.user_id = u.id
        LEFT JOIN schedules s1 ON sr.target_schedule_id = s1.id   -- เวรของเรา (User B)
        LEFT JOIN schedules s2 ON sr.original_schedule_id = s2.id -- เวรของเขา (User A)
        WHERE sr.target_user_id = ? AND sr.status = 'pending'
        ORDER BY sr.created_at ASC LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // แปลงชื่อกะเป็นภาษาไทย
    $row['my_shift_th'] = getShiftName($row['my_shift']);
    $row['their_shift_th'] = getShiftName($row['their_shift']);
    
    echo json_encode(['found' => true, 'data' => $row]);
} else {
    echo json_encode(['found' => false]);
}

function getShiftName($shift) {
    if(!$shift) return '-';
    $map = ['morning'=>'เช้า', 'afternoon'=>'บ่าย', 'night'=>'ดึก', 'day'=>'Day', 'night_shift'=>'Night'];
    return $map[$shift] ?? $shift;
}

mysqli_close($conn);
?>