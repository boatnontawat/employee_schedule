<?php
include 'config.php';

if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// รับพารามิเตอร์ (กรณีคลิกมาจากหน้าปฏิทิน)
$prefill_date = $_GET['date'] ?? '';
$prefill_shift = $_GET['shift'] ?? '';

// ดึงเวรของตัวเอง (เฉพาะวันที่ปัจจุบันหรืออนาคต)
$my_schedules_sql = "SELECT s.id, s.schedule_date, s.shift_type 
                     FROM schedules s 
                     WHERE s.user_id = ? 
                     AND s.schedule_date >= CURDATE()
                     ORDER BY s.schedule_date";
$my_schedules_stmt = mysqli_prepare($conn, $my_schedules_sql);
mysqli_stmt_bind_param($my_schedules_stmt, "i", $user_id);
mysqli_stmt_execute($my_schedules_stmt);
$my_schedules_result = mysqli_stmt_get_result($my_schedules_stmt);

// Helper function (PHP) - แปลงชื่อเวรเป็นไทย
function getShiftNameThai($shift) {
    $map = [
        'morning' => 'เช้า', 
        'afternoon' => 'บ่าย', 
        'night' => 'ดึก', 
        'day' => 'Day', 
        'night_shift' => 'Night',
        'morning_afternoon' => 'เช้า-บ่าย',
        'morning_night' => 'เช้า-ดึก',
        'afternoon_night' => 'บ่าย-ดึก'
    ];
    return $map[$shift] ?? $shift;
}

