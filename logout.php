<?php
include 'config.php';

if (isLoggedIn()) {
    // บันทึก Log การออกจากระบบ
    $logger->logUserAction(
        'logout', 
        'User logged out', 
        $_SESSION['user_id'],
        'users',
        $_SESSION['user_id'],
        null,
        null,
        'low'
    );
}

// ล้าง session ทั้งหมด
session_unset();
session_destroy();

// ล้าง cookie session (ถ้ามี)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect ไปหน้า login
header("location: login.php");
exit;
?>