<?php
session_start();

$db_host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$db_user = '282VmfzaNWZswLE.root';
$db_pass = '38ZlZyuoeY9tWGZ3';
$db_name = 'test';
$db_port = 4000;

// 2. คงค่า Config เดิมไว้
define('UPLOAD_PATH', 'uploads/');
define('LOG_PATH', 'logs/');
define('ENABLE_CSRF', true);
define('ENABLE_XSS_PROTECTION', true);

// 3. เชื่อมต่อฐานข้อมูลแบบมี SSL
$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

$connect_result = mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);

if (!$connect_result) {
    die("Connection failed: " . mysqli_connect_error() . " (Errno: " . mysqli_connect_errno() . ")");
}

// Include Classes
require_once 'classes/Logger.php';
require_once 'classes/Security.php';
require_once 'classes/Validation.php';

// Initialize Classes
$logger = new Logger($conn);
$security = new Security($conn);

// ตั้งค่า Timezone
date_default_timezone_set('Asia/Bangkok');

// ฟังก์ชันจัดการ CSRF Token
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

// ฟังก์ชันตรวจสอบการล็อกอิน
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['last_activity']);
}

// ฟังก์ชันตรวจสอบ Session Timeout
function checkSessionTimeout() {
    global $security, $logger;
    
    $timeout = 60 * 60; // 1 hour
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        $logger->logUserAction('session_timeout', 'Session expired due to inactivity', $_SESSION['user_id'] ?? null);
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

// ฟังก์ชันตรวจสอบระดับผู้ใช้
function checkPermission($required_level) {
    if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != $required_level) {
        header("location: login.php");
        exit;
    }
}

// ฟังก์ชันแปลงชื่อเวรเป็นภาษาไทย
function getShiftTypeThai($shiftType) {
    $shiftMap = [
        'morning' => 'เช้า',
        'afternoon' => 'บ่าย', 
        'night' => 'ดึก',
        'day' => 'เดย์',
        'night_shift' => 'ไนท์',
        'morning_afternoon' => 'เช้าบ่าย',
        'morning_night' => 'เช้าดึก',
        'afternoon_night' => 'บ่ายดึก'
    ];
    return $shiftMap[$shiftType] ?? $shiftType;
}

function getShiftTypeThaiShort($shiftType) {
    $shiftMap = [
        'morning' => 'เช้า',
        'afternoon' => 'บ่าย', 
        'night' => 'ดึก',
        'day' => 'เดย์',
        'night_shift' => 'ไนท์',
        'morning_afternoon' => 'ช-บ',
        'morning_night' => 'ช-ด',
        'afternoon_night' => 'บ-ด'
    ];
    return $shiftMap[$shiftType] ?? $shiftType;
}

// ฟังก์ชันป้องกัน SQL Injection
function sanitizeInput($conn, $input) {
    global $security;
    
    if (!is_string($input)) {
        return '';
    }
    
    if (ENABLE_XSS_PROTECTION && $security->detectXSS($input)) {
        return '';
    }
    
    $input = $security->sanitizeInput($input);
    return mysqli_real_escape_string($conn, $input);
}

// ฟังก์ชันอัพโหลดไฟล์
function uploadFile($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']) {
    global $logger;
    
    if (!isset($file['name']) || !isset($file['tmp_name']) || !isset($file['size']) || !isset($file['error'])) {
        $logger->logSystem("Invalid file array structure");
        return ['success' => false, 'message' => 'โครงสร้างข้อมูลไฟล์ไม่ถูกต้อง'];
    }
    
    $file_name = $file['name'] ?? '';
    $file_tmp = $file['tmp_name'] ?? '';
    $file_size = $file['size'] ?? 0;
    $file_error = $file['error'] ?? 0;
    
    if (empty($file_name) || empty($file_tmp)) {
        return ['success' => false, 'message' => 'ไม่มีไฟล์ถูกอัพโหลด'];
    }
    
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if ($file_error !== 0) {
        $logger->logSystem("File upload error: {$file_error} for file: {$file_name}");
        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์'];
    }
    
    if (!in_array($file_ext, $allowed_types)) {
        $logger->logSecurityEvent('invalid_file_type', "Attempt to upload invalid file type: {$file_ext}", 'medium');
        return ['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง'];
    }
    
    if ($file_size > 5000000) { // 5MB
        $logger->logSecurityEvent('file_size_exceeded', "Attempt to upload large file: {$file_name} ({$file_size} bytes)", 'medium');
        return ['success' => false, 'message' => 'ไฟล์มีขนาดใหญ่เกินไป'];
    }
    
    $new_file_name = uniqid('', true) . '.' . $file_ext;
    $file_destination = UPLOAD_PATH . $new_file_name;
    
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0777, true);
    }
    
    if (move_uploaded_file($file_tmp, $file_destination)) {
        $logger->logSystem("File uploaded successfully: {$file_name} as {$new_file_name}");
        return ['success' => true, 'file_path' => $file_destination];
    } else {
        $logger->logError("Failed to move uploaded file: {$file_name}");
        return ['success' => false, 'message' => 'ไม่สามารถอัพโหลดไฟล์ได้'];
    }
}

// ฟังก์ชันส่งการแจ้งเตือน
function sendNotification($conn, $user_id, $message, $type = 'info') {
    global $logger;
    
    if (empty($user_id) || empty($message)) {
        $logger->logError("Invalid parameters for sendNotification: user_id={$user_id}, message={$message}");
        return false;
    }
    
    $sql = "INSERT INTO notifications (user_id, message, type, is_read) VALUES (?, ?, ?, 0)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        $logger->logError("Failed to prepare statement for sendNotification: " . mysqli_error($conn));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $message, $type);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($result) {
        $logger->logUserAction('notification_sent', "Notification sent to user {$user_id}: {$message}", $user_id);
    } else {
        $logger->logError("Failed to send notification to user {$user_id}: " . mysqli_error($conn));
    }
    
    return $result;
}

if (ENABLE_CSRF) {
    generateCSRFToken();
}

function errorHandler($errno, $errstr, $errfile, $errline) {
    global $logger;
    if (isset($logger)) {
        $logger->logError($errstr, $errfile, $errline);
    }
    if (ini_get('display_errors')) {
        echo "<b>Error:</b> [$errno] $errstr<br>";
        echo "Error on line $errline in $errfile<br>";
    }
    return true;
}

set_error_handler("errorHandler");

function exceptionHandler($exception) {
    global $logger;
    if (isset($logger)) {
        $logger->logError($exception->getMessage(), $exception->getFile(), $exception->getLine());
    }
    if (ini_get('display_errors')) {
        echo "Uncaught exception: " . $exception->getMessage() . "<br>";
        echo "Stack trace: " . $exception->getTraceAsString();
    }
    exit(1);
}

set_exception_handler("exceptionHandler");

function getSafeValue($value, $default = '') {
    return $value ?? $default;
}

function getArrayValue($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

function cleanString($str) {
    if (!is_string($str)) {
        return '';
    }
    return trim(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
}
