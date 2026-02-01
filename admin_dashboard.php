<?php
    include 'config.php';

    // ตรวจสอบสิทธิ์ (Admin Check)
    if (!isLoggedIn() || $_SESSION['user_level'] != 'admin') {
        header("location: login.php");
        exit;
    }

    $department_id = $_SESSION['department_id'];
    $user_id = $_SESSION['user_id'];

    // --- [1] รับค่า Tab เพื่อคงสถานะหน้าเดิมไว้ ---
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'calendar-view';

    // --- Logic จัดการวันที่และมุมมอง ---
    $ref_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    $view_mode = isset($_GET['view']) ? $_GET['view'] : 'week';

    $thai_months_short = [1=>'ม.ค.', 2=>'ก.พ.', 3=>'มี.ค.', 4=>'เม.ย.', 5=>'พ.ค.', 6=>'มิ.ย.', 7=>'ก.ค.', 8=>'ส.ค.', 9=>'ก.ย.', 10=>'ต.ค.', 11=>'พ.ย.', 12=>'ธ.ค.'];
    $thai_months_full = [1=>'มกราคม', 2=>'กุมภาพันธ์', 3=>'มีนาคม', 4=>'เมษายน', 5=>'พฤษภาคม', 6=>'มิถุนายน', 7=>'กรกฎาคม', 8=>'สิงหาคม', 9=>'กันยายน', 10=>'ตุลาคม', 11=>'พฤศจิกายน', 12=>'ธันวาคม'];

    $timeline_start = date('Y-m-01', strtotime($ref_date));
    $timeline_end   = date('Y-m-t', strtotime($ref_date));

    // Logic คำนวณวัน (เหมือนเดิม)
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

    // 1. ดึงข้อมูลตารางเวร
    $schedule_sql = "SELECT s.schedule_date, s.shift_type, u.full_name, s.user_id 
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
    while ($row = mysqli_fetch_assoc($schedule_result)) {
        $calendar_schedules[] = $row;
        $timeline_map[$row['user_id']][$row['schedule_date']] = $row['shift_type'];
    }

    // 2. ดึงข้อมูลพนักงาน
    $u_sql = "SELECT id, full_name FROM users WHERE department_id = ? AND is_active = TRUE ORDER BY full_name";
    $u_stmt = mysqli_prepare($conn, $u_sql);
    mysqli_stmt_bind_param($u_stmt, "i", $department_id);
    mysqli_stmt_execute($u_stmt);
    $users_res = mysqli_stmt_get_result($u_stmt);

    // --- [2] Logic แยกตัวเรา (Sticky User) และ Pagination ---
    $my_user_data = null;
    $other_users = [];

    while ($r = mysqli_fetch_assoc($users_res)) {
        if ($r['id'] == $user_id) {
            $my_user_data = $r;
        } else {
            $other_users[] = $r;
        }
    }

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; 
    if (!in_array($limit, [10, 25, 50, 100])) $limit = 10;

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
    $all_users = $display_users;

    // 3. ดึงวันหยุด
    $holidays = [];
    $h_sql = "SELECT holiday_date FROM holiday_settings WHERE department_id = ? AND YEAR(holiday_date) = ?";
    $h_stmt = mysqli_prepare($conn, $h_sql);
    mysqli_stmt_bind_param($h_stmt, "ii", $department_id, $current_year_check);
    mysqli_stmt_execute($h_stmt);
    $h_res = mysqli_stmt_get_result($h_stmt);
    while ($row = mysqli_fetch_assoc($h_res)) { $holidays[] = $row['holiday_date']; }

    $schedule_count = count($calendar_schedules);
    $user_count = count($other_users) + ($my_user_data ? 1 : 0);

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
        <title>แดชบอร์ดแอดมิน</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            .date-nav-container { display: flex; align-items: center; gap: 10px; background: #f1f5f9; padding: 5px 10px; border-radius: 8px; }
            .date-picker-input { border: 1px solid #cbd5e1; border-radius: 4px; padding: 5px 10px; font-family: 'Sarabun', sans-serif; color: var(--text-dark); cursor: pointer; }
            .view-switcher .btn { padding: 5px 12px; font-size: 0.9rem; }
            .view-switcher .btn.active { background-color: var(--primary-blue); color: white; border-color: var(--primary-blue); }
            .view-switcher .btn:not(.active) { background-color: white; color: var(--dark-gray); border: 1px solid #e2e8f0; }
            .btn-highlight { background-color: #fef9c3 !important; color: #854d0e !important; border: 1px dashed #eab308 !important; font-weight: bold; }
            .btn-highlight:hover { background-color: #fde047 !important; }
            
            .pagination-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 10px; }
            .limit-selector { display: flex; align-items: center; gap: 5px; font-size: 0.9rem; }
            .limit-selector select { padding: 5px; border-radius: 4px; border: 1px solid #ddd; }
            .page-nav { display: flex; gap: 5px; }
            .page-nav a { padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333; background: #fff; }
            .page-nav a.active { background: var(--accent); color: #fff; border-color: var(--accent); }
            .page-nav a.disabled { opacity: 0.5; pointer-events: none; }
            
            /* CSS Badge Notification */
            .badge-notification {
                background-color: #ef4444; 
                color: white;
                font-size: 0.75rem;
                font-weight: bold;
                min-width: 20px;
                height: 20px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-left: auto;
                padding: 0 5px;
                box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
                vertical-align: middle;
            }
            .tapbar-menu li a { display: flex; align-items: center; justify-content: flex-start; }
        </style>
    </head>
    <body>
        <div class="container">
            <header>
                <h1>ระบบจัดการเวรพนักงาน</h1>
                <div class="user-info">
                    <span>สวัสดี, <?php echo $_SESSION['full_name']; ?> (Admin)</span>
                    <span>แผนก: <?php echo $dept_name; ?></span>
                    <a href="logout.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                </div>
            </header>
            
            <div class="dashboard-container">
                <nav class="tapbar">
                    <ul class="tapbar-menu">
                        <li><a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> แดชบอร์ด (ผู้ดูแล)</a></li>
                        <li><a href="user_dashboard.php" class="btn-user-view"><i class="fas fa-user-circle"></i> มุมมองส่วนตัว (User)</a></li>
                        <li><a href="attendance/supervisor_qr.php" target="_blank" class="btn-highlight"><i class="fas fa-qrcode"></i> สร้างจุดลงเวลา</a></li>
                        <li><a href="attendance_history.php"><i class="fas fa-history"></i> ประวัติการลงเวลา</a></li>
                        <li><a href="user_management.php"><i class="fas fa-users"></i>จัดการพนักงาน</a></li>
                        <li><a href="schedule_rules.php"><i class="fas fa-cog"></i>กำหนดระเบียบเวร</a></li>
                        <li><a href="random_schedule.php"><i class="fas fa-random"></i>สุ่มเวรอัตโนมัติ</a></li>
                        <li><a href="manual_schedule.php"><i class="fas fa-edit"></i>แก้ไขตารางเวร</a></li>
                        <li>
    <a href="approve_requests.php">
        <i class="fas fa-check-circle"></i> อนุมัติคำขอ
        <span id="adminPendingBadge" class="badge-notification" style="display:none;">0</span>
    </a>
</li>
                        <li><a href="report_management.php"><i class="fas fa-chart-bar"></i>รายงาน</a></li>
                        <li>
    <a href="admin_notifications.php">
        <i class="fas fa-bell"></i> การแจ้งเตือน
        <span id="adminNotificationBadge" class="badge-notification" style="display:none;">0</span>
    </a>
</li>
                    </ul>
                </nav>
                
                <main class="content-area">
                    <div class="welcome-section">
                        <h2><i class="fas fa-tachometer-alt"></i> แดชบอร์ดผู้ดูแลระบบ</h2>
                    </div>
                    
                    <div class="quick-stats">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <div class="stat-info"><h3>พนักงาน</h3><div class="stat-number"><?php echo $user_count; ?> คน</div></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="stat-info"><h3>เวรทั้งหมด</h3><div class="stat-number"><?php echo $schedule_count; ?> กะ</div></div>
                        </div>
                    </div>
                    
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
                                <button class="tab-btn <?php echo ($active_tab == 'calendar-view') ? 'active' : ''; ?>" onclick="openTab('calendar-view', this)"><i class="fas fa-calendar-alt"></i> ปฏิทิน</button>
                                <button class="tab-btn <?php echo ($active_tab == 'timeline-view') ? 'active' : ''; ?>" onclick="openTab('timeline-view', this)"><i class="fas fa-list-ul"></i> ตารางรายชื่อ</button>
                            </div>

                            <div id="calendar-view" class="tab-content <?php echo ($active_tab == 'calendar-view') ? 'active' : ''; ?>">
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
                                    while ($curr <= $end_cal) {
                                        $is_target_month = ($view_mode == 'month') ? (date('m', strtotime($curr)) == date('m', strtotime($ref_date))) : true;
                                        if ($view_mode == 'month' && !$is_target_month) {
                                            echo "<div class='calendar-day empty'></div>";
                                        } else {
                                            $curr_date_str = date('Y-m-d', strtotime($curr));
                                            $holiday_class = in_array($curr_date_str, $holidays) ? ' is-holiday' : '';
                                            $day_style = ($view_mode == 'week') ? ' weekly-day' : '';
                                            echo "<div class='calendar-day" . $holiday_class . $day_style . "'>";
                                            echo "<div class='calendar-date'>" . date('j', strtotime($curr)) . "</div>";
                                           
                                            foreach ($calendar_schedules as $sched) {
                                                if ($sched['schedule_date'] == $curr) {
                                                    $cls = $sched['shift_type'];
                                                    $name = $sched['full_name'];
                                                    $s_name = function_exists('getShiftTypeThaiShort') ? getShiftTypeThaiShort($cls) : $cls;
                                                    
                                                    echo "<div class='schedule-item $cls'><div class='employee-info'><span class='employee-name'>$name</span><span class='shift-name'>$s_name</span></div></div>";
                                                    
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

                                <div class="search-wrapper" style="margin-bottom: 15px;">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="adminSearch" class="form-control" style="padding-left: 35px; border-radius: 20px;" placeholder="ค้นหาชื่อ..." onkeyup="filterTimeline('adminSearch', 'adminTable')">
                                </div>
                                <div class="timeline-wrapper" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <table class="timeline-table" id="adminTable">
                                        <thead>
                                            <tr>
                                                <th class="sticky-col" style="background: #f8fafc;">รายชื่อ</th>
                                                <?php
                                                $curr_h = $timeline_start;
                                                while ($curr_h <= $timeline_end) {
                                                    $d_num = date('j', strtotime($curr_h));
                                                    $cls = in_array($curr_h, $holidays) ? 'header-holiday' : '';
                                                    echo "<th class='$cls' style='min-width: 40px;'>$d_num</th>";
                                                    $curr_h = date('Y-m-d', strtotime($curr_h . ' +1 day'));
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($all_users as $u): ?>
                                            <tr>
                                                <td class="sticky-col">
                                                    <?php echo $u['full_name']; ?>
                                                    <?php if($u['id'] == $user_id) echo ' <i class="fas fa-user-circle" style="color:var(--success); margin-left:5px;"></i> (คุณ)'; ?>
                                                </td>
                                                <?php
                                                $curr_d = $timeline_start;
                                                while ($curr_d <= $timeline_end) {
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
                </div> 
                </main>
            </div>
        </div>
        
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

        function openTab(tabName, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            btn.classList.add('active');
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
                    if (txt.toUpperCase().indexOf(filter) > -1) tr[i].style.display = ""; else tr[i].style.display = "none";
                }       
            }
        }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
        // ============================================
        // ฟังก์ชันสร้างการแจ้งเตือนมาตรฐาน (Custom Toast)
        // ============================================
        const StandardToast = Swal.mixin({
            toast: true,
            position: 'top-end', 
            showConfirmButton: false, 
            showCloseButton: true,    
            timer: 8000,              
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

        // 1. ฟังก์ชันอัปเดต Badge อนุมัติคำขอ (ตัวเลขแดง)
        function updateAdminBadge() {
            fetch('api/get_pending_requests_count.php')
                .then(response => response.json())
                .then(data => {
                    const badgeElement = document.getElementById('adminPendingBadge');
                    const count = data.count || 0; 
                    
                    if (badgeElement) {
                        if (count > 0) {
                            badgeElement.innerText = count;
                            badgeElement.style.display = 'inline-flex';
                        } else {
                            badgeElement.style.display = 'none';
                        }
                    }
                })
                .catch(err => console.error('Admin Badge fetch error:', err));
        }
        
        // 2. ฟังก์ชันอัปเดต Badge แจ้งเตือน (ตัวเลขแดง)
        function updateNotificationBadge() {
            fetch('api/get_unread_notification_count.php')
                .then(response => response.json())
                .then(data => {
                    const badgeElement = document.getElementById('adminNotificationBadge');
                    const count = data.count || 0; 
                    
                    if (badgeElement) {
                        if (count > 0) {
                            badgeElement.innerText = count;
                            badgeElement.style.display = 'inline-flex';
                        } else {
                            badgeElement.style.display = 'none';
                        }
                    }
                })
                .catch(err => console.error('Notification Badge fetch error:', err));
        }

        // 3. ฟังก์ชัน Alert Event ใช้ Custom Toast
        function checkEventAlerts() {
            fetch('api/check_admin_alerts.php') 
                .then(response => response.json())
                .then(data => {
                    if (data.found) {
                        showSystemNotification(data.message, data.type, data.time);
                        updateNotificationBadge();
                    }
                })
                .catch(err => console.error(err));
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateAdminBadge(); 
            updateNotificationBadge();
            checkEventAlerts(); 
            
            setInterval(updateAdminBadge, 3000);   
            setInterval(updateNotificationBadge, 3000); 
            setInterval(checkEventAlerts, 5000);   

            // Init link state
            const currentTab = new URLSearchParams(window.location.search).get('tab') || 'calendar-view';
            const initBtn = document.querySelector(`.tab-btn:not([onclick*="switchUserTab"])[onclick*="'${currentTab}'"]`);
            if(initBtn) openTab(currentTab, initBtn); 
            
            window.addEventListener('popstate', function(event) {
                const params = new URLSearchParams(window.location.search);
                const tab = params.get('tab') || 'calendar-view';
                const tabBtn = document.querySelector(`.tab-btn:not([onclick*="switchUserTab"])[onclick*="'${tab}'"]`);
                if (tabBtn) openTab(tab, tabBtn);
            });
        });
        </script><script>
// ... (code เดิมของคุณ) ...

// ฟังก์ชันอัปเดตตัวเลขคำขอรออนุมัติ
function updateAdminBadge() {
    fetch('api/get_pending_requests_count.php')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('adminPendingBadge');
            if (badge) {
                if (data.count > 0) {
                    badge.innerText = data.count;
                    badge.style.display = 'inline-flex'; // แสดงเมื่อมีค่า
                } else {
                    badge.style.display = 'none'; // ซ่อนเมื่อเป็น 0
                }
            }
        })
        .catch(err => console.error(err));
}

// ฟังก์ชันอัปเดตตัวเลขการแจ้งเตือน
function updateNotificationBadge() {
    fetch('api/get_unread_notification_count.php')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('adminNotificationBadge');
            if (badge) {
                if (data.count > 0) {
                    badge.innerText = data.count;
                    badge.style.display = 'inline-flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(err => console.error(err));
}

// สั่งให้ทำงานทันทีเมื่อเปิดหน้าเว็บ + ทำซ้ำทุก 3 วินาที
document.addEventListener('DOMContentLoaded', function() {
    updateAdminBadge();         // เรียกครั้งแรกทันที (ไม่ต้องรอ Action)
    updateNotificationBadge();  // เรียกครั้งแรกทันที
    
    setInterval(updateAdminBadge, 3000);        // วนลูปเช็คทุก 3 วิ
    setInterval(updateNotificationBadge, 3000); // วนลูปเช็คทุก 3 วิ
});
</script>
        
    </body>
    </html>
    <?php
    mysqli_stmt_close($schedule_stmt);
    mysqli_close($conn);
    ?>