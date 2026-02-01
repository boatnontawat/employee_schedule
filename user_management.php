<?php
include 'config.php';

// 1. ตรวจสอบสิทธิ์ Admin
if (!isLoggedIn() || $_SESSION['user_level'] != 'admin') {
    header("location: login.php");
    exit;
}

$department_id = $_SESSION['department_id'];
$success_msg = "";
$error_msg = "";

// ส่วนรับข้อความแจ้งเตือนจากไฟล์ edit_user_action.php และ delete_user.php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success_edit') {
        $success_msg = "บันทึกการแก้ไขเรียบร้อยแล้ว";
    } elseif ($_GET['status'] == 'success_delete') {
        $success_msg = "ลบพนักงานเรียบร้อยแล้ว";
    } elseif ($_GET['status'] == 'error') {
        $error_msg = "เกิดข้อผิดพลาดในการบันทึก";
    } elseif ($_GET['status'] == 'error_delete') {
        $error_msg = "ไม่สามารถลบได้ (อาจมีข้อมูลเวรค้างอยู่)";
    }
}

// 2. จัดการการเพิ่มข้อมูล (Add ยังคงไว้ที่นี่ตามเดิม หรือจะแยกก็ได้ แต่ส่วน Edit/Delete แยกไปแล้ว)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- เพิ่มพนักงานใหม่ ---
    if (isset($_POST['add_user'])) {
        $username = sanitizeInput($conn, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $full_name = sanitizeInput($conn, $_POST['full_name']);
        $level = sanitizeInput($conn, $_POST['level']); 
        $emp_level = sanitizeInput($conn, $_POST['employee_level']); 

        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            $error_msg = "Username นี้มีผู้ใช้งานแล้ว";
        } else {
            $sql = "INSERT INTO users (username, password, full_name, department_id, level, employee_level, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, 1)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssdii", $username, $password, $full_name, $department_id, $level, $emp_level);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "เพิ่มพนักงานเรียบร้อยแล้ว";
            } else {
                $error_msg = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// 3. ดึงข้อมูลพนักงานทั้งหมดในแผนก
$users = [];
$sql = "SELECT * FROM users WHERE department_id = ? ORDER BY is_active DESC, level ASC, full_name ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $department_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

// ดึงชื่อแผนก
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
    <title>จัดการพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 20px; border-radius: 10px; width: 90%; max-width: 500px; position: relative; }
        .close { float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .status-active { color: #10b981; font-weight: bold; }
        .status-inactive { color: #ef4444; font-weight: bold; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f8fafc; color: #64748b; font-weight: 600; }
        
        .badge-notification {
            background-color: #ef4444; color: white;
            font-size: 0.75rem; font-weight: bold;
            min-width: 20px; height: 20px;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            margin-left: auto; padding: 0 5px;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
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
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> แดชบอร์ด (ผู้ดูแล)</a></li>
                    <li><a href="user_dashboard.php" class="btn-user-view"><i class="fas fa-user-circle"></i> มุมมองส่วนตัว (User)</a></li>
                    <li><a href="user_management.php" class="active"><i class="fas fa-users"></i>จัดการพนักงาน</a></li>
                    <li><a href="schedule_rules.php"><i class="fas fa-cog"></i>กำหนดระเบียบเวร</a></li>
                    <li><a href="random_schedule.php"><i class="fas fa-random"></i>สุ่มเวรอัตโนมัติ</a></li>
                    <li><a href="manual_schedule.php"><i class="fas fa-edit"></i>แก้ไขตารางเวร</a></li>
                    <li>
                        <a href="approve_requests.php">
                            <i class="fas fa-check-circle"></i> อนุมัติคำขอ
                            <span id="adminPendingBadge" class="badge-notification" style="display:none;"></span>
                        </a>
                    </li>
                    <li><a href="report_management.php"><i class="fas fa-chart-bar"></i>รายงาน</a></li>
                    <li>
                        <a href="admin_notifications.php">
                            <i class="fas fa-bell"></i> การแจ้งเตือน
                            <span id="adminNotificationBadge" class="badge-notification" style="display:none;"></span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <main class="content-area">
                <div class="page-header">
                    <h2><i class="fas fa-users"></i> จัดการข้อมูลพนักงาน</h2>
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fas fa-user-plus"></i> เพิ่มพนักงานใหม่
                    </button>
                </div>

                <?php if ($success_msg): ?>
                    <div class="alert alert-success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                <?php if ($error_msg): ?>
                    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ชื่อ-สกุล</th>
                                        <th>Username</th>
                                        <th>ระดับ</th>
                                        <th>Level พนักงาน</th>
                                        <th>สถานะ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;"><?php echo $u['full_name']; ?></div>
                                        </td>
                                        <td><?php echo $u['username']; ?></td>
                                        <td>
                                            <?php if($u['level'] == 'admin'): ?>
                                                <span class="badge badge-warning">Admin</span>
                                            <?php else: ?>
                                                <span class="badge badge-info">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Level <?php echo $u['employee_level']; ?></td>
                                        <td>
                                            <?php echo $u['is_active'] ? '<span class="status-active">ใช้งานปกติ</span>' : '<span class="status-inactive">ระงับ</span>'; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm" onclick='openEditModal(<?php echo json_encode($u); ?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <form method="post" action="delete_user.php" style="display:inline;" onsubmit="return confirm('ยืนยันการลบ?');">
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h3>เพิ่มพนักงานใหม่</h3>
            <form method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>ชื่อ-นามสกุล</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>สิทธิ์การใช้งาน</label>
                        <select name="level" class="form-control">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ระดับพนักงาน (Level)</label>
                        <select name="employee_level" class="form-control">
                            <option value="1">1 (ทั่วไป)</option>
                            <option value="2">2 (อาวุโส)</option>
                            <option value="3">3 (หัวหน้า/Sup)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_user" class="btn btn-success btn-block">บันทึก</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h3>แก้ไขข้อมูลพนักงาน</h3>
            
            <form method="post" action="edit_user_action.php">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="form-group">
                    <label>ชื่อ-นามสกุล</label>
                    <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>เปลี่ยนรหัสผ่าน (เว้นว่างถ้าไม่เปลี่ยน)</label>
                    <input type="password" name="password" class="form-control" placeholder="******">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>สิทธิ์การใช้งาน</label>
                        <select name="level" id="edit_level" class="form-control">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ระดับพนักงาน</label>
                        <select name="employee_level" id="edit_emp_level" class="form-control">
                            <option value="1">1 (ทั่วไป)</option>
                            <option value="2">2 (อาวุโส)</option>
                            <option value="3">3 (หัวหน้า/Sup)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_active" id="edit_is_active"> ใช้งานปกติ (Active)</label>
                </div>
                <button type="submit" name="edit_user" class="btn btn-primary btn-block">บันทึกการแก้ไข</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // ... JavaScript เดิมของคุณ ...
    function openAddModal() { document.getElementById('addModal').style.display = 'block'; }
    function openEditModal(user) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('edit_user_id').value = user.id;
        document.getElementById('edit_full_name').value = user.full_name;
        document.getElementById('edit_level').value = user.level;
        document.getElementById('edit_emp_level').value = user.employee_level;
        document.getElementById('edit_is_active').checked = (user.is_active == 1);
    }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    window.onclick = function(event) { if (event.target.classList.contains('modal')) { event.target.style.display = "none"; } }

    function updateAdminBadge() {
        fetch('api/get_pending_requests_count.php').then(res => res.json()).then(data => {
            const badge = document.getElementById('adminPendingBadge');
            if(badge) { badge.innerText = data.count > 0 ? data.count : ''; badge.style.display = data.count > 0 ? 'inline-flex' : 'none'; }
        }).catch(err => console.error(err));
    }
    function updateNotificationBadge() {
        fetch('api/get_unread_notification_count.php').then(res => res.json()).then(data => {
            const badge = document.getElementById('adminNotificationBadge');
            if(badge) { badge.innerText = data.count > 0 ? data.count : ''; badge.style.display = data.count > 0 ? 'inline-flex' : 'none'; }
        }).catch(err => console.error(err));
    }

    const StandardToast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, showCloseButton: true, timer: 8000, timerProgressBar: true,
        didOpen: (toast) => { toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); },
        customClass: { popup: 'colored-toast', title: 'toast-title-custom', htmlContainer: 'toast-content-custom' }
    });

    function showSystemNotification(message, type = 'info', timeStr = '') {
        if (!timeStr) {
            const now = new Date();
            timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        }
        let iconType = type === 'danger' ? 'error' : type; 
        StandardToast.fire({
            icon: iconType, title: '🔔 การแจ้งเตือน',
            html: `<div style="font-weight: 500; font-size: 0.95rem; margin-bottom: 3px;">${message}</div><div style="color: #6c757d; font-size: 0.85rem;">(${timeStr})</div>`
        });
    }

    function checkEventAlerts() {
        fetch('api/check_admin_alerts.php').then(res => res.json()).then(data => {
            if (data.found) {
                showSystemNotification(data.message, data.type, data.time);
                updateNotificationBadge();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateAdminBadge();
        updateNotificationBadge();
        checkEventAlerts();
        setInterval(updateAdminBadge, 3000);
        setInterval(updateNotificationBadge, 3000);
        setInterval(checkEventAlerts, 5000);
    });
    </script>
</body>
</html>