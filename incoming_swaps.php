<?php
include 'config.php';

if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- SQL Query ---
$sql = "SELECT sr.id, u.full_name as requester_name, 
        sr.reason, sr.created_at,
        COALESCE(s1.schedule_date, 'ไม่พบข้อมูล') as my_date, 
        COALESCE(s1.shift_type, '') as my_shift,
        COALESCE(s2.schedule_date, 'ไม่พบข้อมูล') as their_date, 
        COALESCE(s2.shift_type, '') as their_shift
        FROM swap_requests sr
        LEFT JOIN users u ON sr.user_id = u.id
        LEFT JOIN schedules s1 ON sr.target_schedule_id = s1.id   -- เวรของเรา (User B)
        LEFT JOIN schedules s2 ON sr.original_schedule_id = s2.id -- เวรของเขา (User A)
        WHERE sr.target_user_id = ? AND sr.status = 'pending'
        ORDER BY sr.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Helper แปลงกะ (แก้ไขให้เป็นภาษาไทยครบถ้วน)
function getShiftName($shift) {
    if(!$shift || $shift == '') return '-';
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
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>คำขอรออนุมัติ</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .request-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 15px; padding: 15px; border-left: 5px solid #f6c23e; }
        .req-header { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .req-name { font-weight: bold; color: #4e73df; font-size: 1.1em; }
        .req-time { color: #888; font-size: 0.85em; }
        .swap-detail { display: flex; align-items: center; justify-content: space-around; background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .swap-box { text-align: center; }
        .swap-date { font-weight: bold; display: block; }
        .swap-shift { font-size: 0.9em; }
        .swap-arrow { color: #888; font-size: 1.2em; }
        .req-reason { font-style: italic; color: #666; margin-bottom: 15px; font-size: 0.9em; }
        .action-btn { width: 100px; }
        
        /* Badge style */
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
        
        /* Custom Toast CSS */
        .colored-toast {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            border-radius: 12px !important;
            padding: 1rem !important;
            background: #fff !important;
        }
        .toast-title-custom {
            font-size: 1rem !important;
            font-weight: 600 !important;
            color: #333 !important;
            margin-bottom: 5px !important;
        }
        .toast-content-custom {
            font-family: 'Sarabun', sans-serif !important;
            line-height: 1.5 !important;
            color: #555 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบจัดการเวรพนักงาน</h1>
            <div class="user-info">
                <span>สวัสดี, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="btn btn-secondary btn-sm"><i class="fas fa-sign-out-alt"></i> ออก</a>
            </div>
        </header>

        <div class="dashboard-container">
            <nav class="tapbar">
                <ul class="tapbar-menu">
                    <li><a href="user_dashboard.php"><i class="fas fa-home"></i>แดชบอร์ด</a></li>
                    <li>
                        <a href="incoming_swaps.php" class="active">
                            <i class="fas fa-inbox"></i> คำขอรออนุมัติ
                            <span id="incomingSwapBadge" class="badge-notification" style="display:none;"></span>
                        </a>
                    </li>
                    <li><a href="request_leave.php"><i class="fas fa-stethoscope"></i>ขอลาป่วย</a></li>
                    <li><a href="request_swap.php"><i class="fas fa-exchange-alt"></i>ขอสลับเวร</a></li>
                    <li><a href="request_holiday.php"><i class="fas fa-umbrella-beach"></i>ขอวันหยุด</a></li>
                    <li><a href="request_future_holiday.php"><i class="fas fa-calendar-plus"></i>ขอลาหยุดล่วงหน้า</a></li>
                    <li><a href="my_requests.php"><i class="fas fa-history"></i>ประวัติคำขอ</a></li>
                </ul>
            </nav>

            <main class="content-area">
                <div class="page-header">
                    <h2><i class="fas fa-inbox"></i> คำขอสลับเวรที่รอคุณอนุมัติ</h2>
                </div>

                <?php 
                mysqli_data_seek($result, 0);
                if (mysqli_num_rows($result) > 0): 
                ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="request-card">
                            <div class="req-header">
                                <span class="req-name"><i class="fas fa-user"></i> <?php echo $row['requester_name']; ?></span>
                                <span class="req-time">ส่งเมื่อ: <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></span>
                            </div>
                            
                            <div class="swap-detail">
                                <div class="swap-box">
                                    <small>เวรของคุณ</small>
                                    <span class="swap-date text-danger">
                                        <?php echo ($row['my_date']!='ไม่พบข้อมูล') ? date('d/m/Y', strtotime($row['my_date'])) : 'ไม่พบข้อมูล'; ?>
                                    </span>
                                    <span class="swap-shift badge badge-secondary"><?php echo getShiftName($row['my_shift']); ?></span>
                                </div>
                                <div class="swap-arrow"><i class="fas fa-exchange-alt"></i></div>
                                <div class="swap-box">
                                    <small>แลกกับ</small>
                                    <span class="swap-date text-success">
                                        <?php echo ($row['their_date']!='ไม่พบข้อมูล') ? date('d/m/Y', strtotime($row['their_date'])) : 'ไม่พบข้อมูล'; ?>
                                    </span>
                                    <span class="swap-shift badge badge-secondary"><?php echo getShiftName($row['their_shift']); ?></span>
                                </div>
                            </div>

                            <div class="req-reason">
                                <strong>เหตุผล:</strong> <?php echo $row['reason'] ? $row['reason'] : '-'; ?>
                            </div>

                            <div class="form-actions" style="text-align: right;">
                                <button onclick="processSwap(<?php echo $row['id']; ?>, 'accept')" class="btn btn-success action-btn"><i class="fas fa-check"></i> ตกลง</button>
                                <button onclick="processSwap(<?php echo $row['id']; ?>, 'reject')" class="btn btn-danger action-btn"><i class="fas fa-times"></i> ปฏิเสธ</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state" style="text-align: center; padding: 50px; color: #ccc;">
                        <i class="fas fa-check-circle" style="font-size: 4rem;"></i>
                        <p style="margin-top: 10px;">ไม่มีคำขอที่รอการอนุมัติ</p>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function processSwap(requestId, action) {
        let actionText = action === 'accept' ? 'อนุมัติการแลกเวรนี้' : 'ปฏิเสธคำขอนี้';
        let confirmBtnColor = action === 'accept' ? '#28a745' : '#dc3545';

        Swal.fire({
            title: 'ยืนยันการทำรายการ?',
            text: `คุณต้องการ${actionText} ใช่หรือไม่?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'กำลังประมวลผล...', didOpen: () => Swal.showLoading() });

                const formData = new FormData();
                formData.append('request_id', requestId);
                formData.append('action', action);

                fetch('api/respond_swap.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('สำเร็จ', data.message, 'success').then(() => {
                            location.reload(); 
                        });
                    } else {
                        Swal.fire('ผิดพลาด', data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                });
            }
        });
    }
    
    // --- Notification & Badge Logic ---
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

    function checkUserAlerts() {
        fetch('api/get_unread_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.found && data.data.length > 0) {
                    data.data.forEach(noti => {
                        showSystemNotification(noti.message, noti.type, noti.time);
                        markNotificationAsRead(noti.id);
                    });
                }
            })
            .catch(err => console.error('Alert fetch error:', err));
    }

    function markNotificationAsRead(notiId) {
        fetch('api/mark_as_read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `noti_id=${notiId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                checkUserBadge();
            }
        })
        .catch(err => console.error('Mark as read failed:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        checkUserBadge(); 
        checkUserAlerts();
        
        setInterval(checkUserBadge, 3000); 
        setInterval(checkUserAlerts, 5000);
    });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>