<?php
include 'config.php';

if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$department_id = $_SESSION['department_id'];
$user_level = $_SESSION['user_level']; 

// ... (ส่วน Logic รับค่า PHP เหมือนเดิม) ...
// เพื่อประหยัดพื้นที่ ผมขอละส่วน Logic PHP ด้านบนไว้ (เพราะเหมือนเดิม 100%)
// ให้คุณใช้โค้ด PHP ส่วนบนจากไฟล์เดิมได้เลยครับ 
// เน้นเปลี่ยนแค่ส่วน <script> ด้านล่างครับ

// --- [1] รับค่า Tab และ Limit ---
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'calendar-view';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; 
if (!in_array($limit, [10, 25, 50, 100])) $limit = 10;

// --- Logic จัดการวันที่ ---
$ref_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$view_mode = isset($_GET['view']) ? $_GET['view'] : 'week';

$thai_months_short = [1=>'ม.ค.', 2=>'ก.พ.', 3=>'มี.ค.', 4=>'เม.ย.', 5=>'พ.ค.', 6=>'มิ.ย.', 7=>'ก.ค.', 8=>'ส.ค.', 9=>'ก.ย.', 10=>'ต.ค.', 11=>'พ.ย.', 12=>'ธ.ค.'];
$thai_months_full = [1=>'มกราคม', 2=>'กุมภาพันธ์', 3=>'มีนาคม', 4=>'เมษายน', 5=>'พฤษภาคม', 6=>'มิถุนายน', 7=>'กรกฎาคม', 8=>'สิงหาคม', 9=>'กันยายน', 10=>'ตุลาคม', 11=>'พฤศจิกายน', 12=>'ธันวาคม'];

$timeline_start = date('Y-m-01', strtotime($ref_date));
$timeline_end   = date('Y-m-t', strtotime($ref_date));

if ($view_mode == 'month') {
    $start_month = date('Y-m-01', strtotime($ref_date));
    $end_month = date('Y-m-t', strtotime($ref_date));
    $start_cal = (date('w', strtotime($start_month)) == 0) ? $start_month : date('Y-m-d', strtotime('last sunday', strtotime($start_month)));
    $end_cal = (date('w', strtotime($end_month)) == 6) ? $end_month : date('Y-m-d', strtotime('next saturday', strtotime($end_month)));
    $prev_date = date('Y-m-d', strtotime($start_month . ' -1 month'));
    $next_date = date('Y-m-d', strtotime($start_month . ' +1 month'));
    $y_th = date('Y', strtotime($start_month)) + 543;
    $header_title = "ประจำเดือน " . $thai_months_full[date('n', strtotime($start_month))] . " " . $y_th;
    $grid_class = ""; 
    $input_type = "month";
    $input_val = date('Y-m', strtotime($ref_date));
} else {
    $ts = strtotime($ref_date);
    $start_cal = (date('w', $ts) == 0) ? date('Y-m-d', $ts) : date('Y-m-d', strtotime('last sunday', $ts));
    $end_cal = date('Y-m-d', strtotime($start_cal . ' +6 days'));
    $prev_date = date('Y-m-d', strtotime($start_cal . ' -7 days'));
    $next_date = date('Y-m-d', strtotime($start_cal . ' +7 days'));
    $sd = date('j', strtotime($start_cal));
    $sm = $thai_months_short[date('n', strtotime($start_cal))];
    $ed = date('j', strtotime($end_cal));
    $em = $thai_months_short[date('n', strtotime($end_cal))];
    $y_th = date('Y', strtotime($end_cal)) + 543;
    $header_title = "ประจำสัปดาห์ที่ $sd $sm - $ed $em $y_th";
    $grid_class = "weekly-view"; 
    $input_type = "date";
    $input_val = $ref_date;
}

$m_th = $thai_months_full[date('n', strtotime($ref_date))];
$y_th_sub = date('Y', strtotime($ref_date)) + 543;
$thai_month_year = "$m_th $y_th_sub";
$current_year_check = date('Y', strtotime($ref_date));

$query_start = ($start_cal < $timeline_start) ? $start_cal : $timeline_start;
$query_end   = ($end_cal > $timeline_end) ? $end_cal : $timeline_end;

