<?php
// ไฟล์: ward_created.php (Super Admin Dashboard)
require_once 'config.php';

// 1. ตรวจสอบสิทธิ์ Super Admin
if (!isLoggedIn() || $_SESSION['user_level'] != 'super_admin') {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$alert_msg = '';

// --- BACKEND LOGIC ---

// A. จัดการหน่วยงาน
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_dept'])) {
    if ($_POST['action_dept'] == 'add') {
        $name = sanitizeInput($conn, $_POST['dept_name']);
        $sql = "INSERT INTO departments (name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name);
        if(mysqli_stmt_execute($stmt)) $alert_msg = "success|เพิ่มหน่วยงานสำเร็จ";
        else $alert_msg = "error|เพิ่มไม่สำเร็จ";
    } elseif ($_POST['action_dept'] == 'delete') {
        $id = $_POST['dept_id'];
        $check = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE department_id = $id");
        $row = mysqli_fetch_assoc($check);
        if ($row['c'] > 0) {
            $alert_msg = "error|ลบไม่ได้: มีพนักงานสังกัดหน่วยงานนี้อยู่";
        } else {
            mysqli_query($conn, "DELETE FROM departments WHERE id = $id");
            $alert_msg = "success|ลบหน่วยงานสำเร็จ";
        }
    } elseif ($_POST['action_dept'] == 'edit') {
        $id = $_POST['dept_id'];
        $name = sanitizeInput($conn, $_POST['dept_name']);
        $stmt = mysqli_prepare($conn, "UPDATE departments SET name = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $name, $id);
        if(mysqli_stmt_execute($stmt)) $alert_msg = "success|แก้ไขชื่อหน่วยงานสำเร็จ";
    }
}

// B. จัดการผู้ใช้
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_user'])) {
    if ($_POST['action_user'] == 'add' || $_POST['action_user'] == 'edit') {
        $username = sanitizeInput($conn, $_POST['username']);
        $fullname = sanitizeInput($conn, $_POST['full_name']);
        $dept_id = $_POST['department_id'];
        $role = $_POST['level'];
        $emp_level = $_POST['employee_level'];
        
        if ($_POST['action_user'] == 'add') {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, full_name, department_id, level, employee_level, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssissi", $username, $password, $fullname, $dept_id, $role, $emp_level);
        } else {
            $id = $_POST['user_id'];
            $sql = "UPDATE users SET full_name=?, department_id=?, level=?, employee_level=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sisii", $fullname, $dept_id, $role, $emp_level, $id);
            if (!empty($_POST['password'])) {
                $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET password = '$new_pass' WHERE id = $id");
            }
        }
        if(mysqli_stmt_execute($stmt)) $alert_msg = "success|บันทึกข้อมูลผู้ใช้สำเร็จ";
        else $alert_msg = "error|เกิดข้อผิดพลาด (Username อาจซ้ำ)";
        
    } elseif ($_POST['action_user'] == 'delete') {
        $id = $_POST['user_id'];
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
        $alert_msg = "success|ลบผู้ใช้งานสำเร็จ";
    }
}

// C. Security Settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_security'])) {
    foreach ($_POST['settings'] as $key => $val) {
        $val = sanitizeInput($conn, $val);
        $key = sanitizeInput($conn, $key);
        $chk = mysqli_query($conn, "SELECT id FROM security_settings WHERE setting_key = '$key'");
        if(mysqli_num_rows($chk) > 0) {
            mysqli_query($conn, "UPDATE security_settings SET setting_value = '$val', updated_at=NOW() WHERE setting_key = '$key'");
        } else {
            mysqli_query($conn, "INSERT INTO security_settings (setting_key, setting_value) VALUES ('$key', '$val')");
        }
    }
    $alert_msg = "success|บันทึกตั้งค่าความปลอดภัยเรียบร้อย";
}

// --- DATA FETCHING ---
$depts = mysqli_query($conn, "SELECT * FROM departments ORDER BY id ASC");
$depts_arr = []; while($d = mysqli_fetch_assoc($depts)) $depts_arr[] = $d;

