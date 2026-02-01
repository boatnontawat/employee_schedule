<?php
// ไฟล์: api/get_swappable_users.php
include '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode([]);
    exit;
}

$department_id = $_SESSION['department_id'];
$my_user_id = $_SESSION['user_id'];
$date = $_GET['date'] ?? '';
$my_shift = $_GET['my_shift'] ?? '';

if (!$date || !$my_shift) {
    echo json_encode([]);
    exit;
}

// Query ดึงพนักงานคนอื่น ที่มีเวรในวันนั้น และกะ "ไม่ตรง" กับเรา
// (Logic: Same Day Swap แต่ห้ามกะซ้ำ)
$sql = "SELECT u.id, u.full_name, s.shift_type 
        FROM schedules s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.department_id = ? 
        AND s.schedule_date = ? 
        AND s.user_id != ?        -- ไม่เอาตัวเอง
        AND s.shift_type != ?     -- ไม่เอากะที่เหมือนเรา (Duplicate Shift)
        ORDER BY u.full_name";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isis", $department_id, $date, $my_user_id, $my_shift);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    // แปลงชื่อกะเป็นภาษาไทยเพื่อให้ดูง่าย
    $shift_map = ['morning'=>'เช้า', 'afternoon'=>'บ่าย', 'night'=>'ดึก', 'day'=>'Day', 'night_shift'=>'Night'];
    $shift_th = $shift_map[$row['shift_type']] ?? $row['shift_type'];
    
    $users[] = [
        'id' => $row['id'],
        'full_name' => $row['full_name'],
        'shift_type' => $row['shift_type'],
        'shift_label' => $shift_th
    ];
}

echo json_encode($users);
mysqli_close($conn);
?>