// 1. ดึงตารางเวร
$schedule_sql = "SELECT s.id as schedule_id, s.schedule_date, s.shift_type, u.full_name, u.id as user_id 
                 FROM schedules s 
                 JOIN users u ON s.user_id = u.id 
                 WHERE s.department_id = ? 
                 AND s.schedule_date BETWEEN ? AND ? 
                 ORDER BY s.schedule_date, s.shift_type";
$schedule_stmt = mysqli_prepare($conn, $schedule_sql);
mysqli_stmt_bind_param($schedule_stmt, "iss", $department_id, $query_start, $query_end);
mysqli_stmt_execute($schedule_stmt);
$schedule_result = mysqli_stmt_get_result($schedule_stmt);

$calendar_schedules = []; 
$timeline_map = [];
while($row = mysqli_fetch_assoc($schedule_result)) {
    $calendar_schedules[] = $row; 
    $timeline_map[$row['user_id']][$row['schedule_date']] = $row['shift_type'];
}

// 2. ดึงเพื่อนร่วมงาน
$u_sql = "SELECT id, full_name FROM users WHERE department_id = ? AND is_active = TRUE ORDER BY full_name";
$u_stmt = mysqli_prepare($conn, $u_sql);
mysqli_stmt_bind_param($u_stmt, "i", $department_id);
mysqli_stmt_execute($u_stmt);
$u_res = mysqli_stmt_get_result($u_stmt);

// --- [2] Logic แยกตัวเรา (Sticky) + Pagination ---
$my_user_data = null;
$other_users = [];

