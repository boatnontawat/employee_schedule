<?php
// ไฟล์: api/get_available_users.php
header('Content-Type: application/json');
include '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$department_id = $_SESSION['department_id'];
$date = $_GET['date'] ?? '';
$current_schedule_id = $_GET['schedule_id'] ?? 0;

if (!$date) {
    echo json_encode([]);
    exit;
}

// 1. ดึง User ทั้งหมดในแผนก
$all_users = [];
$sql = "SELECT id, full_name FROM users WHERE department_id = ? AND is_active = 1 ORDER BY full_name";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $department_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $all_users[$row['id']] = $row['full_name'];
}
mysqli_stmt_close($stmt);

// 2. หาคนที่ติดเวรแล้วในวันนี้ (ยกเว้น ID ของรายการที่เรากำลังจะแก้)
$busy_users = [];
$sql_sched = "SELECT user_id FROM schedules WHERE department_id = ? AND schedule_date = ? AND id != ?";
$stmt = mysqli_prepare($conn, $sql_sched);
mysqli_stmt_bind_param($stmt, "isi", $department_id, $date, $current_schedule_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $busy_users[] = $row['user_id'];
}
mysqli_stmt_close($stmt);

// 3. หาคนที่ลา (Approved) ในวันนี้ (ทั้งลาป่วยและลาล่วงหน้า)
$sql_leave = "SELECT user_id FROM future_leave_requests 
              WHERE department_id = ? AND status = 'approved' AND ? BETWEEN start_date AND end_date
              UNION
              SELECT user_id FROM leave_requests 
              WHERE department_id = ? AND status = 'approved' AND ? BETWEEN start_date AND end_date";
$stmt = mysqli_prepare($conn, $sql_leave);
mysqli_stmt_bind_param($stmt, "isis", $department_id, $date, $department_id, $date);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $busy_users[] = $row['user_id'];
}
mysqli_stmt_close($stmt);

// 4. กรองเหลือแค่คนที่ว่าง
$available_users = [];
foreach ($all_users as $id => $name) {
    if (!in_array($id, $busy_users)) {
        $available_users[] = ['id' => $id, 'name' => $name];
    }
}

echo json_encode($available_users);
?>