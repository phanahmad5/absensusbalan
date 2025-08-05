<?php
ob_start();
require '../vendor/autoload.php';
include '../koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Validasi siswa_id
$siswa_id = isset($_GET['siswa_id']) ? (int)$_GET['siswa_id'] : 0;
if ($siswa_id <= 0) {
    die("Siswa tidak valid.");
}

// Ambil data siswa
$qSiswa = mysqli_query($conn, "SELECT * FROM siswa WHERE id = $siswa_id");
$siswa = mysqli_fetch_assoc($qSiswa);

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

// Hitung jumlah kehadiran (materi yang diikuti)
$qHadir = mysqli_query($conn, "SELECT COUNT(DISTINCT materi_id) AS total FROM absen WHERE siswa_id = $siswa_id");
$rHadir = mysqli_fetch_assoc($qHadir);
$totalHadir = $rHadir ? (int)$rHadir['total'] : 0;

if ($totalHadir < 4) {
    die("Maaf, siswa ini belum mengikuti minimal 4 materi.");
}

// Inisialisasi Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'landscape');

// Gambar/asset
$logo1 = 'http://localhost/absensusbalan/assets/image/logosus.png';
$logo2 = 'http://localhost/absensusbalan/assets/image/ansor.png';
$ornamen = 'http://localhost/absensusbalan/assets/image/ornamen.png';
$ttd = 'http://localhost/absensusbalan/assets/image/ttd.png';

$tanggal_cetak = date("d F Y");

$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0px; }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 60px;
            background: #fff;
            position: relative;
        }
        .ornamen-top { position: absolute; top: 0; right: 0; width: 180px; }
        .ornamen-bottom { position: absolute; bottom: 0; left: 0; width: 180px; }

        .logo-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .logo-wrapper img {
            width: 100px;
        }

        .judul {
            font-size: 38px;
            font-weight: bold;
            color: #c09300;
            text-align: center;
            text-shadow: 1px 1px 2px #888;
            margin-top: 30px;
        }

        .subjudul, .ucapan, .tanggal {
            text-align: center;
            color: #444;
        }

        .subjudul { font-size: 18px; margin-top: 10px; }
        .nama {
            font-size: 30px;
            font-weight: bold;
            margin: 20px 0;
            color: #007055;
            text-transform: uppercase;
            text-align: center;
        }

        .ucapan {
            font-size: 18px;
            margin: 20px 0;
        }

        .tanggal {
            font-size: 15px;
            margin-top: 20px;
            line-height: 1.4;
        }

        .ttd {
            margin-top: 50px;
            text-align: center;
        }
        .ttd-img {
            height: 80px;
            margin-bottom: 5px;
        }
        .nama-ttd {
            font-weight: bold;
            font-size: 16px;
            margin-top: 5px;
        }
        .jabatan-ttd {
            font-size: 14px;
            margin-top: 2px;
            color: #333;
        }
    </style>
</head>
<body>

    <img src="' . $ornamen . '" class="ornamen-top">
    <img src="' . $ornamen . '" class="ornamen-bottom">

    <div class="logo-wrapper">
        <img src="' . $logo1 . '">
        <img src="' . $logo2 . '">
    </div>

    <div class="judul">SERTIFIKAT</div>
    <div class="subjudul">Diberikan Kepada:</div>
    <div class="nama">' . htmlspecialchars($siswa['nama_lengkap']) . '</div>
    <div class="subjudul">Utusan: ' . htmlspecialchars($siswa['utusan_pac_ranting']) . '</div>

    <div class="ucapan">
        Atas partisipasinya mengikuti kegiatan Pelatihan Kepemimpinan Lanjutan<br>
        oleh PAC KAB. Garut sebanyak <strong>' . $totalHadir . ' materi</strong> pada 14-17 Agustus 2025.
    </div>

    <div class="tanggal">
        Diberikan di Komplek Pondok Pesantren Darul Ihsan No.210,<br>
        Kp. Tambakbaya, Ds. Dano, Kec. Leles, Kab. Garut<br>
        pada tanggal ' . $tanggal_cetak . '
    </div>

    <div class="ttd">
        <img src="' . $ttd . '" class="ttd-img">
        <div class="nama-ttd">Ahmad S.KOM, Spd.i, SH</div>
        <div class="jabatan-ttd">Ketua Panitia</div>
    </div>

</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream('sertifikat_' . $siswa_id . '.pdf', ['Attachment' => false]);

ob_end_flush();
exit;
