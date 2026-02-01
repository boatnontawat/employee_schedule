<?php
// edit_user_action.php
session_start();
include 'config.php';

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 'admin') {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $department_id = $_SESSION['department_id'];
    $id = $_POST['user_id'];
    
    // ฟังก์ชัน sanitizeInput ควรอยู่ใน config.php ถ้าไม่มีให้ใช้ mysqli_real_escape_string แทน
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $emp_level = mysqli_real_escape_string($conn, $_POST['employee_level']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $sql = "UPDATE users SET full_name=?, level=?, employee_level=?, is_active=? WHERE id=? AND department_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssiiii", $full_name, $level, $emp_level, $is_active, $id, $department_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // ถ้ามีการเปลี่ยนรหัสผ่าน
        if (!empty($_POST['password'])) {
            $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$new_pass' WHERE id=$id");
        }
        $msg = "success_edit";
    } else {
        $msg = "error";
    }
    mysqli_stmt_close($stmt);
    
    // ส่งกลับไปหน้าเดิมพร้อมสถานะ
    header("location: user_management.php?status=" . $msg);
    exit;
} else {
    header("location: user_management.php");
    exit;
}
?>