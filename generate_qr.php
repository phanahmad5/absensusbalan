<?php
require 'vendor/autoload.php';
require 'koneksi.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = htmlspecialchars(trim($_POST['nama']));

    $stmt = $conn->prepare("INSERT INTO siswa (nama) VALUES (?)");
    $stmt->bind_param("s", $nama);
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        $qrText = $id . "-" . urlencode($nama);

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

        echo "<h3>✅ QR berhasil dibuat untuk <strong>$nama</strong></h3>";
        echo "<img src='$filePath'><br><a href='siswa.php'>Kembali ke daftar siswa</a>";
        exit;
    } else {
        echo "❌ Gagal menyimpan data siswa: " . $conn->error;
    }
}
?>
<h2>🎓 Buat QR Siswa</h2>
<form method="POST">
    <label>Nama Siswa:</label>
    <input type="text" name="nama" required>
    <button type="submit">Generate QR</button>
</form>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
