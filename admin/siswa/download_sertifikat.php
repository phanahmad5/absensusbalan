<?php
ob_start();

require '../../vendor/autoload.php';
include '../../koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM siswa WHERE id = $id"));

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'landscape');

// Aset visual
$logo1 = 'http://localhost/absensusbalan/assets/image/logosus.png';
$logo2 = 'http://localhost/absensusbalan/assets/image/ansor.png';
$ornamen_top = 'http://localhost/absensusbalan/assets/image/ornamen.png';
$ornamen_bottom = 'http://localhost/absensusbalan/assets/image/ornamen.png';
$ttd_ketua = 'http://localhost/absensusbalan/assets/image/ttd.png';

$tanggal_kegiatan = date("d F Y");

$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0px; }
        body {
            font-family: "Arial", sans-serif;
            margin: 0;
            padding: 60px;
            position: relative;
            background: #ffffff;
        }

        .ornamen-top {
            position: absolute;
            top: 0;
            right: 0;
            width: 180px;
        }
        .ornamen-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 180px;
        }

        .logo-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo-wrapper img {
            width: 100px;
        }

        .judul {
            font-size: 38px;
            font-weight: bold;
            color: #c09300;
            margin: 20px 0 10px;
            text-align: center;
            text-shadow: 1px 1px 2px #888;
        }

        .subjudul, .ucapan, .tanggal {
            text-align: center;
            color: #444;
        }

        .subjudul {
            font-size: 18px;
            margin-bottom: 5px;
        }

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

    <!-- Ornamen Dekoratif -->
    <img src="' . $ornamen_top . '" class="ornamen-top">
    <img src="' . $ornamen_bottom . '" class="ornamen-bottom">

    <!-- Logo Sejajar -->
    <div class="logo-wrapper">
        <img src="' . $logo1 . '" alt="Logo 1">
        <img src="' . $logo2 . '" alt="Logo 2">
    </div>

    <!-- Judul -->
    <div class="judul">TERIMA KASIH</div>
    <div class="subjudul">Kepada:</div>
    <div class="nama">' . htmlspecialchars($data['nama_lengkap']) . '</div>
    <div class="subjudul">Atas kesediaannya sebagai</div>
    <div class="subjudul" style="font-size: 20px; font-weight: bold; color: #004d40;">PESERTA</div>

    <div class="ucapan">
        Dalam kegiatan Pelatihan Kepemimpinan Lanjutan oleh PAC KAB. Garut<br>
        yang dilaksanakan pada 14-17 Agustus 2025
    </div>

    <div class="tanggal">
        Diberikan di Komplek Pondok Pesantren Darul Ihsan No.210,<br>
        Kp. Tambakbaya, Ds. Dano, Kec. Leles, Kab. Garut<br>
        pada tanggal ' . $tanggal_kegiatan . '
    </div>

    <!-- Tanda Tangan -->
    <!-- Tanda Tangan -->
<div class="ttd">
    <img src="' . $ttd_ketua . '" alt="Tanda Tangan Ketua" class="ttd-img"><br>
    <div class="nama-ttd">Ahmad S.KOM, Spd.i, SH</div>
    <div class="jabatan-ttd">Ketua Panitia</div>
</div>


</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream('sertifikat_' . $data['id'] . '.pdf', ['Attachment' => true]);

ob_end_flush();
exit;
