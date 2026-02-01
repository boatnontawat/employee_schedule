<?php
include 'config.php';

if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_level = $_SESSION['user_level'];
$department_id = $_SESSION['department_id'];

// --- [เช็คโหมด] ---
// $is_personal จะเป็น true เมื่อ user_level เป็น 'user' หรือมีการส่ง mode=personal มา
$is_personal = ($user_level == 'user') || (isset($_GET['mode']) && $_GET['mode'] == 'personal');

// --- 1. กำหนดค่าเริ่มต้นมุมมอง ---
if ($is_personal) {
    $default_view = 'weekly'; 
} else {
    $default_view = 'daily';
}

$view = isset($_GET['view']) ? $_GET['view'] : $default_view;

// ป้องกัน User/Personal View เข้าดูรายวัน (หากเข้าไม่ได้ จะสลับไป weekly)
if ($is_personal && $view == 'daily') {
    $view = 'weekly';
}

$ref_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$search_text = isset($_GET['search']) ? trim($_GET['search']) : '';

// $mode_param จะมีค่าก็ต่อเมื่อ Admin กำลังดูในโหมดส่วนตัวเท่านั้น
$mode_param = ($is_personal && $user_level != 'user') ? '&mode=personal' : ''; 

$thai_months = [
    1=>'ม.ค.', 2=>'ก.พ.', 3=>'มี.ค.', 4=>'เม.ย.', 5=>'พ.ค.', 6=>'มิ.ย.',
    7=>'ก.ค.', 8=>'ส.ค.', 9=>'ก.ย.', 10=>'ต.ค.', 11=>'พ.ย.', 12=>'ธ.ค.'
];

// --- 2. คำนวณช่วงเวลา ---
switch($view) {
    case 'daily': 
        $start_date = $ref_date;
        $end_date = $ref_date;
        $prev_date = date('Y-m-d', strtotime($ref_date . ' -1 day'));
        $next_date = date('Y-m-d', strtotime($ref_date . ' +1 day'));
        $label_header = "ประจำวันที่ " . date('j', strtotime($ref_date)) . " " . $thai_months[date('n', strtotime($ref_date))] . " " . (date('Y', strtotime($ref_date))+543);
        break;

    case 'weekly': 
        $monday = (date('w', strtotime($ref_date)) == 1) ? strtotime($ref_date) : strtotime('last monday', strtotime($ref_date));
        $start_date = date('Y-m-d', $monday);
        $end_date = date('Y-m-d', strtotime($start_date . ' +6 days'));
        
        $prev_date = date('Y-m-d', strtotime($start_date . ' -1 week'));
        $next_date = date('Y-m-d', strtotime($start_date . ' +1 week'));
        
        $y_th = date('Y', strtotime($end_date)) + 543;
        $label_header = "สัปดาห์ที่ " . date('j', $monday) . " - " . date('j', strtotime($end_date)) . " " . $thai_months[date('n', strtotime($end_date))] . " " . $y_th;
        break;

    case 'yearly': 
        $year = date('Y', strtotime($ref_date));
        $start_date = "$year-01-01";
        $end_date = "$year-12-31";
        $prev_date = date('Y-m-d', strtotime($ref_date . ' -1 year'));
        $next_date = date('Y-m-d', strtotime($ref_date . ' +1 year'));
        $label_header = "ประจำปี " . ($year + 543);
        break;

    case 'monthly': 
    default:
        $start_date = date('Y-m-01', strtotime($ref_date));
        $end_date = date('Y-m-t', strtotime($ref_date));
        $prev_date = date('Y-m-d', strtotime($start_date . ' -1 month'));
        $next_date = date('Y-m-d', strtotime($start_date . ' +1 month'));
        $label_header = "ประจำเดือน " . $thai_months[date('n', strtotime($start_date))] . " " . (date('Y', strtotime($start_date))+543);
        break;
}

