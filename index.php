<?php
require 'koneksi.php';
$message = "";

if (isset($_GET['scan'])) {
    $data = $_GET['scan'];
    $parts = explode("-", $data, 2);
    if (count($parts) === 2) {
        $id = intval($parts[0]);
        $nama = urldecode(trim($parts[1]));

        $stmt = $conn->prepare("SELECT * FROM siswa WHERE id = ? AND LOWER(nama) = LOWER(?)");
        $stmt->bind_param("is", $id, $nama);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt2 = $conn->prepare("SELECT * FROM absensi WHERE siswa_id = ? AND DATE(waktu_absen) = CURDATE()");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $cekAbsen = $stmt2->get_result();

            if ($cekAbsen->num_rows > 0) {
                $message = "⚠️ <strong>" . htmlspecialchars($nama) . "</strong> sudah absen hari ini.";
            } else {
                $stmt3 = $conn->prepare("INSERT INTO absensi (siswa_id, waktu_absen, status) VALUES (?, NOW(), 'hadir')");
                $stmt3->bind_param("i", $id);
                if ($stmt3->execute()) {
                    $message = "✅ Absensi berhasil untuk <strong>" . htmlspecialchars($nama) . "</strong>!";
                } else {
                    $message = "❌ Gagal menyimpan absensi.";
                }
            }
        } else {
            $message = "❌ Data siswa tidak ditemukan.<br><small>ID: $id | Nama: " . htmlspecialchars($nama) . "</small>";
        }
    } else {
        $message = "❌ Format QR tidak valid.";
    }

    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Hasil Absensi</title>
        <style>
            body { font-family: Arial; text-align: center; padding: 40px; background: #f9f9f9; }
            .message {
                background: #fff; padding: 20px; border-radius: 10px;
                display: inline-block; font-size: 18px; border: 1px solid #ddd;
            }
            .buttons {
                margin-top: 20px;
            }
            .buttons a {
                display: inline-block;
                margin: 5px;
                padding: 10px 15px;
                background-color: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 5px;
            }
            .buttons a:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class='message'>$message</div>
        <div class='buttons'>
            <a href='index.php'>🔄 Scan Lagi</a>
            <a href='siswa.php'>⬅️ Kembali ke Daftar Siswa</a>
        </div>
    </body>
    </html>
    ";
    exit;
}
?>

<!-- Tampilan Scanner -->
<h2>📲 Scan QR untuk Absen</h2>
<div id="reader" style="width: 100%; max-width: 400px;"></div>
<div id="result" style="margin-top: 20px; font-size: 18px;"></div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function onScanSuccess(decodedText) {
    html5QrcodeScanner.clear().then(_ => {
        document.getElementById("result").innerHTML = `📡 Mengarahkan ke absensi...`;
        window.location.href = "index.php?scan=" + encodeURIComponent(decodedText);
    }).catch(error => console.error("Gagal menghentikan scanner", error));
}
const html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
html5QrcodeScanner.render(onScanSuccess);
</script>

<style>
#reader {
    margin: auto;
    padding: 10px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
}
</style>
