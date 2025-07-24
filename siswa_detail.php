<?php
require 'koneksi.php';
$title = "Detail Siswa";
ob_start();

$id = intval($_GET['id']);
$data = $conn->query("SELECT * FROM siswa WHERE id = $id");
if ($data->num_rows === 0) {
    echo "<p>❌ Siswa tidak ditemukan.</p>";
} else {
    $siswa = $data->fetch_assoc();

    // Hitung jumlah kehadiran dari tabel absensi
    $hadir = $conn->query("SELECT COUNT(*) AS total FROM absensi WHERE siswa_id = $id")->fetch_assoc();
    $jumlahHadir = $hadir['total'];
    ?>

    <h2>📄 Detail Siswa</h2>
    <table cellpadding="10">
        <tr>
            <td><strong>Nama:</strong></td>
            <td><?= htmlspecialchars($siswa['nama']) ?></td>
        </tr>
        <tr>
            <td><strong>ID:</strong></td>
            <td><?= $siswa['id'] ?></td>
        </tr>
        <tr>
            <td><strong>Jumlah Kehadiran:</strong></td>
            <td><strong><?= $jumlahHadir ?></strong> kali hadir</td>
        </tr>
        <tr>
            <td><strong>QR Code:</strong></td>
            <td><img src="assets/qrcodes/qr_<?= $siswa['id'] ?>.png" width="150"></td>
        </tr>
    </table>

    <br>
    <a href="siswa.php" style="text-decoration:none; padding:10px 15px; background:#2196F3; color:white; border-radius:5px;">⬅️ Kembali ke Daftar</a>

<?php
}
$content = ob_get_clean();
include 'includes/layout.php';
?>