// --- ส่วนจัดการ Submit Form ---
$sweet_alert = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $original_schedule_id = sanitizeInput($conn, $_POST['original_schedule_id']);
    $target_user_id = sanitizeInput($conn, $_POST['target_user_id']);
    $target_date_selected = sanitizeInput($conn, $_POST['target_date']); // รับวันที่ที่เลือกใหม่
    $reason = sanitizeInput($conn, $_POST['reason']);
    
    // 1. ตรวจสอบเวรต้นทาง (ของเรา)
    $chk_sql = "SELECT schedule_date, shift_type FROM schedules WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $chk_sql);
    mysqli_stmt_bind_param($stmt, "ii", $original_schedule_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $s_date, $my_shift_type);
    
    if(mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        
        // 2. หาเวรปลายทาง (target_schedule_id) โดยใช้วันที่ที่เลือกมาใหม่ ($target_date_selected)
        $t_sql = "SELECT id, shift_type FROM schedules WHERE user_id = ? AND schedule_date = ?";
        $t_stmt = mysqli_prepare($conn, $t_sql);
        mysqli_stmt_bind_param($t_stmt, "is", $target_user_id, $target_date_selected);
        mysqli_stmt_execute($t_stmt);
        mysqli_stmt_bind_result($t_stmt, $target_schedule_id, $target_shift_type);
        
        if(mysqli_stmt_fetch($t_stmt)) {
            mysqli_stmt_close($t_stmt);
            
            // 3. Validation
            // กรณีวันเดียวกัน ห้ามแลกกะเดียวกัน
            if ($s_date == $target_date_selected && $my_shift_type == $target_shift_type) {
                $sweet_alert = ['icon' => 'warning', 'title' => 'ไม่สามารถสลับได้', 'text' => 'ในวันเดียวกัน คุณและเพื่อนร่วมงานมีเวรประเภทเดียวกันอยู่แล้ว (' . getShiftNameThai($my_shift_type) . ')'];
            } else {
                // 4. ตรวจสอบคำขอซ้ำ
                $dup_sql = "SELECT id FROM swap_requests WHERE original_schedule_id = ? AND target_schedule_id = ? AND status = 'pending'";
                $dup_stmt = mysqli_prepare($conn, $dup_sql);
                mysqli_stmt_bind_param($dup_stmt, "ii", $original_schedule_id, $target_schedule_id);
                mysqli_stmt_execute($dup_stmt);
                mysqli_stmt_store_result($dup_stmt);
                
                if (mysqli_stmt_num_rows($dup_stmt) > 0) {
                     $sweet_alert = ['icon' => 'info', 'title' => 'มีคำขออยู่แล้ว', 'text' => 'คุณได้ส่งคำขอสลับเวรรายการนี้ไปแล้ว กรุณารอการอนุมัติ'];
                     mysqli_stmt_close($dup_stmt);
                } else {
                    mysqli_stmt_close($dup_stmt);

                    // 5. บันทึกคำขอ
                    $ins_sql = "INSERT INTO swap_requests (user_id, target_user_id, original_schedule_id, target_schedule_id, reason, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
                    $ins = mysqli_prepare($conn, $ins_sql);
                    mysqli_stmt_bind_param($ins, "iiiss", $user_id, $target_user_id, $original_schedule_id, $target_schedule_id, $reason);
                    
                    if(mysqli_stmt_execute($ins)) {
                        // แจ้งเตือน Admin
                        $admin_notification_sql = "SELECT id FROM users WHERE department_id = ? AND (level = 'admin' OR level = 'super_admin')";
                        $admin_stmt = mysqli_prepare($conn, $admin_notification_sql);
                        mysqli_stmt_bind_param($admin_stmt, "i", $_SESSION['department_id']);
                        mysqli_stmt_execute($admin_stmt);
                        mysqli_stmt_bind_result($admin_stmt, $admin_id);
                        
                        $admin_ids = [];
                        while (mysqli_stmt_fetch($admin_stmt)) { $admin_ids[] = $admin_id; }
                        mysqli_stmt_close($admin_stmt);
                        
                        foreach ($admin_ids as $aid) {
                            if (function_exists('sendNotification')) {
                                sendNotification($conn, $aid, "มีคำขอสลับเวรใหม่จาก {$_SESSION['full_name']} ที่รอการอนุมัติ", 'warning');
                            }
                        }
                        
                         $sweet_alert = [
                             'icon' => 'success', 
                             'title' => 'ส่งคำขอเรียบร้อย', 
                             'text' => 'ระบบได้ส่งคำขอไปยังเพื่อนร่วมงานและผู้ดูแลแล้ว'
                         ];
                    } else {
                        $sweet_alert = ['icon' => 'error', 'title' => 'ผิดพลาด', 'text' => 'Database Error: ' . mysqli_error($conn)]; 
                    }
                    mysqli_stmt_close($ins);
                }
            }
        } else { 
            $sweet_alert = ['icon' => 'warning', 'title' => 'ไม่พบข้อมูล', 'text' => 'เพื่อนร่วมงานไม่มีเวรในวันที่เลือก (' . date('d/m/Y', strtotime($target_date_selected)) . ')']; 
        }
    } else { 
        $sweet_alert = ['icon' => 'error', 'title' => 'ไม่พบข้อมูล', 'text' => 'ไม่พบเวรต้นทางของคุณ หรือคุณไม่มีสิทธิ์แก้ไข']; 
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ขอสลับเวร</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .request-form { background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: 20px; }
        .info-box { display: none; background: #e3f2fd; border-left: 5px solid #2196f3; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .swal2-popup { font-family: 'Sarabun', sans-serif !important; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?></span>
                <a href="user_dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> กลับ</a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="user_dashboard.php"><i class="fas fa-home"></i>แดชบอร์ด</a></li>
                    <li><a href="incoming_swaps.php"><i class="fas fa-inbox"></i> คำขอรออนุมัติ</a></li>
                    <li><a href="request_leave.php"><i class="fas fa-stethoscope"></i>ขอลาป่วย</a></li>
                    <li><a href="request_swap.php" class="active"><i class="fas fa-exchange-alt"></i>ขอสลับเวร</a></li>
                    <li><a href="request_holiday.php"><i class="fas fa-umbrella-beach"></i>ขอวันหยุด</a></li>
                    <li><a href="request_future_holiday.php"><i class="fas fa-calendar-plus"></i>ขอลาหยุดล่วงหน้า</a></li>
                    <li><a href="my_requests.php"><i class="fas fa-history"></i>ประวัติคำขอ</a></li>
                </ul>
            </nav>
            
            <main class="content-area">
                <div class="request-form">
                    <h2><i class="fas fa-exchange-alt"></i> ยื่นคำขอสลับเวร</h2>
                    
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo (isset($security) && method_exists($security, 'generateCSRFToken')) ? $security->generateCSRFToken() : ''; ?>">
                        
                        <div class="form-group">
                            <label>1. เลือกเวรของคุณ *</label>
                            <select id="original_schedule_id" name="original_schedule_id" class="form-control" required>
                                <option value="">-- เลือกเวร --</option>
                                <?php while($s = mysqli_fetch_assoc($my_schedules_result)): ?>
                                    <option value="<?php echo $s['id']; ?>" 
                                        data-date="<?php echo $s['schedule_date']; ?>"
                                        data-shift="<?php echo $s['shift_type']; ?>"
                                        <?php echo ($prefill_date == $s['schedule_date'] && $prefill_shift == $s['shift_type']) ? 'selected' : ''; ?>>
                                        <?php echo date('d/m/Y', strtotime($s['schedule_date'])); ?> (<?php echo getShiftNameThai($s['shift_type']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>2. เลือกวันที่ต้องการแลก (ของเพื่อนร่วมงาน) *</label>
                            <input type="date" id="target_date" name="target_date" class="form-control" required disabled>
                            <small class="text-muted">สามารถเปลี่ยนวันที่ได้ หากต้องการสลับข้ามวัน</small>
                        </div>

                        <div id="schedule-info" class="info-box">
                            <p><strong>เวรของคุณ:</strong> <span id="info-date">-</span> <span id="info-shift" style="color:red; font-weight:bold;">-</span></p>
                            <p><strong>เวรที่จะแลก:</strong> <span id="info-target-date">-</span> <span id="info-target-shift" style="color:green; font-weight:bold;">-</span></p>
                        </div>
                        
                        <div class="form-group">
                            <label>3. เลือกเพื่อนร่วมงาน *</label>
                            <select id="target_user_id" name="target_user_id" class="form-control" required disabled>
                                <option value="">-- กรุณาเลือกวันที่ก่อน --</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>เหตุผล (ไม่บังคับ)</label>
                            <textarea name="reason" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-block">ส่งคำขอ</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    const scheduleSelect = document.getElementById('original_schedule_id');
    const targetDateInput = document.getElementById('target_date');
    const targetSelect = document.getElementById('target_user_id');
    const infoBox = document.getElementById('schedule-info');
    
    // Mapping ชื่อเวรภาษาไทยใน JS
    const shiftMap = {
        'morning': 'เช้า', 'afternoon': 'บ่าย', 'night': 'ดึก',
        'day': 'Day', 'night_shift': 'Night',
        'morning_afternoon': 'เช้า-บ่าย', 'morning_night': 'เช้า-ดึก', 'afternoon_night': 'บ่าย-ดึก'
    };

    function getShiftThaiJS(shift) {
        return shiftMap[shift] || shift;
    }

    function formatDateThai(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleDateString('th-TH');
    }

    // เมื่อเลือกเวรของตัวเอง
    scheduleSelect.addEventListener('change', function() {
        if (!this.value) {
            targetDateInput.disabled = true;
            targetDateInput.value = '';
            targetSelect.innerHTML = '<option value="">-- กรุณาเลือกเวรของคุณก่อน --</option>';
            targetSelect.disabled = true;
            infoBox.style.display = 'none';
            return;
        }

        const opt = this.options[this.selectedIndex];
        const date = opt.dataset.date;
        const shift = opt.dataset.shift;

        // เปิดให้เลือกวันที่ โดย Default เป็นวันที่เดียวกับเวรเรา
        targetDateInput.disabled = false;
        targetDateInput.value = date;

        // อัพเดต Info Box ฝั่งเรา
        infoBox.style.display = 'block';
        document.getElementById('info-date').textContent = formatDateThai(date);
        document.getElementById('info-shift').textContent = '(' + getShiftThaiJS(shift) + ')';
        
        // โหลดเพื่อนร่วมงานทันที
        loadTargetUsers(date, shift);
    });

    // เมื่อเปลี่ยนวันที่เป้าหมาย
    targetDateInput.addEventListener('change', function() {
        const opt = scheduleSelect.options[scheduleSelect.selectedIndex];
        const myShift = opt.dataset.shift;
        loadTargetUsers(this.value, myShift);
    });

    // ฟังก์ชันโหลดข้อมูลเพื่อนร่วมงาน
    function loadTargetUsers(targetDate, myShift) {
        // อัพเดตวันที่ใน Info Box
        document.getElementById('info-target-date').textContent = formatDateThai(targetDate);
        document.getElementById('info-target-shift').textContent = '-';

        targetSelect.innerHTML = '<option>กำลังโหลด...</option>';
        targetSelect.disabled = true;

        fetch(`api/get_swappable_users.php?date=${targetDate}&my_shift=${myShift}`)
            .then(res => res.json())
            .then(data => {
                targetSelect.innerHTML = '<option value="">-- เลือกเพื่อนร่วมงาน --</option>';
                
                if (data.length === 0) {
                    targetSelect.innerHTML += '<option disabled>ไม่พบเพื่อนร่วมงานที่มีเวรในวันนี้</option>';
                }
                
                data.forEach(u => {
                    const shiftLabel = getShiftThaiJS(u.shift_type); // ใช้ JS แปลง หรือใช้ u.shift_label จาก API ก็ได้
                    targetSelect.innerHTML += `<option value="${u.id}" data-tshift="${shiftLabel}">${u.full_name} (${shiftLabel})</option>`;
                });
                targetSelect.disabled = false;
            });
    }

    targetSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if(opt.dataset.tshift) {
            document.getElementById('info-target-shift').textContent = '(' + opt.dataset.tshift + ')';
        } else {
            document.getElementById('info-target-shift').textContent = '-';
        }
    });

    // Run once on load if pre-selected
    if (scheduleSelect.value) scheduleSelect.dispatchEvent(new Event('change'));

    // --- ส่วนแสดง Popup เมื่อส่งข้อมูลเสร็จ ---
    <?php if ($sweet_alert): ?>
        Swal.fire({
            icon: '<?php echo $sweet_alert['icon']; ?>',
            title: '<?php echo $sweet_alert['title']; ?>',
            text: '<?php echo $sweet_alert['text']; ?>',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed && '<?php echo $sweet_alert['icon']; ?>' === 'success') {
                window.location.href = 'my_requests.php';
            }
        });
    <?php endif; ?>
    </script>
</body>
</html>