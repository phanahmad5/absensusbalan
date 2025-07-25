<?php
require 'koneksi.php';

if (!isset($_GET['scan'])) {
    echo "<h3>❌ QR Code tidak valid.</h3>";
    exit;
}

$scanData = $_GET['scan'];
// Format: id-nama (contoh: 12-Ahmad)
list($id, $nama) = explode('-', $scanData, 2);

// Cek apakah ID siswa valid
$stmt = $conn->prepare("SELECT * FROM siswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h3>❌ Siswa tidak ditemukan.</h3>";
    exit;
}

// Cek apakah sudah absen hari ini
$tgl = date('Y-m-d');
$check = $conn->prepare("SELECT * FROM absensi WHERE siswa_id = ? AND DATE(waktu) = ?");
$check->bind_param("is", $id, $tgl);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    echo "<div class='msg'>
            <h3>✅ Halo <strong>$nama</strong>, Anda sudah absen hari ini!</h3>
          </div>";
    exit;
}

// Insert ke tabel absensi
$insert = $conn->prepare("INSERT INTO absensi (siswa_id) VALUES (?)");
$insert->bind_param("i", $id);
$insert->execute();

echo "<div class='msg'>
        <h3>✅ Selamat datang, <strong>$nama</strong>! Anda berhasil absen hari ini.</h3>
      </div>";
?>

<style>
    .msg {
        background: #e7ffe7;
        border: 1px solid #b2e2b2;
        padding: 20px;
        border-left: 5px solid green;
        font-family: sans-serif;
        max-width: 500px;
        margin: 30px auto;
        text-align: center;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }
</style>
