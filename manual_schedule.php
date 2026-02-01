<?php
include 'config.php';

if (!isLoggedIn() || $_SESSION['user_level'] != 'admin') {
    header("location: login.php");
    exit;
}

$department_id = $_SESSION['department_id'];
// แก้ไข: ดักค่า null ให้เป็นค่าว่างถ้าไม่มีการส่งมา
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
list($selected_year, $selected_month_num) = explode('-', $selected_month);

// --- HELPER FUNCTION ---
if (!function_exists('getShiftTypeThaiShort')) {
    function getShiftTypeThaiShort($shift) {
        $mapping = [
            'morning' => 'เช้า', 'afternoon' => 'บ่าย', 'night' => 'ดึก',
            'day' => 'D', 'night_shift' => 'N',
            'morning_afternoon' => 'ช-บ', 'morning_night' => 'ช-ด', 'afternoon_night' => 'บ-ด'
        ];
        // ตรวจสอบว่า $shift ไม่ใช่ null
        return $mapping[$shift ?? ''] ?? ($shift ?? '-');
    }
}

// --- Handle Form Submissions ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. เพิ่มเวรใหม่
    if (isset($_POST['add_schedule'])) {
        $user_id = sanitizeInput($conn, $_POST['user_id']);
        $schedule_date = sanitizeInput($conn, $_POST['schedule_date']);
        $shift_type = sanitizeInput($conn, $_POST['shift_type']);
        
        $check_sql = "SELECT id FROM schedules WHERE user_id = ? AND schedule_date = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $schedule_date);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_fetch($stmt)) {
            $error_msg = "พนักงานรายนี้มีเวรในวันนี้แล้ว";
        } else {
            mysqli_stmt_close($stmt);
            $sql = "INSERT INTO schedules (user_id, department_id, schedule_date, shift_type) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiss", $user_id, $department_id, $schedule_date, $shift_type);
            if(mysqli_stmt_execute($stmt)) $success_msg = "เพิ่มเวรสำเร็จ";
        }
        if (isset($stmt) && $stmt instanceof mysqli_stmt) mysqli_stmt_close($stmt);
    }

    // 2. เปลี่ยนตัว (Replace) - *** แก้ไขจุดนี้ ***
    if (isset($_POST['replace_user_submit'])) {
        $schedule_id = intval($_POST['schedule_id']);
        $new_user_id = intval($_POST['new_user_id']);
        $current_date = $_POST['current_date']; // รับค่าวันที่จาก Hidden Input

        if ($new_user_id && $schedule_id && $current_date) {
            
            // 2.1 ตรวจสอบก่อนว่าคนใหม่ (new_user_id) มีเวรในวันนั้น (current_date) หรือยัง?
            // โดยต้องไม่นับรายการเดิมที่กำลังจะแก้นี้ (เผื่อกรณีแปลกๆ แต่กันไว้ก่อน)
            $check_dup_sql = "SELECT id FROM schedules WHERE user_id = ? AND schedule_date = ? AND id != ?";
            $check_stmt = mysqli_prepare($conn, $check_dup_sql);
            mysqli_stmt_bind_param($check_stmt, "isi", $new_user_id, $current_date, $schedule_id);
            mysqli_stmt_execute($check_stmt);
            
            if (mysqli_stmt_fetch($check_stmt)) {
                // ถ้าเจอข้อมูล แสดงว่าคนนี้มีเวรแล้ว -> ห้ามเปลี่ยน
                $error_msg = "ไม่สามารถเปลี่ยนคนได้: พนักงานรายนี้มีเวรอื่นในวันนี้อยู่แล้ว";
            } else {
                // ถ้าไม่เจอ -> ยอมให้เปลี่ยนได้
                mysqli_stmt_close($check_stmt); // ปิด check_stmt ก่อน

                $sql = "UPDATE schedules SET user_id = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $new_user_id, $schedule_id);
                if(mysqli_stmt_execute($stmt)) {
                    $success_msg = "เปลี่ยนคนเข้าเวรเรียบร้อยแล้ว";
                }
                mysqli_stmt_close($stmt);
            }
            if (isset($check_stmt) && $check_stmt instanceof mysqli_stmt) mysqli_stmt_close($check_stmt);
        } else {
            $error_msg = "ข้อมูลไม่ครบถ้วน (กรุณาลองใหม่อีกครั้ง)";
        }
    }

    // 3. ลบเวร (Delete)
    if (isset($_POST['delete_schedule_submit'])) {
        $schedule_id = intval($_POST['schedule_id']);
        $sql = "DELETE FROM schedules WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
        if(mysqli_stmt_execute($stmt)) {
            $success_msg = "ลบเวรเรียบร้อยแล้ว";
        }
        mysqli_stmt_close($stmt);
    }
}

