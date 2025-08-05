<?php
session_start();
ob_start(); // Mulai output buffering
include '../../koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak");
}

$folder_qr = "../../assets/qr/";
$zip_name = "QR_Code_Siswa_" . date('Y-m-d') . ".zip";

$zip_path = tempnam(sys_get_temp_dir(), 'zip');
$zip = new ZipArchive();

if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
    die("Tidak dapat membuat file ZIP");
}

$files = glob($folder_qr . "*.png");

if (empty($files)) {
    die("Tidak ada file QR Code yang ditemukan.");
}

foreach ($files as $file) {
    $filename = basename($file); // contoh: 5.png
    $id = pathinfo($filename, PATHINFO_FILENAME);

    $q = mysqli_query($conn, "SELECT nama_lengkap, utusan_pac_ranting FROM siswa WHERE id='$id'");
    $data = mysqli_fetch_assoc($q);

    if ($data) {
        $nama_siswa = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['nama_lengkap']);
        $utusan = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['utusan_pac_ranting']);
        $new_filename = "{$nama_siswa}_{$utusan}.png";
    } else {
        $new_filename = $filename;
    }

    $zip->addFile($file, $new_filename);
}

// Tutup ZIP
$zip->close();

// Bersihkan semua output buffer
ob_end_clean();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zip_name . '"');
header('Content-Length: ' . filesize($zip_path));
readfile($zip_path);

unlink($zip_path);
exit;

