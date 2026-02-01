<?php
// ไฟล์: api_mobile/login.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include '../config.php'; // เรียกใช้ db connection ตัวเดิม

// รับค่าที่ส่งมาจากแอป (แอปจะส่งมาเป็น JSON)
$json = file_get_contents("php://input");
$data = json_decode($json, true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if(empty($username) || empty($password)){
    echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูล"]);
    exit;
}

// เช็ค User ในฐานข้อมูล (Logic เดิมของคุณ)
$sql = "SELECT id, full_name, password, level FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    // ล็อกอินผ่าน! ส่งข้อมูล user กลับไป (ห้ามส่ง password กลับไปนะ)
    echo json_encode([
        "success" => true,
        "message" => "ยินดีต้อนรับ " . $user['full_name'],
        "user_data" => [
            "id" => $user['id'],
            "name" => $user['full_name'],
            "level" => $user['level']
        ]
    ]);
} else {
    // ล็อกอินไม่ผ่าน
    echo json_encode(["success" => false, "message" => "ชื่อผู้ใช้หรือรหัสผ่านผิด"]);
}
?>