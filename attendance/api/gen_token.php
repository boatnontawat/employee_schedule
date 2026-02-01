<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || $_SESSION['employee_level'] < 3) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// รับค่าพิกัดหัวหน้า (ถ้ามี)
$lat = isset($_POST['lat']) ? $_POST['lat'] : null;
$lng = isset($_POST['lng']) ? $_POST['lng'] : null;

// สร้าง Token สุ่ม
$token = bin2hex(random_bytes(32));

// กำหนดเวลาหมดอายุ (ดึงจาก Setting หรือ Default 60 วิ + เผื่อ Delay 5 วิ)
$refresh_rate = 60; 
// ... (โค้ดดึง qr_refresh_rate_seconds จาก DB) ...
$expires_at = date('Y-m-d H:i:s', time() + $refresh_rate + 5);

$sql = "INSERT INTO active_qr_tokens (token, generated_by, latitude, longitude, created_at, expires_at) 
        VALUES (?, ?, ?, ?, NOW(), ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sidds", $token, $_SESSION['user_id'], $lat, $lng, $expires_at);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'token' => $token]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}
?>