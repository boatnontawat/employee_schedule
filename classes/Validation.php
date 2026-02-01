<?php
class Validation {
    
    // ตรวจสอบอีเมล
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // ตรวจสอบเบอร์โทร
    public static function validatePhone($phone) {
        return preg_match('/^[0-9]{10,15}$/', $phone);
    }
    
    // ตรวจสอบวันที่
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    // ตรวจสอบว่าเป็นวันที่ในอนาคต
    public static function isFutureDate($date) {
        $inputDate = new DateTime($date);
        $today = new DateTime();
        return $inputDate > $today;
    }
    
    // ตรวจสอบช่วงวันที่
    public static function validateDateRange($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        return $start <= $end;
    }
    
    // ตรวจสอบชื่อผู้ใช้
    public static function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username);
    }
    
    // ตรวจสอบชื่อ-นามสกุล
    public static function validateFullName($name) {
        return preg_match('/^[a-zA-Zก-๙\s]{2,100}$/u', $name);
    }
}
?>