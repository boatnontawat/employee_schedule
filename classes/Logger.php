<?php
class Logger {
    private $logPath;
    private $db;
    
    public function __construct($db = null) {
        $this->logPath = __DIR__ . '/../logs/';
        $this->db = $db;
        
        // สร้างโฟลเดอร์ logs ถ้ายังไม่มี
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }
    
    // บันทึก Log ลงไฟล์
    private function writeToFile($filename, $message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents($this->logPath . $filename, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    // บันทึก Log ลงฐานข้อมูล
    private function writeToDatabase($data) {
        if (!$this->db) return false;
        
        $sql = "INSERT INTO audit_logs (user_id, action_type, action_description, ip_address, user_agent, affected_table, affected_id, old_values, new_values, severity) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "isssssisss", 
            $data['user_id'],
            $data['action_type'],
            $data['action_description'],
            $data['ip_address'],
            $data['user_agent'],
            $data['affected_table'],
            $data['affected_id'],
            $data['old_values'],
            $data['new_values'],
            $data['severity']
        );
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // บันทึกการกระทำของ User
    public function logUserAction($action, $description, $userId = null, $table = null, $recordId = null, $oldData = null, $newData = null, $severity = 'low') {
        $ip = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // บันทึกลงไฟล์
        $fileMessage = "USER_ACTION [{$action}] User: {$userId} - {$description} - IP: {$ip}";
        $this->writeToFile('user_actions.log', $fileMessage);
        
        // บันทึกลงฐานข้อมูล
        $dbData = [
            'user_id' => $userId,
            'action_type' => $action,
            'action_description' => $description,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'affected_table' => $table,
            'affected_id' => $recordId,
            'old_values' => $oldData ? json_encode($oldData) : null,
            'new_values' => $newData ? json_encode($newData) : null,
            'severity' => $severity
        ];
        
        $this->writeToDatabase($dbData);
    }
    
    // บันทึกเหตุการณ์ความปลอดภัย
    public function logSecurityEvent($eventType, $description, $severity = 'medium') {
        $ip = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // บันทึกลงไฟล์
        $fileMessage = "SECURITY [{$eventType}] {$description} - IP: {$ip} - Severity: {$severity}";
        $this->writeToFile('security.log', $fileMessage);
        
        // บันทึกลงฐานข้อมูล
        if ($this->db) {
            $sql = "INSERT INTO security_events (event_type, description, ip_address, user_agent, severity) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $eventType, $description, $ip, $userAgent, $severity);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    // บันทึก Error
    public function logError($message, $file = '', $line = '') {
        $errorMessage = "ERROR: {$message}";
        if ($file && $line) {
            $errorMessage .= " in {$file} on line {$line}";
        }
        $this->writeToFile('error.log', $errorMessage);
    }
    
    // บันทึก System Log
    public function logSystem($message) {
        $this->writeToFile('system.log', "SYSTEM: {$message}");
    }
    
    // ฟังก์ชันช่วยเหลือ - ดึง IP Address
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}
?>