while($r = mysqli_fetch_assoc($u_res)) { 
    if ($r['id'] == $user_id) {
        $my_user_data = $r; 
    } else {
        $other_users[] = $r; 
    }
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total_others = count($other_users);
$total_pages = ceil($total_others / $limit);
$offset = ($page - 1) * $limit;

$display_others = array_slice($other_users, $offset, $limit);

$display_users = [];
if ($my_user_data) {
    $display_users[] = $my_user_data;
}
$display_users = array_merge($display_users, $display_others);
// -------------------------------------------

// 3. ดึงวันหยุด
$holidays = [];
$h_sql = "SELECT holiday_date FROM holiday_settings WHERE department_id = ? AND YEAR(holiday_date) = ?";
$h_stmt = mysqli_prepare($conn, $h_sql);
mysqli_stmt_bind_param($h_stmt, "ii", $department_id, $current_year_check);
mysqli_stmt_execute($h_stmt);
$h_res = mysqli_stmt_get_result($h_stmt);
while($r = mysqli_fetch_assoc($h_res)) { $holidays[] = $r['holiday_date']; }

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>แดชบอร์ดผู้ใช้</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mobile-drag-drop@2.3.0-rc.2/default.css">

    <style>
        .schedule-item { transition: all 0.2s; position: relative; z-index: 1; }
        .schedule-item.dragging { opacity: 0.5; }
        
        .schedule-item.drop-zone { 
            border: 2px dashed #e74c3c !important; 
            background-color: #fadbd8 !important; 
            transform: scale(1.05); 
            z-index: 10; 
        }
        
        .schedule-item * { 
            pointer-events: none !important; 
        }
        
        .schedule-item.my-schedule {
            touch-action: none !important;
            cursor: grab;
        }

        .drag-instruction { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; border-radius: 5px; padding: 10px; margin: 10px 0; text-align: center; font-size: 0.9rem; }
        .date-nav-container { display: flex; align-items: center; gap: 10px; background: #f1f5f9; padding: 5px 10px; border-radius: 8px; }
        .date-picker-input { border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px 10px; font-family: 'Sarabun', sans-serif; cursor: pointer; }
        .view-switcher .btn { padding: 5px 12px; font-size: 0.9rem; }
        .view-switcher .btn.active { background-color: var(--primary-blue); color: white; border-color: var(--primary-blue); }
        .view-switcher .btn:not(.active) { background-color: white; color: var(--dark-gray); border: 1px solid #e2e8f0; }
        
        /* แก้ไข Badge */
        .badge-notification { background-color: #ef4444; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: 5px; vertical-align: middle; position: relative; top: -1px; }

        .pagination-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 10px; }
        .limit-selector { display: flex; align-items: center; gap: 5px; font-size: 0.9rem; }
        .limit-selector select { padding: 5px; border-radius: 4px; border: 1px solid #ddd; }
        .page-nav { display: flex; gap: 5px; }
        .page-nav a { padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333; background: #fff; }
        .page-nav a.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .page-nav a.disabled { opacity: 0.5; pointer-events: none; }

        .dnd-poly-drag-image {
            pointer-events: none !important;
            opacity: 0.9 !important;
            transform: scale(1.1);
            z-index: 9999 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?></span>
                <span>แผนก: <?php echo $dept_name; ?></span>
                <a href="logout.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <?php if (isset($_SESSION['user_level']) && ($_SESSION['user_level'] == 'admin' || $_SESSION['user_level'] == 'super_admin')): ?>
                    <li>
                        <a href="admin_dashboard.php" class="btn-admin-back">
                            <i class="fas fa-shield-alt"></i> กลับสู่ระบบจัดการ
                        </a>
                    </li>
                    <?php endif; ?>

                    <li><a href="user_dashboard.php" class="active"><i class="fas fa-home"></i>แดชบอร์ด</a></li>
                    
                    <li>
                        <a href="scan_qr.php" class="btn-scan-menu">
                            <i class="fas fa-camera"></i> สแกนเข้า/ออกงาน
                        </a>
                    </li>

                    <li>
                        <a href="incoming_swaps.php">
                            <i class="fas fa-inbox"></i> คำขอรออนุมัติ
                            <span id="incomingSwapBadge" class="badge-notification" style="display:none;"></span> 
                            </a>
                    </li>
                    
                    <li>
                        <?php 
                        $history_url = 'attendance_history.php?mode=personal'; 
                        ?>
                        <a href="<?php echo $history_url; ?>">
                        <i class="fas fa-history"></i> ประวัติการลงเวลา
                        </a>
                    </li>
                    <li><a href="request_leave.php"><i class="fas fa-stethoscope"></i>ขอลาป่วย</a></li>
                    <li><a href="request_swap.php"><i class="fas fa-exchange-alt"></i>ขอสลับเวร</a></li>
                    <li><a href="request_future_holiday.php"><i class="fas fa-calendar-plus"></i>ขอลาหยุดล่วงหน้า</a></li>
                    <li><a href="my_requests.php"><i class="fas fa-history"></i>ประวัติคำขอ</a></li>
                </ul>
            </nav>
            
            <main class="content-area">
                
                <div class="card shadow-sm mb-4" style="border-radius: 12px; border: none; overflow: visible;">
                    <div class="card-body" style="padding: 20px;">

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0;">
                            <div>
                                <h2 style="margin: 0; font-size: 1.6rem; color: var(--primary-blue); font-weight: bold; display: flex; align-items: center; gap: 10px;">
                                    <i class="far fa-calendar-alt"></i> <?php echo $header_title; ?>
                                </h2>
                                <p style="margin: 5px 0 0 35px; color: #64748b; font-size: 0.95rem;">
                                    ตารางเวรประจำเดือน <?php echo $thai_month_year; ?>
                                </p>
                            </div>

                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <div class="view-switcher btn-group">
                                    <a href="?view=week&date=<?php echo $ref_date; ?>&tab=<?php echo $active_tab; ?>&limit=<?php echo $limit; ?>" class="btn <?php echo ($view_mode == 'week') ? 'active' : ''; ?>">รายสัปดาห์</a>
                                    <a href="?view=month&date=<?php echo $ref_date; ?>&tab=<?php echo $active_tab; ?>&limit=<?php echo $limit; ?>" class="btn <?php echo ($view_mode == 'month') ? 'active' : ''; ?>">รายเดือน</a>
                                </div>

                                <div class="date-nav-container">
                                    <a href="?view=<?php echo $view_mode; ?>&date=<?php echo $prev_date; ?>&tab=<?php echo $active_tab; ?>&limit=<?php echo $limit; ?>" class="btn btn-sm btn-light" style="border:none;"><i class="fas fa-chevron-left"></i></a>
                                    <form method="GET" style="margin:0;">
                                        <input type="hidden" name="view" value="<?php echo $view_mode; ?>">
                                        <input type="hidden" name="tab" value="<?php echo $active_tab; ?>">
                                        <input type="hidden" name="limit" value="<?php echo $limit; ?>">
                                        <input type="<?php echo $input_type; ?>" name="date" class="date-picker-input" 
                                               value="<?php echo $input_val; ?>" onchange="this.form.submit()" 
                                               style="border:none; background:transparent; font-weight:600; color:var(--primary-blue); width: 140px;">
                                    </form>
                                    <a href="?view=<?php echo $view_mode; ?>&date=<?php echo $next_date; ?>&tab=<?php echo $active_tab; ?>&limit=<?php echo $limit; ?>" class="btn btn-sm btn-light" style="border:none;"><i class="fas fa-chevron-right"></i></a>
                                </div>
                                <a href="?view=<?php echo $view_mode; ?>&date=<?php echo date('Y-m-d'); ?>&tab=<?php echo $active_tab; ?>&limit=<?php echo $limit; ?>" class="btn btn-info btn-sm" style="color: white; border-radius: 20px; padding: 5px 15px;">วันนี้</a>
                            </div>
                        </div>

                        <div class="view-tabs" style="margin-bottom: 20px;">
                            <button class="tab-btn <?php echo ($active_tab == 'calendar-view') ? 'active' : ''; ?>" onclick="switchUserTab('calendar-view', this)">
                                <i class="fas fa-table"></i> ปฏิทิน (สลับเวร)
                            </button>
                            <button class="tab-btn <?php echo ($active_tab == 'timeline-view') ? 'active' : ''; ?>" onclick="switchUserTab('timeline-view', this)">
                                <i class="fas fa-list-ul"></i> ตารางรายชื่อ
                            </button>
                        </div>

                        <div id="calendar-view" class="tab-content <?php echo ($active_tab == 'calendar-view') ? 'active' : ''; ?>">
                            <div class="drag-instruction" style="margin-bottom: 20px; border-radius: 8px;">
                                <i class="fas fa-hand-holding-medical"></i> <strong>วิธีใช้:</strong> แตะเวรของคุณ (สีเขียว) ค้างไว้ แล้วลากไปวางทับเวรเพื่อนร่วมงานเพื่อขอสลับ (หากลากไปขอบบน/ล่างของจอ หน้าจอจะเลื่อนอัตโนมัติ)
                            </div>
                            
                            <div class="calendar-grid <?php echo $grid_class; ?>" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                <div class="calendar-header">อาทิตย์</div>
                                <div class="calendar-header">จันทร์</div>
                                <div class="calendar-header">อังคาร</div>
                                <div class="calendar-header">พุธ</div>
                                <div class="calendar-header">พฤหัสบดี</div>
                                <div class="calendar-header">ศุกร์</div>
                                <div class="calendar-header">เสาร์</div>
                                
                                <?php
                                $curr = $start_cal;
                                while($curr <= $end_cal) {
                                    $is_target_month = ($view_mode == 'month') ? (date('m', strtotime($curr)) == date('m', strtotime($ref_date))) : true;
                                    if ($view_mode == 'month' && !$is_target_month) {
                                        echo "<div class='calendar-day empty'></div>";
                                    } else {
                                        $curr_date_str = date('Y-m-d', strtotime($curr));
                                        $holiday_class = in_array($curr_date_str, $holidays) ? ' is-holiday' : '';
                                        $day_style = ($view_mode == 'week') ? ' weekly-day' : '';
                                        
                                        echo "<div class='calendar-day" . $holiday_class . $day_style . "'>";
                                        echo "<div class='calendar-date'>" . date('j', strtotime($curr)) . "</div>";
                                        
                                        foreach($calendar_schedules as $row) {
                                            if($row['schedule_date'] == $curr) {
                                                $is_mine = ($row['user_id'] == $user_id);
                                                $cls = $row['shift_type'] . ($is_mine ? ' my-schedule' : '');
                                                $s_name = function_exists('getShiftTypeThaiShort') ? getShiftTypeThaiShort($row['shift_type']) : $row['shift_type'];
                                                
                                                echo "<div class='schedule-item $cls' draggable='" . ($is_mine ? 'true' : 'false') . "'
                                                      data-id='{$row['schedule_id']}' data-date='{$row['schedule_date']}'
                                                      data-shift='{$row['shift_type']}' data-user='{$row['user_id']}'
                                                      data-name='{$row['full_name']}'>
                                                      <div class='employee-info'>
                                                          <span class='employee-name'>{$row['full_name']}</span>
                                                          <span class='shift-name'>$s_name</span>
                                                      </div></div>";
                                            }
                                        }

                                        echo "</div>";
                                    }
                                    $curr = date('Y-m-d', strtotime($curr . ' +1 day'));
                                }
                                ?>
                            </div>
                        </div>

                        <div id="timeline-view" class="tab-content <?php echo ($active_tab == 'timeline-view') ? 'active' : ''; ?>">
                            <div class="pagination-controls">
                                <form method="GET" class="limit-selector" style="margin:0;">
                                    <input type="hidden" name="view" value="<?php echo $view_mode; ?>">
                                    <input type="hidden" name="date" value="<?php echo $ref_date; ?>">
                                    <input type="hidden" name="tab" value="timeline-view"> <label>แสดง:</label>
                                    <select name="limit" onchange="this.form.submit()">
                                        <option value="10" <?php echo $limit==10?'selected':''; ?>>10</option>
                                        <option value="25" <?php echo $limit==25?'selected':''; ?>>25</option>
                                        <option value="50" <?php echo $limit==50?'selected':''; ?>>50</option>
                                        <option value="100" <?php echo $limit==100?'selected':''; ?>>100</option>
                                    </select>
                                    <span>คน/หน้า</span>
                                </form>
                                
                                <div class="page-nav">
                                    <?php if($page > 1): ?>
                                        <a href="?view=<?php echo $view_mode; ?>&date=<?php echo $ref_date; ?>&limit=<?php echo $limit; ?>&page=<?php echo $page-1; ?>&tab=timeline-view">&laquo; ก่อนหน้า</a>
                                    <?php endif; ?>
                                    
                                    <span style="padding:5px 10px; font-weight:bold; color:var(--text-muted);">
                                        หน้า <?php echo $page; ?> / <?php echo $total_pages; ?>
                                    </span>
                                    
                                    <?php if($page < $total_pages): ?>
                                        <a href="?view=<?php echo $view_mode; ?>&date=<?php echo $ref_date; ?>&limit=<?php echo $limit; ?>&page=<?php echo $page+1; ?>&tab=timeline-view">ถัดไป &raquo;</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="search-wrapper" style="margin-bottom: 15px; position: relative;">
                                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                                <input type="text" id="userSearch" class="form-control" style="padding-left: 40px; border-radius: 20px;" placeholder="ค้นหาชื่อ..." onkeyup="filterTimeline('userSearch', 'userTable')">
                            </div>

                            <div class="timeline-wrapper" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                                <table class="timeline-table" id="userTable">
                                    <thead>
                                        <tr>
                                            <th class="sticky-col" style="background: #f8fafc;">รายชื่อ</th>
                                            <?php 
                                            $curr_h = $timeline_start;
                                            while($curr_h <= $timeline_end) {
                                                $d_num = date('j', strtotime($curr_h));
                                                $cls = in_array($curr_h, $holidays) ? 'header-holiday' : '';
                                                echo "<th class='$cls' style='min-width: 40px;'>$d_num</th>";
                                                $curr_h = date('Y-m-d', strtotime($curr_h . ' +1 day'));
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($display_users as $u): ?>
                                        <tr class="<?php echo ($u['id'] == $user_id) ? 'my-row' : ''; ?>">
                                            <td class="sticky-col">
                                                <?php echo $u['full_name']; ?>
                                                <?php if($u['id'] == $user_id) echo ' <i class="fas fa-user-circle" style="color:var(--success); margin-left:5px;"></i> (คุณ)'; ?>
                                            </td>
                                            <?php
                                            $curr_d = $timeline_start;
                                            while($curr_d <= $timeline_end) {
                                                $bg = in_array($curr_d, $holidays) ? 'is-holiday-col' : '';
                                                echo "<td class='$bg'>";
                                                if (isset($timeline_map[$u['id']][$curr_d])) {
                                                    $st = $timeline_map[$u['id']][$curr_d];
                                                    $short_map = ['morning'=>'ช','afternoon'=>'บ','night'=>'ด','day'=>'D','night_shift'=>'N','morning_afternoon'=>'ชบ','morning_night'=>'ชด','afternoon_night'=>'บด'];
                                                    $sh = $short_map[$st] ?? substr($st, 0, 1);
                                                    echo "<div class='schedule-item $st' style='justify-content:center; width:28px; height:28px; border-radius:50%; margin:auto;'>$sh</div>";
                                                } else {
                                                    echo "<div class='day-off-marker'><i class='fas fa-times'></i></div>";
                                                }
                                                echo "</td>";
                                                $curr_d = date('Y-m-d', strtotime($curr_d . ' +1 day'));
                                            }
                                            ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> 
                </div> 
            </main>
        </div> 
    </div> 

    <input type="hidden" id="csrf_token" value="<?php echo $security->generateCSRFToken(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/mobile-drag-drop@2.3.0-rc.2/index.min.js"></script>

    <script>
    function updateTabState(tabName) {
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState(null, '', url);

        document.querySelectorAll('.date-nav-container a, .view-switcher a, .page-nav a').forEach(link => {
            try {
                const linkUrl = new URL(link.href);
                linkUrl.searchParams.set('tab', tabName);
                link.href = linkUrl.toString();
            } catch (e) {}
        });

        document.querySelectorAll('input[name="tab"]').forEach(input => {
            input.value = tabName;
        });
    }

    function switchUserTab(tabName, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        if(btn) btn.classList.add('active');
        
        const instruct = document.querySelector('.drag-instruction');
        if(instruct) instruct.style.display = (tabName === 'calendar-view') ? 'block' : 'none';

        updateTabState(tabName);
    }

    function filterTimeline(inputId, tableId) {
        var input = document.getElementById(inputId);
        var filter = input.value.toUpperCase();
        var table = document.getElementById(tableId);
        var tr = table.getElementsByTagName("tr");
        for (var i = 1; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                var txt = td.textContent || td.innerText;
                if (txt.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }

    let draggedSchedule = null;
    const userId = '<?php echo $user_id; ?>';

    // ============================================
    // [START] New Auto Scroll Function Logic
    // ============================================
    function handleAutoScroll(yPos) {
        // ระยะขอบจอที่ต้องการให้เริ่มเลื่อน (pixels)
        const edgeSize = 100;
        const scrollSpeed = 15; // ปรับความเร็วตรงนี้
        
        const viewportHeight = window.innerHeight;
        
        // เลื่อนขึ้น
        if (yPos < edgeSize) {
            window.scrollBy(0, -scrollSpeed);
        } 
        // เลื่อนลง
        else if (yPos > (viewportHeight - edgeSize)) {
            window.scrollBy(0, scrollSpeed);
        }
    }
    // ============================================

    function initDragAndDrop() {
        const scheduleItems = document.querySelectorAll('.schedule-item.my-schedule');
        scheduleItems.forEach(item => {
            item.setAttribute('draggable', 'true');
            item.addEventListener('dragstart', function(e) {
                draggedSchedule = { 
                    element: this, 
                    scheduleDate: this.dataset.date, 
                    shiftType: this.dataset.shift, 
                    scheduleId: this.dataset.id, 
                    userName: '<?php echo $_SESSION['full_name']; ?>' 
                };
                e.dataTransfer.effectAllowed = 'move'; 
                e.dataTransfer.setData('text/plain', this.dataset.id);
                this.classList.add('dragging');
            });
            
            // เพิ่ม: จับเหตุการณ์ drag ปกติ (Desktop/Polyfill)
            item.addEventListener('drag', function(e) {
                // e.clientY อาจเป็น 0 ในบางจังหวะจบ ให้เช็คก่อน
                if(e.clientY > 0) handleAutoScroll(e.clientY);
            });

            item.addEventListener('dragend', function() { this.classList.remove('dragging'); draggedSchedule = null; });
        });
        
        // เพิ่ม: Global Touch Move สำหรับ Mobile เพื่อบังคับ Scroll
        window.addEventListener('touchmove', function(e) {
            if (draggedSchedule && e.touches && e.touches[0]) {
                // ป้องกัน default scroll ของ browser ที่อาจตีกับการลาก
                e.preventDefault(); 
                // ใช้ manual scroll ของเราแทน
                handleAutoScroll(e.touches[0].clientY);
            }
        }, {passive: false});

        // Global Dragover เพื่อให้ Auto Scroll ทำงานแม้ไม่ได้อยู่เหนือ Drop Zone
        document.addEventListener('dragover', function(e) {
            e.preventDefault();
            if(e.clientY > 0) handleAutoScroll(e.clientY);
        });

        const allScheduleItems = document.querySelectorAll('.schedule-item:not(.my-schedule)');
        allScheduleItems.forEach(item => {
            
            item.addEventListener('dragenter', function(e) { 
                e.preventDefault(); 
                this.classList.add('drop-zone'); 
            });

            item.addEventListener('dragover', function(e) { 
                e.preventDefault(); 
                this.classList.add('drop-zone'); 
            });

            item.addEventListener('dragleave', function() { 
                this.classList.remove('drop-zone'); 
            });

            item.addEventListener('drop', function(e) { 
                e.preventDefault(); 
                this.classList.remove('drop-zone'); 
                
                if (draggedSchedule) {
                    handleScheduleSwap(this);
                }
            });
        });
    }

    function handleScheduleSwap(targetSchedule) {
        if (!draggedSchedule) return;
        if (targetSchedule.dataset.user === userId) { 
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถสลับเวรกับตัวเองได้', 'error');
            return; 
        }
        const sourceData = {...draggedSchedule};
        const targetId = targetSchedule.dataset.id;
        const targetUserId = targetSchedule.dataset.user;
        const targetDate = targetSchedule.dataset.date;
        const targetShift = targetSchedule.dataset.shift; 
        const myShift = sourceData.shiftType; 

        if(!targetId || !targetUserId) {
            Swal.fire('Error', 'ข้อมูลเป้าหมายไม่ครบถ้วน', 'error');
            return;
        }

        fetch(`api/check_swap_availability.php?original_date=${sourceData.scheduleDate}&target_date=${targetDate}&target_user_id=${targetUserId}&target_shift=${targetShift}&my_shift=${myShift}`)
        .then(response => {
            if(!response.ok) throw new Error("Server Error");
            return response.json();
        })
        .then(data => {
            if (data.canSwap) {
                Swal.fire({
                    title: 'ยืนยันการขอสลับเวร',
                    text: `ต้องการสลับกับ ${targetSchedule.dataset.name} ใช่หรือไม่?`,
                    input: 'textarea',
                    inputLabel: 'เหตุผล (ไม่บังคับ)',
                    inputPlaceholder: 'ระบุเหตุผล...',
                    showCancelButton: true,
                    confirmButtonText: 'ส่งคำขอ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitSwapRequest(sourceData, targetId, targetUserId, result.value || '');
                    }
                });
            } else {
                Swal.fire('ไม่สามารถสลับได้', data.message, 'warning');
            }
        }).catch(err => {
            console.error(err);
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่', 'error');
        });
    }

    function submitSwapRequest(sourceData, targetScheduleId, targetUserId, reason) {
        Swal.fire({ title: 'กำลังส่งคำขอ...', didOpen: () => Swal.showLoading() });
        const formData = new FormData();
        formData.append('original_schedule_id', sourceData.scheduleId);
        formData.append('target_user_id', targetUserId);
        formData.append('target_schedule_id', targetScheduleId);
        formData.append('reason', reason);
        const csrfEl = document.getElementById('csrf_token');
        if(csrfEl) formData.append('csrf_token', csrfEl.value);
        
        fetch('api/submit_swap_dragdrop.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'สำเร็จ',
                    text: 'ส่งคำขอสลับเวรเรียบร้อยแล้ว',
                    icon: 'success',
                    timer: 2000, 
                    showConfirmButton: false
                }).then(() => location.reload()); 
            } else {
                Swal.fire('ผิดพลาด', data.message, 'error');
            }
        });
    }

    // ============================================
    // ฟังก์ชันสร้างการแจ้งเตือนมาตรฐาน (Custom Toast)
    // ============================================
    const StandardToast = Swal.mixin({
        toast: true,
        position: 'top-end', 
        showConfirmButton: false, 
        showCloseButton: true,    
        
        // --- แก้ไขตรงนี้: ระยะเวลาแสดงผล (15 วินาที) ---
        timer: 15000,              
        
        timerProgressBar: true,   
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        },
        customClass: {
            popup: 'colored-toast',
            title: 'toast-title-custom',
            htmlContainer: 'toast-content-custom'
        }
    });

    function showSystemNotification(message, type = 'info', timeStr = '') {
        if (!timeStr) {
            const now = new Date();
            timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                        now.getMinutes().toString().padStart(2, '0');
        }
        let iconType = type === 'danger' ? 'error' : type; 

        StandardToast.fire({
            icon: iconType,               
            title: '🔔 การแจ้งเตือน',      
            html: `
                <div style="font-weight: 500; font-size: 0.95rem; margin-bottom: 3px;">${message}</div>
                <div style="color: #6c757d; font-size: 0.85rem;">(${timeStr})</div>
            `
        });
    }

    // *** ฟังก์ชัน Polling สำหรับ Badge ***
    function checkUserBadge() {
        fetch('api/get_badge_count.php')
            .then(response => response.json())
            .then(data => {
                const totalCount = data.total_count || 0;
                
                const incomingBadge = document.getElementById('incomingSwapBadge'); 
                if (incomingBadge) {
                    if (totalCount > 0) {
                        incomingBadge.innerText = totalCount;
                        incomingBadge.style.display = 'inline-block';
                    } else {
                        incomingBadge.style.display = 'none';
                    }
                }
            })
            .catch(err => console.error('Badge fetch error:', err));
    }

    // *** ฟังก์ชัน Pop-up Alert แบบมาตรฐานใหม่ ***
    function checkUserAlerts() {
        fetch('api/get_unread_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.found && data.data.length > 0) {
                    data.data.forEach(noti => {
                        // เรียกใช้ฟังก์ชันมาตรฐานใหม่
                        showSystemNotification(noti.message, noti.type, noti.time);

                        // ตั้งค่าว่าอ่านแล้ว
                        markNotificationAsRead(noti.id);
                    });
                }
            })
            .catch(err => console.error('Alert fetch error:', err));
    }

    // *** ฟังก์ชันสำหรับตั้งค่าว่าอ่านแล้ว ***
    function markNotificationAsRead(notiId) {
        fetch('api/mark_as_read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `noti_id=${notiId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                checkUserBadge(); // อัพเดต Badge ทันที
            }
        })
        .catch(err => console.error('Mark as read failed:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDragAndDrop();
        
        checkUserBadge(); 
        checkUserAlerts(); 
        
        // --- แก้ไขตรงนี้: ความถี่ในการเช็ค (3 วินาที) ---
        setInterval(checkUserBadge, 3000); 
        setInterval(checkUserAlerts, 3000); 

        const currentTab = new URLSearchParams(window.location.search).get('tab') || 'calendar-view';
        const initBtn = document.querySelector(`.tab-btn[onclick*="'${currentTab}'"]`);
        if(initBtn) switchUserTab(currentTab, initBtn); 

        window.addEventListener('popstate', function(event) {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab') || 'calendar-view'; 
            
            const tabBtn = document.querySelector(`.tab-btn[onclick*="'${tab}'"]`);
            if (tabBtn) {
                switchUserTab(tab, tabBtn);
            }
        });
    });
    </script>

    <script>
        var polyfillOptions = {
            dragImageTranslateOverride: function(event, hoverCoordinates, element, context) {
                hoverCoordinates.y = hoverCoordinates.y - 80; 
                return hoverCoordinates;
            },
            holdToDrag: 300, 
            forceApply: true 
        };
        
        MobileDragDrop.polyfill(polyfillOptions);
    </script>
    </body>
</html>