// --- 3. เวลาทำงาน ---
$work_start = '08:00:00'; 
$work_end   = '17:00:00';
$rule_sql = "SELECT work_start_time, work_end_time FROM schedule_rules WHERE department_id = ? LIMIT 1";
$rule_stmt = mysqli_prepare($conn, $rule_sql);
mysqli_stmt_bind_param($rule_stmt, "i", $department_id);
mysqli_stmt_execute($rule_stmt);
$rule_res = mysqli_stmt_get_result($rule_stmt);
if ($row = mysqli_fetch_assoc($rule_res)) {
    if(!empty($row['work_start_time'])) $work_start = $row['work_start_time'];
    if(!empty($row['work_end_time'])) $work_end   = $row['work_end_time'];
}

// --- 4. Query ข้อมูล (ปรับปรุงการจัดการข้อผิดพลาด) ---
$sql = "SELECT a.*, u.full_name, u.username 
        FROM attendance_logs a 
        JOIN users u ON a.user_id = u.id 
        WHERE DATE(a.scan_time) BETWEEN ? AND ? ";

$params = [$start_date, $end_date];
$types = "ss";

if (!$is_personal && ($user_level == 'admin' || $user_level == 'super_admin')) {
    // โหมด Admin: ค้นหาได้
    if (!empty($search_text)) {
        $sql .= " AND (u.full_name LIKE ? OR u.username LIKE ?) ";
        $params[] = "%$search_text%";
        $params[] = "%$search_text%";
        $types .= "ss";
    }
    if ($user_level == 'admin') {
        $sql .= " AND u.department_id = ? ";
        $params[] = $department_id;
        $types .= "i";
    }
} else {
    // โหมดส่วนตัว: ดูเฉพาะของตัวเอง
    $sql .= " AND a.user_id = ? ";
    $params[] = $user_id;
    $types .= "i";
}

$sql .= " ORDER BY a.scan_time DESC";

