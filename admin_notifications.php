<?php
// ไฟล์: admin_notifications.php
include 'config.php';

if (!isLoggedIn() || $_SESSION['user_level'] != 'admin') {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ดึงการแจ้งเตือนทั้งหมดของ Admin คนนี้
$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการแจ้งเตือน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .noti-item { 
            padding: 15px; border-bottom: 1px solid #eee; background: #fff; 
            display: flex; justify-content: space-between; align-items: center;
        }
        .noti-item:hover { background: #f8f9fa; }
        .noti-content { display: flex; align-items: center; gap: 15px; }
        .noti-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        .bg-warning { background-color: #f6c23e; }
        .bg-success { background-color: #1cc88a; }
        .bg-danger { background-color: #e74a3b; }
        .bg-info { background-color: #36b9cc; }
        .noti-time { color: #858796; font-size: 0.85rem; white-space: nowrap; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?> (Admin)</span>
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
            </div>
        </header>
        
        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="admin_dashboard.php"><i class="fas fa-home"></i>แดชบอร์ด</a></li>
                    <li><a href="admin_notifications.php" class="active"><i class="fas fa-bell"></i>การแจ้งเตือน</a></li>
                    <li><a href="user_management.php"><i class="fas fa-users"></i>จัดการพนักงาน</a></li>
                    </ul>
            </nav>
            
            <main class="content-area">
                <div class="page-header">
                    <h2><i class="fas fa-bell"></i> ประวัติการแจ้งเตือน</h2>
                </div>
                
                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): 
                                $bg = 'bg-info';
                                $icon = 'fa-info-circle';
                                if($row['type']=='warning') { $bg='bg-warning'; $icon='fa-exclamation-triangle'; }
                                if($row['type']=='success') { $bg='bg-success'; $icon='fa-check-circle'; }
                                if($row['type']=='danger')  { $bg='bg-danger'; $icon='fa-times-circle'; }
                            ?>
                            <div class="noti-item">
                                <div class="noti-content">
                                    <div class="noti-icon <?php echo $bg; ?>">
                                        <i class="fas <?php echo $icon; ?>"></i>
                                    </div>
                                    <div>
                                        <p style="margin: 0; font-weight: 500;"><?php echo $row['message']; ?></p>
                                        <small class="text-muted"><?php echo $row['is_read'] ? 'อ่านแล้ว' : 'ใหม่'; ?></small>
                                    </div>
                                </div>
                                <div class="noti-time">
                                    <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="padding: 30px; text-align: center; color: #aaa;">ไม่มีการแจ้งเตือน</div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>