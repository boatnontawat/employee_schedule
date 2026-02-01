<?php
// config.php

// 1. Error Reporting (Turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Constants & Settings (Add the missing constant here)
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'employee_schedule');
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('LOG_PATH', __DIR__ . '/logs/');
define('ENABLE_CSRF', true);
// Fix: Explicitly define this constant
define('ENABLE_SECURE_SESSION', true); 

// 3. Secure Session Start
if (session_status() === PHP_SESSION_NONE) {
    if (defined('ENABLE_SECURE_SESSION') && ENABLE_SECURE_SESSION) {
        ini_set('session.cookie_httponly', 1);
        // Only enable cookie_secure if using HTTPS (optional check)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', 1); 
        }
        ini_set('session.use_strict_mode', 1);
    }
    session_start();
}

// 4. Database Connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
date_default_timezone_set('Asia/Bangkok');

// 5. Include Classes
require_once __DIR__ . '/classes/Logger.php';
require_once __DIR__ . '/classes/Security.php';
require_once __DIR__ . '/classes/Validation.php';
$logger = new Logger($conn);
$security = new Security($conn);

// ----------------------------------------------------------------------
// 6. Global Helper Functions
// ----------------------------------------------------------------------

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['last_activity']);
}

function checkSessionTimeout() {
    global $logger;
    $timeout = 60 * 60; // 1 hour
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        if(isset($logger)) {
            $logger->logUserAction('session_timeout', 'Session expired due to inactivity', $_SESSION['user_id'] ?? null);
        }
        session_unset();
        session_destroy();
        header("location: login.php?error=session_expired");
        exit;
    }
    $_SESSION['last_activity'] = time();
}

if (isLoggedIn()) {
    checkSessionTimeout();
}

function getShiftTypeThai($shift_type) {
    $mapping = [
        'morning' => 'เวรเช้า',
        'afternoon' => 'เวรบ่าย', 
        'night' => 'เวรดึก',
        'day' => 'เวรเดย์',
        'night_shift' => 'เวรไนท์',
        'morning_afternoon' => 'เวรเช้าบ่าย',
        'morning_night' => 'เวรเช้าดึก',
        'afternoon_night' => 'เวรบ่ายดึก'
    ];
    return $mapping[$shift_type] ?? $shift_type;
}

function getShiftTypeThaiShort($shift_type) {
    $mapping = [
        'morning' => 'เช้า',
        'afternoon' => 'บ่าย',
        'night' => 'ดึก',
        'day' => 'เดย์',
        'night_shift' => 'ไนท์',
        'morning_afternoon' => 'ช/บ',
        'morning_night' => 'ช/ด',
        'afternoon_night' => 'บ/ด'
    ];
    return $mapping[$shift_type] ?? $shift_type;
}

function month_to_thai($month_num) {
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    return $months[(int)$month_num] ?? 'ไม่ระบุเดือน';
}

function getShiftCssClass($shift_type) {
    return $shift_type; 
}

function sanitizeInput($conn, $input) {
    global $security;
    if ($input === null) return '';
    if (!is_string($input)) return '';
    
    if (isset($security)) {
        return mysqli_real_escape_string($conn, $security->sanitizeInput($input));
    }
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8'));
}

function uploadFile($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']) {
    global $logger;
    
    if (!isset($file['name']) || $file['error'] !== 0) {
        return ['success' => false, 'message' => 'Upload failed or no file selected'];
    }
    
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > 5000000) { 
        return ['success' => false, 'message' => 'File too large (Max 5MB)'];
    }
    
    $new_file_name = uniqid('', true) . '.' . $file_ext;
    
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $new_file_name)) {
        return ['success' => true, 'file_path' => 'uploads/' . $new_file_name];
    } else {
        if(isset($logger)) $logger->logError("Move file failed: " . $file['name']);
        return ['success' => false, 'message' => 'Failed to save file'];
    }
}

function sendNotification($conn, $user_id, $message, $type = 'info') {
    global $logger;
    if (empty($user_id) || empty($message)) return false;
    
    $sql = "INSERT INTO notifications (user_id, message, type, is_read, created_at) VALUES (?, ?, ?, 0, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        if(isset($logger)) $logger->logError("Notification prepare failed: " . mysqli_error($conn));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $message, $type);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function customErrorHandler($errno, $errstr, $errfile, $errline) {
    global $logger;
    
    // ป้องกันลูปนรก: ถ้า Error เกิดขึ้นในไฟล์ Logger เอง ให้ข้ามไปเลย
    if (strpos($errfile, 'Logger.php') !== false) {
        return false;
    }

    if (isset($logger)) {
        // กรอง Error ที่ไม่จำเป็น และใช้ @ เพื่อระงับ error ที่เกิดจากการเขียน log เอง
        if (strpos($errstr, 'include') === false && strpos($errstr, 'require') === false) {
            @$logger->logError($errstr, $errfile, $errline);
        }
    }
    return false; 
}

set_error_handler("customErrorHandler");
?>