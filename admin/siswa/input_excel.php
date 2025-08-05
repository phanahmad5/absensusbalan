<?php
require '../../vendor/autoload.php'; // PhpSpreadsheet & Endroid QR Code
include '../../koneksi.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$importedData = [];

if (isset($_POST['upload'])) {
    $file = $_FILES['file']['tmp_name'];
    if ($file) {
        // Load spreadsheet
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet()->toArray();

        for ($i = 1; $i < count($sheet); $i++) { // mulai dari baris 2 (index 1), karena baris 1 header
            $row = $sheet[$i];

            // Ambil dan trim data sesuai kolom Excel
            $nama_lengkap       = mysqli_real_escape_string($conn, trim($row[0] ?? ''));
            $nik                = mysqli_real_escape_string($conn, trim($row[1] ?? ''));
            $no_kta             = mysqli_real_escape_string($conn, trim($row[2] ?? ''));
            $ttl                = mysqli_real_escape_string($conn, trim($row[3] ?? ''));
            $alamat_lengkap     = mysqli_real_escape_string($conn, trim($row[4] ?? ''));
            $tempat_pkd         = mysqli_real_escape_string($conn, trim($row[5] ?? ''));
            $no_hp              = mysqli_real_escape_string($conn, trim($row[6] ?? ''));
            $utusan_pac_ranting = mysqli_real_escape_string($conn, trim($row[7] ?? ''));
            $bukti_transfer     = mysqli_real_escape_string($conn, trim($row[8] ?? ''));
            $surat_bukti        = mysqli_real_escape_string($conn, trim($row[9] ?? ''));

            // Buat isi QR: "nama_lengkap|utusan"
            $qrContent = "$nama_lengkap|$utusan_pac_ranting";

            // Insert data siswa
            $sql = "INSERT INTO siswa 
                    (nama_lengkap, nik, no_kta, ttl, alamat_lengkap, tempat_pkd, no_hp, utusan_pac_ranting, bukti_transfer, surat_bukti, qr_code) 
                    VALUES 
                    ('$nama_lengkap', '$nik', '$no_kta', '$ttl', '$alamat_lengkap', '$tempat_pkd', '$no_hp', '$utusan_pac_ranting', '$bukti_transfer', '$surat_bukti', '$qrContent')";
            $query = mysqli_query($conn, $sql);

            if ($query) {
                $id = mysqli_insert_id($conn);

                // Generate QR Code
                $qr = new QrCode($qrContent);
                $writer = new PngWriter();
                $result = $writer->write($qr);

                // Simpan file QR di folder assets/qr dengan nama {id}.png
                file_put_contents("../../assets/qr/{$id}.png", $result->getString());

                // Simpan data untuk preview
                $importedData[] = [
                    'id' => $id,
                    'nama' => $nama_lengkap,
                    'nik' => $nik,
                    'no_kta' => $no_kta,
                    'qr_code' => "../../assets/qr/{$id}.png"
                ];
            }
        }
    }
}
?>

<?php include '../../layouts/header.php'; ?>
<?php include '../../layouts/sidebar.php'; ?>

<div id="content">
    <?php include '../../layouts/topbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Import Data Siswa dari Excel</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload File Excel (.xlsx)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx" required>
                    </div>
                    <button type="submit" name="upload" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import
                    </button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>

                <hr>
                <p>Pastikan file Excel kamu memiliki kolom sesuai urutan berikut:</p>
                <ol>
                    <li>Nama Lengkap</li>
                    <li>NIK</li>
                    <li>No. KTA</li>
                    <li>Tempat, Tanggal Lahir</li>
                    <li>Alamat Lengkap</li>
                    <li>Tempat PKD/Diklatsar</li>
                    <li>No HP</li>
                    <li>Utusan PAC dan Ranting</li>
                    <li>Bukti Transfer (nama file, opsional)</li>
                    <li>Surat Bukti (nama file, opsional)</li>
                </ol>
            </div>
        </div>

        <?php if (!empty($importedData)): ?>
        <div class="card shadow mb-4 mt-4">
            <div class="card-header bg-success text-white">Hasil Import:</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>NIK</th>
                            <th>No KTA</th>
                            <th>QR Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($importedData as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['nama']) ?></td>
                            <td><?= htmlspecialchars($d['nik']) ?></td>
                            <td><?= htmlspecialchars($d['no_kta']) ?></td>
                            <td><img src="<?= htmlspecialchars($d['qr_code']) ?>" width="80" alt="QR Code"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