$users_list = mysqli_query($conn, "SELECT u.*, d.name as dept_name FROM users u LEFT JOIN departments d ON u.department_id = d.id WHERE u.level != 'super_admin' ORDER BY u.id DESC");

$sec_query = mysqli_query($conn, "SELECT * FROM security_settings");
$settings = []; while($row = mysqli_fetch_assoc($sec_query)) { $settings[$row['setting_key']] = $row['setting_value']; }
$defaults = ['max_login_attempts'=>5, 'lockout_duration'=>30, 'qr_refresh_rate_seconds'=>60, 'session_timeout'=>60];
foreach($defaults as $k=>$v) { if(!isset($settings[$k])) $settings[$k] = $v; }

$logs = mysqli_query($conn, "SELECT al.*, u.full_name FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 100");

// Stats
$count_users = mysqli_num_rows($users_list);
$count_depts = count($depts_arr);
$count_logs = mysqli_num_rows($logs);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { background-color: #f1f5f9; font-family: 'Sarabun', sans-serif; }
        .dashboard-container { display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #1e293b;
            color: #fff;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { padding: 20px 0; }
        .menu-item {
            padding: 12px 20px;
            display: flex; align-items: center;
            color: #94a3b8; text-decoration: none;
            transition: 0.2s; cursor: pointer;
        }
        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.05); color: #fff; border-left: 3px solid #3b82f6;
        }
        .menu-item i { width: 25px; margin-right: 10px; }
        
        /* Main Content */
        .main-content { flex-grow: 1; padding: 20px; overflow-x: auto; display: flex; flex-direction: column; }
        
        /* Top Bar Style */
        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Cards & Tables */
        .stat-card {
            background: #fff; border-radius: 10px; padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #3b82f6;
            margin-bottom: 20px;
        }
        .content-section { display: none; }
        .content-section.active { display: block; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .table-responsive { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-user-shield"></i> Super Admin</h3>
            <small class="text-muted" style="font-size:0.8em;">CONTROL PANEL</small>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item" id="menu-dashboard" onclick="showTab('dashboard', this)">
                <i class="fas fa-chart-pie"></i> ภาพรวม
            </div>
            <div class="menu-item" id="menu-departments" onclick="showTab('departments', this)">
                <i class="fas fa-building"></i> จัดการหน่วยงาน
            </div>
            <div class="menu-item" id="menu-users" onclick="showTab('users', this)">
                <i class="fas fa-users"></i> จัดการผู้ใช้
            </div>
            <div class="menu-item" id="menu-security" onclick="showTab('security', this)">
                <i class="fas fa-lock"></i> ความปลอดภัย
            </div>
            <div class="menu-item" id="menu-audit" onclick="showTab('audit', this)">
                <i class="fas fa-history"></i> Logs ระบบ
            </div>
            <a href="report_management.php" class="menu-item">
                <i class="fas fa-file-alt"></i> รายงานรวม
            </a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="top-bar">
            <div>
                <h5 class="m-0 fw-bold text-primary" id="page-title">ภาพรวมระบบ</h5>
                <small class="text-muted">ยินดีต้อนรับ, Super Administrator</small>
            </div>
            <div>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
                </a>
            </div>
        </div>

        <div id="dashboard" class="content-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="text-muted">หน่วยงานทั้งหมด</h5>
                                <h2><?= $count_depts ?></h2>
                            </div>
                            <i class="fas fa-building fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="border-left-color: #10b981;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="text-muted">ผู้ใช้งานทั้งหมด</h5>
                                <h2><?= $count_users ?></h2>
                            </div>
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="border-left-color: #f59e0b;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="text-muted">กิจกรรมล่าสุด (Logs)</h5>
                                <h2><?= $count_logs ?></h2>
                            </div>
                            <i class="fas fa-history fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5><i class="fas fa-server"></i> System Info</h5>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">PHP Version: <?= phpversion() ?></li>
                                <li class="list-group-item">Database: MySQL</li>
                                <li class="list-group-item">Server Time: <?= date('d/m/Y H:i:s') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="departments" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>จัดการหน่วยงาน</h2>
                <button class="btn btn-primary" onclick="resetDeptForm(); new bootstrap.Modal(document.getElementById('modalDept')).show();">
                    <i class="fas fa-plus"></i> เพิ่มหน่วยงาน
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>ชื่อหน่วยงาน</th><th>จัดการ</th></tr></thead>
                    <tbody>
                        <?php foreach($depts_arr as $d): ?>
                        <tr>
                            <td><?= $d['id'] ?></td>
                            <td><?= $d['name'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editDept(<?= $d['id'] ?>, '<?= $d['name'] ?>')"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteDept(<?= $d['id'] ?>)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="users" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>จัดการผู้ใช้งาน</h2>
                <button class="btn btn-primary" onclick="openUserModal('add')">
                    <i class="fas fa-user-plus"></i> เพิ่มผู้ใช้
                </button>
            </div>
            <div class="table-responsive">
                <table id="tableUsers" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>ชื่อ-สกุล</th>
                            <th>Username</th>
                            <th>แผนก</th>
                            <th>Role</th>
                            <th>Level</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($users_list, 0); while($u = mysqli_fetch_assoc($users_list)): ?>
                        <tr>
                            <td><?= $u['full_name'] ?></td>
                            <td><?= $u['username'] ?></td>
                            <td><?= $u['dept_name'] ?? '-' ?></td>
                            <td>
                                <span class="badge <?= $u['level']=='admin'?'bg-danger':'bg-secondary' ?>">
                                    <?= ucfirst($u['level']) ?>
                                </span>
                            </td>
                            <td>L<?= $u['employee_level'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick='openUserModal("edit", <?= json_encode($u) ?>)'><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $u['id'] ?>)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="security" class="content-section">
            <h2 class="mb-4">ตั้งค่าความปลอดภัย</h2>
            <div class="card border-0 shadow-sm" style="max-width: 800px;">
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="save_security" value="1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Max Login Attempts (ครั้ง)</label>
                                <input type="number" name="settings[max_login_attempts]" class="form-control" value="<?= $settings['max_login_attempts'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lockout Duration (นาที)</label>
                                <input type="number" name="settings[lockout_duration]" class="form-control" value="<?= $settings['lockout_duration'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">QR Refresh Rate (วินาที)</label>
                                <input type="number" name="settings[qr_refresh_rate_seconds]" class="form-control" value="<?= $settings['qr_refresh_rate_seconds'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Session Timeout (นาที)</label>
                                <input type="number" name="settings[session_timeout]" class="form-control" value="<?= $settings['session_timeout'] ?>">
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> บันทึกการตั้งค่า</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="audit" class="content-section">
            <h2 class="mb-4">Audit Logs</h2>
            <div class="table-responsive">
                <table id="tableLogs" class="table table-sm table-hover w-100" style="font-size:0.9rem;">
                    <thead>
                        <tr>
                            <th>เวลา</th>
                            <th>ผู้ใช้</th>
                            <th>Action</th>
                            <th>รายละเอียด</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($logs, 0); while($l = mysqli_fetch_assoc($logs)): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($l['created_at'])) ?></td>
                            <td><?= $l['full_name'] ?? 'System' ?></td>
                            <td class="fw-bold"><?= $l['action_type'] ?></td>
                            <td><?= $l['action_description'] ?></td>
                            <td><?= $l['ip_address'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalDept" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deptModalLabel">เพิ่มหน่วยงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action_dept" id="action_dept" value="add">
                <input type="hidden" name="dept_id" id="dept_id">
                <div class="mb-3">
                    <label>ชื่อหน่วยงาน</label>
                    <input type="text" name="dept_name" id="dept_name" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">จัดการผู้ใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action_user" id="action_user" value="add">
                <input type="hidden" name="user_id" id="user_edit_id">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Username</label>
                        <input type="text" name="username" id="u_username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Password <small class="text-muted" id="pass_hint"></small></label>
                        <input type="password" name="password" id="u_password" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>ชื่อ-นามสกุล</label>
                        <input type="text" name="full_name" id="u_fullname" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>แผนก</label>
                        <select name="department_id" id="u_dept" class="form-select">
                            <?php foreach($depts_arr as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Role</label>
                        <select name="level" id="u_level" class="form-select">
                            <option value="user">User (พนักงานทั่วไป)</option>
                            <option value="admin">Admin (หัวหน้าแผนก)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Level</label>
                        <select name="employee_level" id="u_emp_level" class="form-select">
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableUsers').DataTable({ language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" } });
        $('#tableLogs').DataTable({ order: [[ 0, "desc" ]], language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" } });

        // [LOGIC ใหม่] ตรวจสอบ Tab ล่าสุดจาก LocalStorage
        let activeTab = localStorage.getItem('superAdmin_activeTab') || 'dashboard';
        let activeMenu = document.getElementById('menu-' + activeTab);
        
        if (activeMenu) {
            showTab(activeTab, activeMenu);
        } else {
            showTab('dashboard', document.getElementById('menu-dashboard'));
        }

        <?php if($alert_msg): list($type, $msg) = explode('|', $alert_msg); ?>
            Swal.fire({ icon: '<?= $type ?>', title: '<?= $msg ?>', timer: 2000, showConfirmButton: false });
        <?php endif; ?>
    });

    function showTab(id, el) {
        // บันทึก Tab ล่าสุดลงในเครื่อง
        localStorage.setItem('superAdmin_activeTab', id);

        $('.content-section').removeClass('active');
        $('#'+id).addClass('active');
        $('.menu-item').removeClass('active');
        $(el).addClass('active');
        
        let titleMap = {
            'dashboard': 'ภาพรวมระบบ',
            'departments': 'จัดการหน่วยงาน',
            'users': 'จัดการผู้ใช้งาน',
            'security': 'ตั้งค่าความปลอดภัย',
            'audit': 'Logs ระบบ'
        };
        $('#page-title').text(titleMap[id] || 'Super Admin Dashboard');
    }

    // Departments Logic
    function resetDeptForm() {
        $('#action_dept').val('add'); $('#dept_id').val(''); $('#dept_name').val(''); $('#deptModalLabel').text('เพิ่มหน่วยงาน');
    }
    function editDept(id, name) {
        $('#action_dept').val('edit'); $('#dept_id').val(id); $('#dept_name').val(name); $('#deptModalLabel').text('แก้ไขหน่วยงาน');
        new bootstrap.Modal(document.getElementById('modalDept')).show();
    }
    function deleteDept(id) {
        Swal.fire({
            title: 'ยืนยันการลบ?', text: "ข้อมูลจะหายไปถาวร!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form'); form.method = 'POST';
                form.innerHTML = `<input type="hidden" name="action_dept" value="delete"><input type="hidden" name="dept_id" value="${id}">`;
                document.body.appendChild(form); form.submit();
            }
        });
    }

    // Users Logic
    function openUserModal(action, user = null) {
        $('#action_user').val(action);
        if (action == 'add') {
            $('#userModalLabel').text('เพิ่มผู้ใช้งาน');
            $('#user_edit_id').val(''); $('#u_username').val('').prop('disabled', false);
            $('#u_password').val('').prop('required', true); $('#pass_hint').text('');
            $('#u_fullname').val(''); $('#u_level').val('user'); $('#u_emp_level').val('1');
        } else {
            $('#userModalLabel').text('แก้ไขข้อมูลผู้ใช้');
            $('#user_edit_id').val(user.id); $('#u_username').val(user.username).prop('disabled', true);
            $('#u_password').val('').prop('required', false); $('#pass_hint').text('(เว้นว่างถ้าไม่เปลี่ยน)');
            $('#u_fullname').val(user.full_name); $('#u_dept').val(user.department_id);
            $('#u_level').val(user.level); $('#u_emp_level').val(user.employee_level);
        }
        new bootstrap.Modal(document.getElementById('modalUser')).show();
    }
    function deleteUser(id) {
        Swal.fire({
            title: 'ยืนยันลบผู้ใช้?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form'); form.method = 'POST';
                form.innerHTML = `<input type="hidden" name="action_user" value="delete"><input type="hidden" name="user_id" value="${id}">`;
                document.body.appendChild(form); form.submit();
            }
        });
    }
</script>

</body>
</html>