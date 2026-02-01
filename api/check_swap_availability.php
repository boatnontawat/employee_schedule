<?php
// ไฟล์: api/check_swap_availability.php
include '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['canSwap' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$original_date = sanitizeInput($conn, $_GET['original_date']);
$target_date = sanitizeInput($conn, $_GET['target_date']);
$target_user_id = sanitizeInput($conn, $_GET['target_user_id']);
$target_shift = sanitizeInput($conn, $_GET['target_shift'] ?? ''); // รับค่ากะเป้าหมายมาด้วย
$my_shift = sanitizeInput($conn, $_GET['my_shift'] ?? ''); // รับค่ากะของเรามาด้วย

// 1. ตรวจสอบกรณีสลับข้ามวัน (Different Day)
if ($original_date != $target_date) {
    // เงื่อนไข: เราต้องไม่มีเวรอยู่ในวันนั้นๆ ถึงจะสลับได้
    $check_sql = "SELECT COUNT(*) FROM schedules WHERE user_id = ? AND schedule_date = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $target_date);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($count > 0) {
        echo json_encode(['canSwap' => false, 'message' => 'ไม่สามารถสลับได้: คุณมีเวรในวันที่ปลายทางอยู่แล้ว']);
        exit;
    }
} 
// 2. ตรวจสอบกรณีสลับวันเดียวกัน (Same Day)
else {
    // เงื่อนไข: ต้องไม่ตรงกับเวรเดิมของเรา (เช้า แลก เช้า ไม่ได้)
    if ($my_shift == $target_shift) {
        echo json_encode(['canSwap' => false, 'message' => 'ไม่สามารถสลับได้: กะการทำงานเหมือนกัน']);
        exit;
    }
    
    // (Optional) ตรวจสอบว่าเรามีกะนี้อยู่แล้วหรือยังในวันนั้น (กันการสลับแล้วได้กะซ้ำ)
    $check_dup_sql = "SELECT COUNT(*) FROM schedules WHERE user_id = ? AND schedule_date = ? AND shift_type = ?";
    $stmt = mysqli_prepare($conn, $check_dup_sql);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $original_date, $target_shift);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $dup_count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($dup_count > 0) {
        echo json_encode(['canSwap' => false, 'message' => 'คุณมีกะนี้อยู่ในวันนี้แล้ว']);
        exit;
    }
}

// ถ้าผ่านทุกเงื่อนไข
echo json_encode(['canSwap' => true, 'message' => 'สามารถสลับเวรได้']);
mysqli_close($conn);
?>