// --- Fetch Schedules ---
$schedules_by_date = [];
$sql = "SELECT s.id, s.schedule_date, s.shift_type, u.id as user_id, u.full_name 
        FROM schedules s JOIN users u ON s.user_id = u.id 
        WHERE s.department_id = ? AND MONTH(s.schedule_date) = ? AND YEAR(s.schedule_date) = ?
        ORDER BY FIELD(s.shift_type, 'morning', 'afternoon', 'night', 'day', 'night_shift', 'morning_afternoon', 'morning_night', 'afternoon_night')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $department_id, $selected_month_num, $selected_year);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $schedules_by_date[$row['schedule_date']][] = $row;
}

// --- Fetch Leaves (Conflicts) ---
$approved_leaves = [];
$leave_sql = "SELECT user_id, start_date, end_date, request_type FROM future_leave_requests 
              WHERE department_id = ? AND status = 'approved' AND (MONTH(start_date) = ? OR MONTH(end_date) = ?)";
$stmt = mysqli_prepare($conn, $leave_sql);
mysqli_stmt_bind_param($stmt, "iii", $department_id, $selected_month_num, $selected_month_num);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $approved_leaves[$row['user_id']][] = $row;
}

// Helper: Check Conflict
function checkConflict($user_id, $date, $leaves) {
    if (isset($leaves[$user_id])) {
        foreach ($leaves[$user_id] as $l) {
            if ($date >= $l['start_date'] && $date <= $l['end_date']) return $l['request_type'];
        }
    }
    return false;
}

// Helper: Fetch All Staff for Add Modal
$all_staff_sql = "SELECT id, full_name FROM users WHERE department_id = ? AND is_active = 1 ORDER BY full_name";
$staff_stmt = mysqli_prepare($conn, $all_staff_sql);
mysqli_stmt_bind_param($staff_stmt, "i", $department_id);
mysqli_stmt_execute($staff_stmt);
$staff_res = mysqli_stmt_get_result($staff_stmt);

