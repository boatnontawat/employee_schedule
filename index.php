<?php
include 'config.php';

// ถ้าล็อกอินอยู่แล้ว ให้ redirect ไปยังหน้า dashboard ที่เหมาะสม
if (isLoggedIn()) {
    $level = $_SESSION['user_level'];
    if ($level == 'super_admin') {
        header("location: ward_created.php");
    } elseif ($level == 'admin') {
        header("location: admin_dashboard.php");
    } else {
        header("location: user_dashboard.php");
    }
    exit;
} else {
    header("location: login.php");
    exit;
}
?>