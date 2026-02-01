<?php
require_once __DIR__ . '/../config.php';

// ถ้ายังไม่ล็อกอิน ให้เด้งไปล็อกอินก่อน แล้วส่งกลับมาหน้านี้
if (!isLoggedIn()) {
    $_SESSION['redirect_url'] = "attendance/scan.php?t=" . $_GET['t'];
    header("location: ../login.php");
    exit;
}

$token = isset($_GET['t']) ? $_GET['t'] : '';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กำลังยืนยันตัวตน...</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container" style="justify-content:center; align-items:center; text-align:center; min-height: 100vh;">
        
        <div class="card" style="padding:30px; max-width:400px; width: 90%;">
            
            <i class="fas fa-satellite-dish fa-3x fa-spin" style="color:var(--accent);"></i>
            
            <h3 style="margin-top:20px;">กำลังระบุตำแหน่งของคุณ...</h3>
            <p style="color: #64748b; margin-bottom: 20px;">กรุณาอนุญาตให้เข้าถึง GPS เพื่อยืนยันการเข้างาน</p>
            
            <div id="result_area"></div>

            <div style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <a href="../user_dashboard.php" class="btn btn-secondary" style="width: 100%; border-radius: 50px; background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                    <i class="fas fa-arrow-left"></i> กลับหน้าหลัก
                </a>
            </div>

        </div>
    </div>

    <script>
    const token = "<?php echo htmlspecialchars($token); ?>";
    
    document.addEventListener("DOMContentLoaded", function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(sendAttendanceData, showError, {
                enableHighAccuracy: true,
                timeout: 10000, // เพิ่มเวลา Timeout เป็น 10 วิ
                maximumAge: 0
            });
        } else {
            Swal.fire('Error', 'Browser ของคุณไม่รองรับ GPS', 'error');
        }
    });

    function sendAttendanceData(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        const accuracy = position.coords.accuracy;

        // ถาม User ว่าจะ Check-in หรือ Check-out
        Swal.fire({
            title: 'เลือกรายการ',
            text: 'คุณต้องการบันทึกเวลา เข้า หรือ ออก?',
            icon: 'question',
            showDenyButton: true,
            showCancelButton: true, // เพิ่มปุ่ม Cancel ตรงนี้ด้วย
            confirmButtonText: 'เข้างาน (Check In)',
            confirmButtonColor: '#10b981',
            denyButtonText: 'ออกงาน (Check Out)',
            denyButtonColor: '#ef4444',
            cancelButtonText: 'ยกเลิก',
            allowOutsideClick: false
        }).then((result) => {
            let action = '';
            if (result.isConfirmed) action = 'check_in';
            else if (result.isDenied) action = 'check_out';
            else {
                // ถ้ากดยกเลิก ให้เด้งกลับหน้า Dashboard
                window.location.href = '../user_dashboard.php';
                return;
            }

            submitData(action, lat, lng, accuracy);
        });
    }

    function submitData(action, lat, lng, accuracy) {
        // แสดง Loading ระหว่างส่งข้อมูล
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('token', token);
        formData.append('action', action);
        formData.append('lat', lat);
        formData.append('lng', lng);
        formData.append('accuracy', accuracy);

        fetch('api/process_scan.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '../user_dashboard.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: data.message,
                    confirmButtonText: 'ตกลง'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server', 'error');
        });
    }

    function showError(error) {
        let msg = 'ไม่สามารถระบุพิกัดได้';
        switch(error.code) {
            case error.PERMISSION_DENIED:
                msg = "กรุณากด 'อนุญาต' (Allow) ให้เข้าถึงตำแหน่ง GPS";
                break;
            case error.POSITION_UNAVAILABLE:
                msg = "สัญญาณ GPS ไม่เสถียร";
                break;
            case error.TIMEOUT:
                msg = "หมดเวลาในการค้นหาพิกัด";
                break;
        }
        Swal.fire({
            icon: 'warning',
            title: 'GPS Error',
            text: msg,
            confirmButtonText: 'ลองใหม่'
        }).then(() => {
            location.reload(); // รีโหลดหน้าเพื่อขอลองใหม่
        });
    }
    </script>
</body>
</html>