<?php
require 'koneksi.php';

$title = "Dashboard";

// Query untuk menghitung jumlah siswa
$jumlahSiswa = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM siswa");
if ($row = $result->fetch_assoc()) {
    $jumlahSiswa = $row['total'];
}

ob_start();
?>

<h2>Dashboard</h2>
<p>Selamat datang di sistem absensi QR Code! Untuk siswa susbalan</p>

<div class="box">
    <h3>👨‍🎓 Jumlah Siswa Terdaftar: <?= $jumlahSiswa ?></h3>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
