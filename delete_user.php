<?php
session_start();
include 'config.php';

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 'admin') {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $department_id = $_SESSION['department_id'];
    $id = $_POST['user_id'];

    // ลบข้อมูล
    $sql = "DELETE FROM users WHERE id=? AND department_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id, $department_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $msg = "success_delete";
    } else {
        $msg = "error_delete";
    }
    mysqli_stmt_close($stmt);

    header("location: user_management.php?status=" . $msg);
    exit;
} else {
    header("location: user_management.php");
    exit;
}
?>