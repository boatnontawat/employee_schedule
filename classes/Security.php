<?php
class Security {
    private $db;
    private $logger;
    private $settings;
    
    public function __construct($db) {
        $this->db = $db;
        $this->logger = new Logger($db);
        $this->loadSecuritySettings();
        $this->cleanupOldRecords();
    }
    
    // โหลดการตั้งค่าความปลอดภัย
    private function loadSecuritySettings() {
        $this->settings = [];
        
        $sql = "SELECT setting_key, setting_value FROM security_settings";
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            error_log("Failed to load security settings: " . mysqli_error($this->db));
            $this->loadDefaultSettings();
            return;
        }
        
        while ($row = mysqli_fetch_assoc($result)) {
            $this->settings[$row['setting_key']] = $row['setting_value'];
        }
        
        mysqli_free_result($result);
        $this->loadDefaultSettings();
    }
    
    // โหลดการตั้งค่า default
    private function loadDefaultSettings() {
        $defaults = [
            'enable_brute_force_protection' => '1',
            'max_login_attempts' => '5',
            'lockout_duration' => '30',
            'password_min_length' => '8',
            'password_require_uppercase' => '1',
            'password_require_lowercase' => '1',
            'password_require_numbers' => '1',
            'password_require_special_chars' => '1',
            'password_expiry_days' => '90',
            'enable_rate_limiting' => '1',
            'max_requests_per_minute' => '60'
        ];
        
        foreach ($defaults as $key => $value) {
            if (!isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
    }
    
    // ตรวจสอบ Brute Force
    public function checkBruteForce($username, $ip) {
        if (!$this->settings['enable_brute_force_protection']) {
            return true;
        }
        
        $maxAttempts = (int)$this->settings['max_login_attempts'];
        $lockoutDuration = (int)$this->settings['lockout_duration'];
        
        $sql = "SELECT COUNT(*) as attempts 
                FROM failed_login_attempts 
                WHERE (username = ? OR ip_address = ?) 
                AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)";
        
        $stmt = mysqli_prepare($this->db, $sql);
        if (!$stmt) {
            error_log("Failed to prepare statement: " . mysqli_error($this->db));
            return false;
        }
        
        // ตรวจสอบและกำหนดค่า default หากเป็น null
        $username = $username ?? '';
        $ip = $ip ?? '';
        
        mysqli_stmt_bind_param($stmt, "ssi", $username, $ip, $lockoutDuration);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute statement: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
        
        mysqli_stmt_bind_result($stmt, $attempts);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        if ($attempts >= $maxAttempts) {
            $this->logger->logSecurityEvent(
                'brute_force_detected', 
                "Brute force detected for username: {$username} from IP: {$ip} - Attempts: {$attempts}", 
                'high'
            );
            return false;
        }
        
        return true;
    }
    
    // บันทึกการล็อกอินผิด
    public function recordFailedLogin($username, $ip) {
        $sql = "INSERT INTO failed_login_attempts (username, ip_address, user_agent) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $sql);
        
        if (!$stmt) {
            error_log("Failed to prepare statement for recordFailedLogin: " . mysqli_error($this->db));
            return false;
        }
        
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // ตรวจสอบและกำหนดค่า default หากเป็น null
        $username = $username ?? '';
        $ip = $ip ?? '';
        
        mysqli_stmt_bind_param($stmt, "sss", $username, $ip, $userAgent);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute statement for recordFailedLogin: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
        
        mysqli_stmt_close($stmt);
        
        $this->logger->logSecurityEvent(
            'failed_login', 
            "Failed login attempt for username: {$username} from IP: {$ip}", 
            'medium'
        );
        
        return true;
    }
    
    // ล้างประวัติการล็อกอินผิด
    public function clearFailedAttempts($username, $ip) {
        $sql = "DELETE FROM failed_login_attempts WHERE username = ? OR ip_address = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        
        if (!$stmt) {
            error_log("Failed to prepare statement for clearFailedAttempts: " . mysqli_error($this->db));
            return false;
        }
        
        // ตรวจสอบและกำหนดค่า default หากเป็น null
        $username = $username ?? '';
        $ip = $ip ?? '';
        
        mysqli_stmt_bind_param($stmt, "ss", $username, $ip);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute statement for clearFailedAttempts: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
        
        mysqli_stmt_close($stmt);
        return true;
    }
    
    // ตรวจสอบ Rate Limiting
    public function checkRateLimit($identifier, $maxRequests = null, $timeWindow = 60) {
        if (!$this->settings['enable_rate_limiting']) {
            return true;
        }
        
        if ($maxRequests === null) {
            $maxRequests = (int)$this->settings['max_requests_per_minute'];
        }
        
        $sql = "SELECT COUNT(*) as requests 
                FROM rate_limits 
                WHERE identifier = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";
        
        $stmt = mysqli_prepare($this->db, $sql);
        if (!$stmt) {
            error_log("Failed to prepare statement for checkRateLimit: " . mysqli_error($this->db));
            return false;
        }
        
        // ตรวจสอบและกำหนดค่า default หากเป็น null
        $identifier = $identifier ?? '';
        
        mysqli_stmt_bind_param($stmt, "si", $identifier, $timeWindow);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute statement for checkRateLimit: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
        
        mysqli_stmt_bind_result($stmt, $requests);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        if ($requests >= $maxRequests) {
            $this->logger->logSecurityEvent(
                'rate_limit_exceeded',
                "Rate limit exceeded for: {$identifier} - Requests: {$requests}/{$maxRequests} per {$timeWindow}s",
                'medium'
            );
            return false;
        }
        
        // Record this request
        $sql = "INSERT INTO rate_limits (identifier, user_agent, ip_address) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $sql);
        
        if ($stmt) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            mysqli_stmt_bind_param($stmt, "sss", $identifier, $userAgent, $ipAddress);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        return true;
    }
    
    // ตรวจสอบความแข็งแรงของรหัสผ่าน
    public function validatePassword($password) {
        $errors = [];
        
        $minLength = (int)($this->settings['password_min_length'] ?? 8);
        if (strlen($password) < $minLength) {
            $errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย {$minLength} ตัวอักษร";
        }
        
        if ($this->settings['password_require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "รหัสผ่านต้องมีตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว";
        }
        
        if ($this->settings['password_require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = "รหัสผ่านต้องมีตัวพิมพ์เล็กอย่างน้อย 1 ตัว";
        }
        
        if ($this->settings['password_require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = "รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว";
        }
        
        if ($this->settings['password_require_special_chars'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "รหัสผ่านต้องมีอักขระพิเศษอย่างน้อย 1 ตัว";
        }
        
        // ตรวจสอบรหัสผ่านที่พบบ่อย
        if ($this->isCommonPassword($password)) {
            $errors[] = "รหัสผ่านนี้ถูกใช้บ่อยเกินไป กรุณาใช้รหัสผ่านที่แข็งแกร่งกว่า";
        }
        
        return $errors;
    }
    
    // ตรวจสอบรหัสผ่านที่พบบ่อย
    private function isCommonPassword($password) {
        $commonPasswords = [
            '123456', 'password', '12345678', 'qwerty', '123456789', 
            '12345', '1234', '111111', '1234567', 'dragon'
        ];
        return in_array(strtolower($password), $commonPasswords);
    }
    
    // ตรวจสอบอายุรหัสผ่าน
    public function isPasswordExpired($passwordChangedAt) {
        $expiryDays = (int)($this->settings['password_expiry_days'] ?? 90);
        if ($expiryDays <= 0) return false;
        
        $expiryDate = date('Y-m-d H:i:s', strtotime("-$expiryDays days"));
        return strtotime($passwordChangedAt) < strtotime($expiryDate);
    }
    
    // 🔥 NEW: Method สำหรับดึงค่าการตั้งค่า
    public function getMaxLoginAttempts() {
        return (int)($this->settings['max_login_attempts'] ?? 5);
    }
    
    public function getLockoutDuration() {
        return (int)($this->settings['lockout_duration'] ?? 30);
    }
    
    public function getPasswordMinLength() {
        return (int)($this->settings['password_min_length'] ?? 8);
    }
    
    public function isBruteForceProtectionEnabled() {
        return (bool)($this->settings['enable_brute_force_protection'] ?? true);
    }
    
    // ตั้งค่า Session Security
    public function secureSession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            
            session_regenerate_id(true);
        }
    }
    
    // สร้าง CSRF Token
    public function generateCSRFToken($formName = 'default') {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }
        
        $_SESSION['csrf_tokens'][$formName] = [
            'token' => bin2hex(random_bytes(32)),
            'expires' => time() + 3600
        ];
        
        return $_SESSION['csrf_tokens'][$formName]['token'];
    }
    
    // ตรวจสอบ CSRF Token
    public function verifyCSRFToken($token, $formName = 'default') {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_tokens'][$formName])) {
            $this->logger->logSecurityEvent('csrf_attempt', 'CSRF token not found for form: ' . $formName, 'high');
            return false;
        }
        
        $csrfData = $_SESSION['csrf_tokens'][$formName];
        unset($_SESSION['csrf_tokens'][$formName]);
        
        if (time() > $csrfData['expires'] || !hash_equals($csrfData['token'], $token)) {
            $this->logger->logSecurityEvent('csrf_attempt', 'CSRF token validation failed for form: ' . $formName, 'high');
            return false;
        }
        
        return true;
    }
    
    // Sanitize Input - แก้ไขให้รองรับ null
    public function sanitizeInput($input) {
        if ($input === null) {
            return '';
        }
        
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }
    
    // ตรวจสอบ XSS - แก้ไขให้รองรับ null
    public function detectXSS($input) {
        if ($input === null) {
            return false;
        }
        
        if (is_array($input)) {
            foreach ($input as $value) {
                if ($this->detectXSS($value)) {
                    return true;
                }
            }
            return false;
        }
        
        $xssPatterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/on\w+\s*=/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/expression\s*\(/i',
            '/<iframe\b[^>]*>/i',
            '/<object\b[^>]*>/i',
            '/<embed\b[^>]*>/i',
            '/data:/i'
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $this->logger->logSecurityEvent(
                    'xss_attempt', 
                    'XSS attempt detected: ' . substr($input, 0, 100), 
                    'high'
                );
                return true;
            }
        }
        
        return false;
    }
    
    // ตรวจสอบอีเมล - แก้ไขให้รองรับ null
    public function validateEmail($email) {
        if ($email === null) {
            return false;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        $domain = substr($email, strpos($email, '@') + 1);
        return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
    }
    
    // ตรวจสอบ IP Address - แก้ไขให้รองรับ null
    public function validateIP($ip) {
        if ($ip === null) {
            return false;
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
    
    // Sanitize filename - แก้ไขให้รองรับ null
    public function sanitizeFileName($filename) {
        if ($filename === null) {
            return '';
        }
        
        $filename = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $filename);
        return basename($filename);
    }
    
    // สร้างรหัสผ่านแบบสุ่ม
    public function generateRandomPassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
        $password = '';
        $charLength = strlen($chars) - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $charLength)];
        }
        
        return $password;
    }
    
    // ทำความสะอาด records เก่า
    public function cleanupOldRecords() {
        // ลบ records การล็อกอินที่เก่ากว่า 30 วัน
        $sql = "DELETE FROM failed_login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        mysqli_query($this->db, $sql);
        
        // ลบ rate limit records ที่เก่ากว่า 1 วัน
        $sql = "DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)";
        mysqli_query($this->db, $sql);
    }
    
    // ดึงการตั้งค่าความปลอดภัย
    public function getSettings() {
        return $this->settings;
    }
    
    // อัพเดทการตั้งค่าความปลอดภัย
    public function updateSetting($key, $value) {
        $sql = "REPLACE INTO security_settings (setting_key, setting_value) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->db, $sql);
        
        if (!$stmt) {
            error_log("Failed to prepare statement for updateSetting: " . mysqli_error($this->db));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "ss", $key, $value);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute statement for updateSetting: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
        
        mysqli_stmt_close($stmt);
        
        $this->settings[$key] = $value;
        return true;
    }
}
?>