// ดึงข้อมูลแผนก
$dept_sql = "SELECT name FROM departments WHERE id = ?";
$dept_stmt = mysqli_prepare($conn, $dept_sql);
mysqli_stmt_bind_param($dept_stmt, "i", $department_id);
mysqli_stmt_execute($dept_stmt);
mysqli_stmt_bind_result($dept_stmt, $dept_name);
mysqli_stmt_fetch($dept_stmt);
mysqli_stmt_close($dept_stmt);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขตารางเวร</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS Same as Random Schedule Theme */
        .modal { display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .modal-dialog { position: relative; width: auto; margin: 10vh auto; pointer-events: none; max-width: 500px; }
        .modal-content { position: relative; display: flex; flex-direction: column; width: 100%; pointer-events: auto; background-color: #fff; background-clip: padding-box; border: 1px solid rgba(0,0,0,.2); border-radius: 1rem; outline: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1); animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from {transform: translateY(-30px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6; }
        .modal-body { padding: 1.5rem; }
        .close { font-size: 1.5rem; font-weight: 700; opacity: .5; cursor: pointer; border: none; background: none; }
        
        .fab-btn { position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background: var(--success); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); cursor: pointer; transition: transform 0.2s; z-index: 1040; }
        .fab-btn:hover { transform: scale(1.1); }

        .schedule-item.conflict { background: var(--danger) !important; border: 2px solid #fff; animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(231, 74, 59, 0); } 100% { box-shadow: 0 0 0 0 rgba(231, 74, 59, 0); } }
        
        .calendar-grid .schedule-item { cursor: pointer; }
        .calendar-grid .schedule-item:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?> (Admin)</span>
                <span>แผนก: <?php echo htmlspecialchars($dept_name ?? ''); ?></span>
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="admin_dashboard.php"><i class="fas fa-home"></i>แดชบอร์ด</a></li>
                    <li><a href="user_management.php"><i class="fas fa-users"></i>จัดการพนักงาน</a></li>
                    <li><a href="schedule_rules.php"><i class="fas fa-cog"></i>กำหนดระเบียบเวร</a></li>
                    <li><a href="random_schedule.php"><i class="fas fa-random"></i>สุ่มเวรอัตโนมัติ</a></li>
                    <li><a href="manual_schedule.php" class="active"><i class="fas fa-edit"></i>แก้ไขตารางเวร</a></li>
                    <li><a href="approve_requests.php"><i class="fas fa-check-circle"></i>อนุมัติคำขอ</a></li>
                    <li><a href="report_management.php"><i class="fas fa-chart-bar"></i>รายงาน</a></li>
                </ul>
            </nav>
            
            <main class="content-area">
                <div class="page-header">
                    <h2><i class="fas fa-edit"></i> แก้ไขตารางเวร</h2>
                    <p>จัดการตารางเวรและตรวจสอบข้อขัดแย้ง (พนักงานลา)</p>
                </div>

                <?php if (isset($success_msg)) echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> $success_msg</div>"; ?>
                <?php if (isset($error_msg)) echo "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> $error_msg</div>"; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <form method="get" class="month-selector" style="display:flex; gap:10px; align-items: center;">
                            <label for="month" style="margin:0; font-weight:600;">เลือกเดือน:</label>
                            <input type="month" id="month" name="month" class="form-control" value="<?php echo $selected_month; ?>" onchange="this.form.submit()" style="width: auto;">
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <div class="calendar-grid">
                            <div class="calendar-header">อาทิตย์</div>
                            <div class="calendar-header">จันทร์</div>
                            <div class="calendar-header">อังคาร</div>
                            <div class="calendar-header">พุธ</div>
                            <div class="calendar-header">พฤหัสบดี</div>
                            <div class="calendar-header">ศุกร์</div>
                            <div class="calendar-header">เสาร์</div>
                            
                            <?php
                            $first_day = "$selected_year-$selected_month_num-01";
                            $total_days = date('t', strtotime($first_day));
                            $start_day_of_week = date('w', strtotime($first_day));
                            
                            for ($i = 0; $i < $start_day_of_week; $i++) echo "<div class='calendar-day empty'></div>";
                            
                            for ($day = 1; $day <= $total_days; $day++) {
                                $current_date = sprintf("%s-%s-%02d", $selected_year, $selected_month_num, $day);
                                $today_schedules = $schedules_by_date[$current_date] ?? [];
                                $is_today = ($current_date == date('Y-m-d')) ? 'today' : '';
                                
                                echo "<div class='calendar-day $is_today'>";
                                echo "<div class='calendar-date'>$day</div>";
                                
                                foreach ($today_schedules as $sched) {
                                    $conflict = checkConflict($sched['user_id'], $current_date, $approved_leaves);
                                    $conflict_class = $conflict ? 'conflict' : '';
                                    
                                    // เรียกใช้ Helper Function
                                    $shift_short = getShiftTypeThaiShort($sched['shift_type']);
                                    
                                    // Escape string สำหรับใส่ใน HTML Attribute
                                    $safe_fullname = htmlspecialchars($sched['full_name'], ENT_QUOTES);
                                    
                                    echo "<div class='schedule-item {$sched['shift_type']} $conflict_class' 
                                               onclick='openEditModal({$sched['id']}, \"{$safe_fullname}\", \"$current_date\", \"$shift_short\")'
                                               title='คลิกเพื่อแก้ไข'>
                                            <span style='overflow: hidden; text-overflow: ellipsis; white-space: nowrap;'>{$sched['full_name']}</span>
                                            <span style='background:rgba(255,255,255,0.2); padding:1px 6px; border-radius:10px; font-size:0.75rem; margin-left: 5px;'>$shift_short</span>
                                          </div>";
                                }
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="fab-btn" onclick="document.getElementById('addModal').style.display='block'" title="เพิ่มเวรใหม่">
                    <i class="fas fa-plus"></i>
                </div>

            </main>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit"></i> จัดการเวร</h5>
                    <button type="button" class="close" onclick="closeModal('editModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>วันที่:</strong> <span id="modalDate" style="color: var(--primary-blue); font-weight: 600;"></span></p>
                    <p><strong>คนปัจจุบัน:</strong> <span id="modalCurrentName" style="color: var(--danger); font-weight: 600;"></span></p>
                    
                    <form method="post">
                        <input type="hidden" name="schedule_id" id="modalScheduleId">
                        <input type="hidden" name="current_date" id="modalDateVal">
                        
                        <div class="form-group">
                            <label for="availableUserSelect" style="font-weight:600;">เปลี่ยนเป็น (เฉพาะคนที่ว่าง):</label>
                            <select name="new_user_id" id="availableUserSelect" class="form-control" required>
                                <option value="">-- กำลังโหลด... --</option>
                            </select>
                        </div>

                        <div style="margin-top: 25px; display:flex; gap:10px;">
                            <button type="submit" name="delete_schedule_submit" class="btn btn-danger" style="flex:1;" onclick="return confirm('ยืนยันลบเวรนี้?')">
                                <i class="fas fa-trash-alt"></i> ลบเวร
                            </button>
                            <button type="submit" name="replace_user_submit" class="btn btn-primary" style="flex:2;">
                                <i class="fas fa-save"></i> บันทึกการเปลี่ยน
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: var(--success);"><i class="fas fa-plus-circle"></i> เพิ่มเวรใหม่</h5>
                    <button type="button" class="close" onclick="closeModal('addModal')">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="form-group">
                            <label>วันที่ <span class="text-danger">*</span></label>
                            <input type="date" name="schedule_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>พนักงาน <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- เลือกพนักงาน --</option>
                                <?php 
                                mysqli_data_seek($staff_res, 0);
                                while($u = mysqli_fetch_assoc($staff_res)) echo "<option value='{$u['id']}'>{$u['full_name']}</option>"; 
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>กะทำงาน <span class="text-danger">*</span></label>
                            <select name="shift_type" class="form-control" required>
                                <option value="morning">เวรเช้า</option>
                                <option value="afternoon">เวรบ่าย</option>
                                <option value="night">เวรดึก</option>
                                <option value="day">เวรเดย์</option>
                                <option value="night_shift">เวรไนท์</option>
                                <option value="morning_afternoon">เวรเช้าบ่าย</option>
                                <option value="morning_night">เวรเช้าดึก</option>
                                <option value="afternoon_night">เวรบ่ายดึก</option>
                            </select>
                        </div>
                        <button type="submit" name="add_schedule" class="btn btn-success btn-block" style="margin-top:15px;">
                            <i class="fas fa-check"></i> บันทึก
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function openEditModal(scheduleId, currentName, dateStr, shiftName) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('modalScheduleId').value = scheduleId;
        document.getElementById('modalCurrentName').innerText = currentName + ' (' + shiftName + ')';
        
        const dateObj = new Date(dateStr);
        const thaiDate = dateObj.toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('modalDate').innerText = thaiDate;
        document.getElementById('modalDateVal').value = dateStr;
        
        const select = document.getElementById('availableUserSelect');
        select.innerHTML = '<option value="">-- กำลังค้นหาคนว่าง... --</option>';
        select.disabled = true;
        
        fetch(`api/get_available_users.php?date=${dateStr}&schedule_id=${scheduleId}`)
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">-- เลือกคนแทน (ว่าง) --</option>';
                if(data.length === 0) {
                    select.innerHTML += '<option disabled>ไม่มีคนว่างเลย</option>';
                } else {
                    data.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.text = user.name;
                        select.appendChild(option);
                    });
                }
                select.disabled = false;
            })
            .catch(err => {
                select.innerHTML = '<option value="">ไม่สามารถโหลดข้อมูลได้</option>';
                console.error(err);
                select.disabled = false;
            });
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
    </script>
</body>
</html>
<?php 
if (isset($stmt) && $stmt instanceof mysqli_stmt) mysqli_stmt_close($stmt);
mysqli_close($conn); 
?>