$result = null; // กำหนดค่าเริ่มต้น $result เป็น null
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // ใช้ฟังก์ชัน call_user_func_array เพื่อจัดการกับจำนวนพารามิเตอร์ที่ไม่แน่นอน
    // เตรียม array ของพารามิเตอร์สำหรับ bind_param
    $bind_params = array_merge([$types], $params);
    $bind_refs = [];
    foreach ($bind_params as $key => $value) {
        $bind_refs[$key] = &$bind_params[$key];
    }
    
    // ผูกพารามิเตอร์
    if (call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind_refs))) {
        if (mysqli_stmt_execute($stmt)) {
            // ดึงผลลัพธ์ก็ต่อเมื่อการ Execute สำเร็จ
            $result = mysqli_stmt_get_result($stmt);
        } else {
            // กรณี Execute ล้มเหลว 
            error_log("MySQLi Execute failed: " . mysqli_stmt_error($stmt) . " SQL: " . $sql);
        }
    } else {
        // กรณี Bind Param ล้มเหลว 
        error_log("MySQLi Bind Param failed for types: " . $types . " SQL: " . $sql);
    }
} else {
    // กรณี Prepare ล้มเหลว 
    error_log("MySQLi Prepare failed: " . mysqli_error($conn) . " SQL: " . $sql);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการลงเวลา</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .toolbar-container {
            background: #fff; padding: 15px; border-radius: 12px; margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;
        }
        .view-group { display: flex; background: #f1f5f9; padding: 4px; border-radius: 8px; gap: 2px; }
        .view-btn {
            border: none; background: transparent; padding: 6px 15px; border-radius: 6px;
            font-size: 0.9rem; color: #64748b; cursor: pointer; transition: all 0.2s;
            text-decoration: none; display: inline-block;
        }
        .view-btn:hover { background: #e2e8f0; }
        .view-btn.active { background: #fff; color: #3b82f6; box-shadow: 0 1px 2px rgba(0,0,0,0.1); font-weight: 600; }
        
        .nav-group { display: flex; align-items: center; gap: 10px; font-weight: bold; color: #334155; }
        .nav-btn {
            background: #fff; border: 1px solid #cbd5e1; color: #64748b; width: 32px; height: 32px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;
            text-decoration: none; transition: 0.2s;
        }
        .nav-btn:hover { background: #f8fafc; color: #334155; border-color: #94a3b8; }

        .search-box { position: relative; }
        .search-box input {
            padding: 8px 15px 8px 35px; border-radius: 20px; border: 1px solid #e2e8f0; font-size: 0.9rem; width: 200px;
        }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
        }
        .status-green { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-red   { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .history-table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
        .history-table th { background: #f8fafc; color: #475569; font-weight: 600; padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        .history-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .time-text { font-family: 'Courier New', monospace; font-weight: 700; font-size: 1rem; }
        
        .gps-btn { 
            color: #3b82f6; background: #eff6ff; padding: 4px 8px; border-radius: 6px; 
            text-decoration: none; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;
            border: 1px solid #dbeafe; transition: all 0.2s;
        }
        .gps-btn:hover { background: #3b82f6; color: white; }

        @media (max-width: 768px) {
            .toolbar-container { flex-direction: column; align-items: stretch; }
            .nav-group { justify-content: space-between; background: #f8fafc; padding: 10px; border-radius: 8px; }
            .view-group { justify-content: space-between; }
            .view-btn { flex: 1; text-align: center; padding: 8px 5px; font-size: 0.8rem; }
            .hide-mobile { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-history"></i> ประวัติการลงเวลา</h1>
            <div class="user-info">
                <?php
                // โค้ดปุ่ม "กลับ": ใช้ $is_personal ในการกำหนด URL
                if ($is_personal) { 
                    $return_url = 'user_dashboard.php';
                } else {
                    $return_url = 'admin_dashboard.php';
                }
                ?>
                <a href="<?php echo $return_url; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
            </div>
        </header>

        <div class="content-area">
            
            <div class="toolbar-container">
                <div class="view-group">
                    <?php 
                    // โค้ดที่แก้ไข: Admin จะเห็นรายวันได้ ถ้าไม่ใช่โหมดส่วนตัว ($is_personal == false)
                    if (!$is_personal): 
                    ?>
                        <a href="?view=daily&date=<?php echo $ref_date; ?><?php echo $mode_param; ?>" class="view-btn <?php echo $view=='daily'?'active':''; ?>">รายวัน</a>
                    <?php endif; ?>
                    
                    <a href="?view=weekly&date=<?php echo $ref_date; ?><?php echo $mode_param; ?>" class="view-btn <?php echo $view=='weekly'?'active':''; ?>">รายสัปดาห์</a>
                    <a href="?view=monthly&date=<?php echo $ref_date; ?><?php echo $mode_param; ?>" class="view-btn <?php echo $view=='monthly'?'active':''; ?>">รายเดือน</a>
                    <a href="?view=yearly&date=<?php echo $ref_date; ?><?php echo $mode_param; ?>" class="view-btn <?php echo $view=='yearly'?'active':''; ?>">รายปี</a>
                </div>

                <div class="nav-group">
                    <a href="?view=<?php echo $view; ?>&date=<?php echo $prev_date; ?>&search=<?php echo urlencode($search_text); ?><?php echo $mode_param; ?>" class="nav-btn"><i class="fas fa-chevron-left"></i></a>
                    <span style="min-width: 150px; text-align: center;"><?php echo $label_header; ?></span>
                    <a href="?view=<?php echo $view; ?>&date=<?php echo $next_date; ?>&search=<?php echo urlencode($search_text); ?><?php echo $mode_param; ?>" class="nav-btn"><i class="fas fa-chevron-right"></i></a>
                </div>

                <?php if (!$is_personal): // เฉพาะ Admin ที่ดูภาพรวมเท่านั้นถึงจะเห็นช่องค้นหา ?>
                <form method="GET" class="search-box">
                    <input type="hidden" name="view" value="<?php echo $view; ?>">
                    <input type="hidden" name="date" value="<?php echo $ref_date; ?>">
                    <?php 
                    if (!empty($mode_param)): ?>
                        <input type="hidden" name="mode" value="personal">
                    <?php endif; ?>
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="ค้นหาชื่อ..." value="<?php echo htmlspecialchars($search_text); ?>">
                </form>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-body" style="padding: 0; overflow-x: auto;">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>วันที่</th>
                                <?php if(!$is_personal): ?><th>พนักงาน</th><?php endif; ?>
                                <th>รายการ</th>
                                <th>เวลาที่สแกน</th>
                                <th>สถานะ</th>
                                <th>พิกัด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?> 
                                <?php while ($row = mysqli_fetch_assoc($result)): 
                                    $timestamp = strtotime($row['scan_time']);
                                    $date_thai = date('d/m/Y', $timestamp);
                                    $time_only = date('H:i:s', $timestamp);
                                    
                                    $status_html = '';
                                    if ($row['action_type'] == 'check_in') {
                                        $action_label = '<span style="color:#16a34a; font-weight:bold;"><i class="fas fa-sign-in-alt"></i> เข้างาน</span>';
                                        
                                        if ($time_only <= $work_start) {
                                            $status_html = '<span class="status-badge status-green"><i class="fas fa-check"></i> ตรงเวลา</span>';
                                        } else {
                                            $late_min = round((strtotime($time_only) - strtotime($work_start)) / 60);
                                            $status_html = '<span class="status-badge status-red"><i class="fas fa-exclamation-circle"></i> สาย '.$late_min.' น.</span>';
                                        }
                                    } else {
                                        $action_label = '<span style="color:#dc2626; font-weight:bold;"><i class="fas fa-sign-out-alt"></i> ออกงาน</span>';
                                        
                                        if ($time_only >= $work_end) {
                                            $status_html = '<span class="status-badge status-green"><i class="fas fa-check"></i> ปกติ</span>';
                                        } else {
                                            $early_min = round((strtotime($work_end) - strtotime($time_only)) / 60);
                                            $status_html = '<span class="status-badge status-red"><i class="fas fa-running"></i> ออกก่อน '.$early_min.' น.</span>';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $date_thai; ?></td>
                                    
                                    <?php if(!$is_personal): ?>
                                    <td>
                                        <div style="font-weight:600;"><?php echo $row['full_name']; ?></div>
                                        <div style="font-size:0.75rem; color:#94a3b8;"><?php echo $row['username']; ?></div>
                                    </td>
                                    <?php endif; ?>

                                    <td><?php echo $action_label; ?></td>
                                    <td><span class="time-text"><?php echo date('H:i', $timestamp); ?></span></td>
                                    <td><?php echo $status_html; ?></td>
                                    
                                    <td>
                                        <?php if (!empty($row['latitude']) && !empty($row['longitude'])): ?>
                                            <a href="http://maps.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>" target="_blank" class="gps-btn">
                                                <i class="fas fa-map-marker-alt"></i> <span class="hide-mobile">Map</span>
                                            </a>
                                        <?php else: ?>
                                            <span style="color:#cbd5e1;">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                        <i class="far fa-calendar-times fa-3x" style="margin-bottom:10px;"></i><br>
                                        <?php echo ($result === false) ? 'เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาตรวจสอบ Log' : 'ไม่พบข้อมูลการลงเวลาในช่วงนี้'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div style="margin-top: 15px; font-size: 0.85rem; color: #64748b; text-align: center;">
                <i class="fas fa-info-circle"></i> เวลาเข้างานปกติ: <?php echo date('H:i', strtotime($work_start)); ?> น. | เวลาเลิกงานปกติ: <?php echo date('H:i', strtotime($work_end)); ?> น.
            </div>

        </div>
    </div>
</body>
</html>
<?php
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>