<?php
require 'vendor/autoload.php';
require 'koneksi.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

$title = "Generate QR";
ob_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = $_POST['nama'];
    $conn->query("INSERT INTO siswa (nama) VALUES ('$nama')");
    $id = $conn->insert_id;

    $serverIp = "192.168.160.190"; // Ganti sesuai IP LAN kamu
    $qrText = "http://$serverIp/absen_susbalan/index.php?scan=" . $id . "-" . $nama;

    $builder = new Builder(
        writer: new PngWriter(),
        data: $qrText,
        encoding: new Encoding('UTF-8'),
        size: 300,
        margin: 10,
        labelText: $nama
    );

    $result = $builder->build();

    if (!is_dir("assets/qrcodes")) mkdir("assets/qrcodes", 0777, true);
    $filePath = "assets/qrcodes/qr_" . $id . ".png";
    $result->saveToFile($filePath);

    echo "<div class='msg'>
            <h3>✅ QR berhasil dibuat untuk <strong>$nama</strong></h3>
            <img src='$filePath' width='200'><br><br>
            <a href='siswa.php'>⬅️ Kembali ke Daftar Siswa</a>
          </div>";
    exit;
}
?>

<h2>➕ Tambah Siswa & Generate QR</h2>

<form method="POST" class="form-box">
    <label>Nama Siswa:</label><br>
    <input type="text" name="nama" required placeholder="Masukkan nama siswa"><br><br>
    <button type="submit">✅ Generate QR</button>
</form>

<style>
    .form-box {
        background: #f9f9f9;
        padding: 20px;
        border-left: 5px solid #4CAF50;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }
    input[type="text"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    button {
        background: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    button:hover {
        background: #45a049;
    }
    .msg {
        background: #e7ffe7;
        border: 1px solid #b2e2b2;
        padding: 20px;
        border-left: 5px solid green;
    }
</style>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
