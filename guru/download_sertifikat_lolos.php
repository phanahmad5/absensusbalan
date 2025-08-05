<?php
session_start();
ob_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    die("Akses ditolak");
}

include '../koneksi.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$tmpDir = __DIR__ . "/temp_sertifikat/";
if (!is_dir($tmpDir)) mkdir($tmpDir);

// Bersihkan hanya PDF lama
foreach (glob("$tmpDir*.pdf") as $f) unlink($f);

// Ambil siswa yang hadir >= 4 materi
$query = "
    SELECT s.id, s.nama_lengkap, s.utusan_pac_ranting,
        (SELECT COUNT(DISTINCT materi_id) FROM absen WHERE siswa_id = s.id) AS total_materi
    FROM siswa s
    HAVING total_materi >= 4
";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Gagal ambil data siswa: " . mysqli_error($conn));
}

$pdf_files = [];
while ($row = mysqli_fetch_assoc($result)) {
    $nama = $row['nama_lengkap'];
    $utusan = $row['utusan_pac_ranting'];
    $totalHadir = $row['total_materi'];

    $safe_nama = preg_replace('/[^a-zA-Z0-9_]/', '_', $nama); 
    $filename = $tmpDir . $safe_nama . ".pdf";

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->setPaper('A4', 'landscape');

    $html = "
    <html><body style='text-align:center; padding-top:100px; font-family:Arial,sans-serif'>
        <h1>SERTIFIKAT</h1>
        <h2 style='color:green;'>$nama</h2>
        <p>Utusan: $utusan</p>
        <p>Telah mengikuti $totalHadir materi pelatihan.</p>
        <p style='margin-top:40px;'>Tanggal Cetak: " . date('d F Y') . "</p>
    </body></html>
    ";

    $dompdf->loadHtml($html);
    $dompdf->render();
    file_put_contents($filename, $dompdf->output());
    $pdf_files[] = $filename;
}

if (count($pdf_files) == 0) {
    echo "<script>alert('Tidak ada siswa yang memenuhi syarat (≥ 4 materi).'); window.close();</script>";
    exit;
}

// Buat file ZIP
$zipName = "sertifikat_lolos_" . date('Ymd_His') . ".zip";
$zipPath = $tmpDir . $zipName;

$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Gagal membuat file ZIP");
}

foreach ($pdf_files as $pdf) {
    $zip->addFile($pdf, basename($pdf));
}
$zip->close();

if (!file_exists($zipPath)) {
    die("ZIP tidak berhasil dibuat.");
}

// Set header dan kirim file ke browser
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipPath) . '"');
header('Content-Length: ' . filesize($zipPath));
readfile($zipPath);

// Bersihkan
foreach ($pdf_files as $f) unlink($f);
unlink($zipPath);
rmdir($tmpDir);
exit;
