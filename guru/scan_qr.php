<?php
session_start();
include '../koneksi.php';

// Cek login guru
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header('Location: ../index.php');
    exit;
}

// Pastikan materi_id tersedia
if (!isset($_GET['materi_id']) || $_GET['materi_id'] === '') {
    header('Location: scan.php');
    exit;
}

$materi_id = intval($_GET['materi_id']);
$materi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM materi WHERE id = $materi_id"));
if (!$materi) {
    die("Materi tidak ditemukan!");
}
?>

<?php include '../layouts/header.php'; ?>
<?php include '../layouts/sidebar.php'; ?>

<!-- Custom CSS -->
<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="../css/sb-admin-2.min.css" rel="stylesheet">
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include '../layouts/topbar.php'; ?>

        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Scan QR Siswa - <?= htmlspecialchars($materi['judul']) ?></h1>
                <a href="scan.php?materi_id=<?= $materi_id ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail Materi
                </a>
            </div>

            <!-- Scanner -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Scan QR Code Siswa</h6>
                </div>
                <div class="card-body text-center">
                    <div id="reader" style="width:320px; margin:auto;"></div>
                    <div id="result" class="mt-3 text-lg"></div>
                </div>
            </div>

            <!-- Tabel Absensi -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Daftar Siswa yang Sudah Absen</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y:auto;">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Waktu Absen</th>
                                </tr>
                            </thead>
                            <tbody id="absensi-body">
                                <?php
                                date_default_timezone_set('Asia/Jakarta');
                                $absen = mysqli_query($conn, "
                                    SELECT a.jam, s.nama_lengkap
                                    FROM absen a 
                                    JOIN siswa s ON a.siswa_id = s.id
                                    WHERE a.materi_id = $materi_id
                                    ORDER BY a.jam DESC
                                ");
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($absen)) {
                                    $waktuAbsen = ($row['jam'] && $row['jam'] !== '0000-00-00 00:00:00') 
                                        ? (new DateTime($row['jam'], new DateTimeZone('Asia/Jakarta')))->format('d-m-Y H:i:s') 
                                        : '-';
                                    echo "<tr>
                                            <td class='text-center'>{$no}</td>
                                            <td>" . htmlspecialchars($row['nama_lengkap']) . "</td>
                                            <td class='text-center'>{$waktuAbsen}</td>
                                          </tr>";
                                    $no++;
                                }

                                if ($no === 1) {
                                    echo "<tr><td colspan='3' class='text-center'>Belum ada yang absen</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../layouts/footer.php'; ?>
</div>

<!-- Script Scanner -->
<script>
    const resultDiv = document.getElementById('result');
    const absensiBody = document.getElementById('absensi-body');
    const html5QrcodeScanner = new Html5Qrcode("reader");

    function startScanner() {
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            {
                fps: 15,
                qrbox: 250,
            },
            onScanSuccess,
            error => console.warn("Gagal baca:", error)
        ).catch(err => {
            console.warn("Fallback ke kamera pertama:", err);
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras.length) {
                    html5QrcodeScanner.start(
                        cameras[0].id,
                        { fps: 15, qrbox: 250 },
                        onScanSuccess,
                        error => console.warn("Gagal fallback:", error)
                    );
                } else {
                    resultDiv.innerHTML = "<p class='text-danger'>Tidak ada kamera ditemukan.</p>";
                }
            });
        });
    }

    function onScanSuccess(decodedText) {
        console.log("QR berhasil:", decodedText);
        html5QrcodeScanner.stop().then(() => {
            resultDiv.innerHTML = "<p class='text-info'>Memproses absensi...</p>";

            fetch('simpan_absen.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'qr=' + encodeURIComponent(decodedText) + '&materi_id=<?= $materi_id ?>'
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    resultDiv.innerHTML = `<p class='text-success'>${response.message}</p>`;
                } else if (response.status === 'info') {
                    resultDiv.innerHTML = `<p class='text-warning'>${response.message}</p>`;
                } else {
                    resultDiv.innerHTML = `<p class='text-danger'>${response.message}</p>`;
                }

                // Refresh tabel
                fetch('tabel_absen.php?materi_id=<?= $materi_id ?>')
                    .then(res => res.text())
                    .then(html => {
                        absensiBody.innerHTML = html;
                        setTimeout(() => {
                            resultDiv.innerHTML = "";
                            startScanner();
                        }, 1500);
                    });
            })
            .catch(err => {
                console.error('Error:', err);
                resultDiv.innerHTML = "<p class='text-danger'>Terjadi kesalahan saat absen.</p>";
                setTimeout(() => {
                    resultDiv.innerHTML = "";
                    startScanner();
                }, 1500);
            });
        });
    }

    // Start scanner when ready
    window.addEventListener('load', startScanner);
</script>
