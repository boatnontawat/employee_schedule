<?php
require_once __DIR__ . '/../config.php';

// ตรวจสอบสิทธิ์: อนุญาตถ้าเป็น Admin, Super Admin หรือ พนักงานระดับ 3 ขึ้นไป
$user_level = $_SESSION['user_level'] ?? '';
$emp_level = $_SESSION['employee_level'] ?? 0;

$is_admin = in_array($user_level, ['admin', 'super_admin']);
$is_supervisor = ($emp_level >= 3);

if (!isLoggedIn() || (!$is_admin && !$is_supervisor)) {
    die("Access Denied: สำหรับหัวหน้างานระดับ 3 หรือผู้ดูแลระบบเท่านั้น");
}

// ดึงค่า Refresh Rate
$refresh_rate = 30;
$sql = "SELECT setting_value FROM security_settings WHERE setting_key = 'qr_refresh_rate_seconds'";
if ($result = mysqli_query($conn, $sql)) {
    if ($row = mysqli_fetch_assoc($result)) $refresh_rate = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จุดลงเวลา (QR Code)</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { background-color: #f8fafc; text-align: center; font-family: 'Sarabun', sans-serif; }
        .qr-container { 
            background: white; padding: 40px; border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            display: inline-block; margin-top: 50px;
            max-width: 90%; width: 400px;
        }
        #qrcode { margin: 20px auto; display: flex; justify-content: center; }
        .timer-bar { height: 5px; background: #3b82f6; width: 100%; transition: width 1s linear; }
        .info-box { background: #e0f2fe; color: #0369a1; padding: 10px; border-radius: 8px; margin-top: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="qr-container">
        <h2 style="color: #1e293b;"><i class="fas fa-clock"></i> จุดลงเวลาเข้า-ออก</h2>
        <p class="text-muted">สแกนเพื่อลงเวลา</p>
        
        <div id="qrcode"></div>
        
        <div style="margin-top:20px; background:#e2e8f0; height:5px; border-radius:5px; overflow:hidden;">
            <div id="progress" class="timer-bar"></div>
        </div>
        
        <h3 id="status_text" style="margin-top:20px; color:#3b82f6;">กำลังโหลด...</h3>

        <div class="info-box">
            <i class="fas fa-map-marker-alt"></i> ระบบกำลังใช้พิกัดของคุณเป็นจุดอ้างอิง
        </div>
        
        <div style="margin-top: 20px;">
            <button onclick="window.close()" class="btn btn-secondary btn-sm" style="background:#94a3b8; color:white; border:none; padding:5px 15px; border-radius:20px; cursor:pointer;">ปิดหน้าต่าง</button>
        </div>
    </div>

    <script>
    const refreshRate = <?php echo $refresh_rate; ?>;
    let timeLeft = refreshRate;
    let supervisorLat = null;
    let supervisorLng = null;

    // ขอพิกัดเครื่องหัวหน้า
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            supervisorLat = pos.coords.latitude;
            supervisorLng = pos.coords.longitude;
            generateToken();
        },
        (err) => {
            alert("กรุณาเปิด GPS และอนุญาตการเข้าถึงตำแหน่งเพื่อสร้างจุดลงเวลา");
            document.getElementById("status_text").innerText = "ต้องการ GPS";
            document.getElementById("status_text").style.color = "red";
        }
    );

    function generateToken() {
        if(supervisorLat === null || supervisorLng === null) return;

        fetch('api/gen_token.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `lat=${supervisorLat}&lng=${supervisorLng}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const scanUrl = `${window.location.origin}/employee-schedule/attendance/scan.php?t=${data.token}`;
                
                document.getElementById("qrcode").innerHTML = "";
                new QRCode(document.getElementById("qrcode"), {
                    text: scanUrl,
                    width: 250,
                    height: 250,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
                
                document.getElementById("status_text").innerText = "พร้อมสแกน (รีเฟรชใน " + timeLeft + " วินาที)";
                timeLeft = refreshRate;
            }
        })
        .catch(err => console.error("Gen Token Error:", err));
    }

    setInterval(() => {
        if(supervisorLat !== null) {
            timeLeft--;
            const percentage = (timeLeft / refreshRate) * 100;
            const bar = document.getElementById("progress");
            if(bar) bar.style.width = percentage + "%";
            
            if(timeLeft <= 0) {
                generateToken();
            } else {
                document.getElementById("status_text").innerText = "รีเฟรชใน " + timeLeft + " วินาที";
            }
        }
    }, 1000);
    </script>
</body>
</html>