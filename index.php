<?php
require 'koneksi.php';

$serverIp = "192.168.160.190"; // Ganti sesuai IP LAN kamu
$baseUrl = "http://$serverIp/absen_susbalan";
$message = "";
$isScan = isset($_GET['scan']);

if ($isScan) {
    $data = $_GET['scan'];
    $parts = explode("-", $data);

    if (count($parts) >= 2) {
        $id = intval($parts[0]);
        $nama = trim($parts[1]);

        $cekSiswa = $conn->query("SELECT * FROM siswa WHERE id = $id AND nama = '$nama'");
        if ($cekSiswa->num_rows > 0) {
            $cekAbsen = $conn->query("SELECT * FROM absensi WHERE siswa_id = $id AND DATE(waktu) = CURDATE()");
            if ($cekAbsen->num_rows > 0) {
                $message = "⚠️ <strong>$nama</strong> sudah absen hari ini.";
            } else {
                $conn->query("INSERT INTO absensi (siswa_id, nama) VALUES ($id, '$nama')");
                $message = "✅ Absensi berhasil untuk <strong>$nama</strong>!";
            }
        } else {
            $message = "❌ Data siswa tidak ditemukan.";
        }
    } else {
        $message = "❌ Format QR tidak valid.";
    }

    // Jika ini dari scan, tampilkan hanya pesan
    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Absensi</title>
        <style>
            body { font-family: sans-serif; padding: 40px; text-align: center; background: #f0f0f0; }
            .msg { display: inline-block; padding: 20px; background: #fff; border: 2px solid #2196F3; border-radius: 10px; font-size: 18px; }
        </style>
    </head>
    <body>
        <div class='msg'>$message</div>
    </body>
    </html>
    ";
    exit;
}

// Jika tidak dari scan (akses langsung), tampilkan dengan layout normal
$title = "Scan QR Absensi";
ob_start();
?>

<h2>📲 Scan QR & Hadir Otomatis</h2>

<div class="box">
    Scan pakai aplikasi seperti <strong>QR Scanner</strong>, dan buka link hasil scan:<br>
    <code><?= $baseUrl ?>/index.php?scan=ID-Nama</code><br><br>
    Contoh: <code><?= $baseUrl ?>/index.php?scan=5-Ahmad</code><br>
    <small>*Pastikan HP dan Laptop terhubung ke WiFi yang sama</small>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
