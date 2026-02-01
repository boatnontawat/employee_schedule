<?php
include 'config.php';

// ตรวจสอบล็อกอิน
if (!isLoggedIn()) {
    header("location: login.php");
    exit;
}

// กำหนดปุ่มย้อนกลับตาม Level
$back_url = ($_SESSION['user_level'] == 'admin') ? 'admin_dashboard.php' : 'user_dashboard.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>สแกน QR Code</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    /* ปรับแต่งหน้าสแกนโดยเฉพาะ */
    body { 
        background-color: #000; 
        color: #fff; 
        overflow: hidden; 
        margin: 0; 
        padding: 0;
    }
    
    .scanner-wrapper {
        position: relative;
        width: 100%;
        height: 100vh; /* เต็มความสูงหน้าจอ */
        overflow: hidden;
    }

    /* พื้นที่แสดงกล้อง */
    #reader {
        width: 100% !important;
        height: 100% !important;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
        overflow: hidden;
    }

    /* --- [ส่วนสำคัญที่แก้] บังคับให้ Video ยืดเต็มจอและไม่เสียสัดส่วน --- */
    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important; /* ให้ภาพเต็มจอโดยไม่บีบ */
        border-radius: 0 !important;
    }

    /* UI ด้านบน (ปุ่มกลับ) */
    .scanner-header {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 20; /* เพิ่ม Z-index ให้ลอยเหนือ overlay */
        width: calc(100% - 40px);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-back {
        background: rgba(0, 0, 0, 0.4); /* พื้นหลังเข้มขึ้นนิดหน่อยให้อ่านง่าย */
        backdrop-filter: blur(5px);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        padding: 8px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    /* Container สำหรับจัดกลาง Overlay */
    .overlay-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        pointer-events: none; /* ให้กดทะลุไปโดน video ได้ถ้าจำเป็น */
    }

    /* กรอบ Focus ตรงกลาง */
    .scan-overlay {
        position: relative;
        width: 260px; /* ลดขนาดลงเล็กน้อยให้พอดีจอมือถือเล็ก */
        height: 260px;
        /* ใช้ box-shadow สร้างขอบดำทึบรอบๆ กรอบสแกน */
        box-shadow: 0 0 0 100vmax rgba(0, 0, 0, 0.6); 
        border-radius: 20px;
        pointer-events: auto;
    }

    /* เส้นขอบโฟกัส 4 มุม */
    .scan-overlay::before, .scan-overlay::after {
        content: '';
        position: absolute;
        width: 40px;
        height: 40px;
        border: 4px solid #3b82f6;
        transition: all 0.3s;
    }
    
    .scan-overlay::before { top: 0; left: 0; border-right: 0; border-bottom: 0; border-radius: 16px 0 0 0; }
    .scan-overlay::after { bottom: 0; right: 0; border-left: 0; border-top: 0; border-radius: 0 0 16px 0; }

    .corner-tr-bl {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
    }
    .corner-tr-bl::before {
        content: ''; position: absolute; top: 0; right: 0; width: 40px; height: 40px;
        border: 4px solid #3b82f6; border-left: 0; border-bottom: 0; border-radius: 0 16px 0 0;
    }
    .corner-tr-bl::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 40px; height: 40px;
        border: 4px solid #3b82f6; border-right: 0; border-top: 0; border-radius: 0 0 0 16px;
    }

    /* เส้นสแกนวิ่งขึ้นลง */
    .scan-line {
        width: 100%;
        height: 2px;
        background: #3b82f6;
        box-shadow: 0 0 10px #3b82f6;
        position: absolute;
        top: 0;
        animation: scanMove 2s infinite linear;
    }

    @keyframes scanMove {
        0% { top: 0; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }

    .scan-text {
        margin-top: 20px;
        color: #fff;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        font-size: 1rem;
        background: rgba(0,0,0,0.3);
        padding: 5px 15px;
        border-radius: 20px;
    }
</style>
</head>
    <body>
    <div class="scanner-wrapper">
        <div id="reader"></div>

        <div class="scanner-header">
            <a href="<?php echo $back_url; ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
            <div style="background:rgba(0,0,0,0.5); padding:5px 12px; border-radius:20px; font-size:0.8rem; display:flex; align-items:center; gap:5px;">
                <i class="fas fa-camera"></i> สแกน
            </div>
        </div>

        <div class="overlay-container">
            <div class="scan-overlay">
                <div class="corner-tr-bl"></div>
                <div class="scan-line"></div>
            </div>
            <div class="scan-text">
                วาง QR Code ในกรอบ
            </div>
        </div>
    </div>

    <script>
    let html5QrcodeScanner;

    function startScanner() {
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        // --- [ส่วนที่แก้] ลบ aspectRatio ออก และปรับขนาด qrbox ---
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            // aspectRatio: window.innerWidth / window.innerHeight  <-- ลบบรรทัดนี้ออกครับ
        };

        html5QrcodeScanner.start(
            { facingMode: "environment" }, 
            config,
            (decodedText, decodedResult) => {
                // ... (โค้ดส่วนเดิม) ...
                if (decodedText.includes("attendance/scan.php")) {
                     html5QrcodeScanner.stop().then(() => {
                        window.location.href = decodedText;
                    });
                } else {
                     Swal.fire({
                        title: 'QR Code ไม่ถูกต้อง',
                        text: 'กรุณาสแกน QR Code สำหรับลงเวลาเท่านั้น',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            (errorMessage) => {
                // ignore scanning errors
            }
        ).catch(err => {
            // ... (Error handling เดิม) ...
        });
    }

    document.addEventListener('DOMContentLoaded', startScanner);
    </script>
</body>
</html>