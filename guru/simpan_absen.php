<?php
session_start();
require_once '../koneksi.php';
header('Content-Type: application/json');

// Validasi sesi login guru
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

// Validasi input
$qr        = isset($_POST['qr']) ? trim($_POST['qr']) : '';
$materi_id = isset($_POST['materi_id']) ? intval($_POST['materi_id']) : 0;

if (empty($qr) || empty($materi_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Data QR atau materi tidak valid.']);
    exit;
}

date_default_timezone_set('Asia/Jakarta');
$waktuSekarang  = date('Y-m-d H:i:s');
$tanggalHariIni = date('Y-m-d');

// Karena format QR adalah "nama_lengkap|utusan"
list($nama_lengkap, $utusan) = explode('|', $qr);

// Cari siswa berdasarkan qr_code yang persis sama
$stmt = $conn->prepare("SELECT id, nama_lengkap FROM siswa WHERE qr_code = ?");
$stmt->bind_param("s", $qr);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
    echo json_encode(['status' => 'error', 'message' => 'QR tidak valid atau siswa tidak ditemukan.']);
    exit;
}

$siswa_id   = $siswa['id'];
$nama_siswa = htmlspecialchars($siswa['nama_lengkap']);

// Cek apakah siswa sudah absen pada materi dan tanggal yang sama
$cek = $conn->prepare("SELECT id FROM absen WHERE siswa_id = ? AND materi_id = ? AND DATE(jam) = ?");
$cek->bind_param("iis", $siswa_id, $materi_id, $tanggalHariIni);
$cek->execute();
$cek_result = $cek->get_result();

if ($cek_result->num_rows > 0) {
    echo json_encode([
        'status'  => 'info',
        'message' => "Siswa <strong>$nama_siswa</strong> sudah absen hari ini."
    ]);
    exit;
}

// Simpan data absensi
$insert = $conn->prepare("INSERT INTO absen (siswa_id, materi_id, jam, status) VALUES (?, ?, ?, 'Hadir')");
$insert->bind_param("iis", $siswa_id, $materi_id, $waktuSekarang);

if ($insert->execute()) {
    echo json_encode([
        'status'  => 'success',
        'message' => "Absensi berhasil untuk <strong>$nama_siswa</strong>."
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal menyimpan absensi.'
    ]);
}
