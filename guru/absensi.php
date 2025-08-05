<?php
session_start();
include '../koneksi.php';

// Cek login guru
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guru') {
    header('Location: ../index.php');
    exit;
}

// Ambil daftar materi untuk dropdown
$materiList = [];
$query = mysqli_query($conn, "SELECT * FROM materi ORDER BY judul ASC");
while ($row = mysqli_fetch_assoc($query)) {
    $materiList[] = $row;
}

// Cek materi terpilih dari GET
$materiTerpilih = null;
if (isset($_GET['materi_id']) && $_GET['materi_id'] !== '') {
    $id = intval($_GET['materi_id']);
    $result = mysqli_query($conn, "SELECT * FROM materi WHERE id = $id");
    $materiTerpilih = mysqli_fetch_assoc($result);
}

?>

<?php include '../layouts/header.php'; ?>
<?php include '../layouts/sidebar.php'; ?>

<!-- Custom CSS -->
<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link href="../css/sb-admin-2.min.css" rel="stylesheet">
<link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <?php include '../layouts/topbar.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Absensi Siswa</h1>
            </div>

            <!-- Pilih Materi -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Pilih Materi</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <div class="form-group">
                                    <label for="materi_id">Materi:</label>
                                    <select name="materi_id" id="materi_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- Pilih Materi --</option>
                                        <?php foreach ($materiList as $materi): ?>
                                            <option value="<?= $materi['id'] ?>" 
                                                <?= (isset($_GET['materi_id']) && $_GET['materi_id'] == $materi['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($materi['judul']) ?> - <?= htmlspecialchars($materi['hari']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($materiTerpilih): ?>
            <!-- Detail Materi -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">Detail Materi</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Judul:</strong> <?= htmlspecialchars($materiTerpilih['judul']) ?></p>
                            <p><strong>Hari:</strong> <?= htmlspecialchars($materiTerpilih['hari']) ?></p>
                            <p><strong>Jam:</strong> <?= htmlspecialchars($materiTerpilih['jam']) ?></p>
                            <p><strong>nama pemateri:</strong> <?= htmlspecialchars($materiTerpilih['nama_guru']) ?></p>

                            <!-- Tombol Scan -->
                            <a href="scan_qr.php?materi_id=<?= $materiTerpilih['id'] ?>" class="btn btn-primary mb-3">
                                <i class="fas fa-qrcode"></i> Scan QR Siswa
                            </a>

                            <!-- Tabel Absensi -->
                            <h6 class="font-weight-bold text-secondary">Daftar Siswa yang Sudah Absen</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Waktu Absen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        date_default_timezone_set('Asia/Jakarta');
                                        $materi_id = $materiTerpilih['id'];

                                        // Ambil data absensi berdasarkan materi_id
                                      $absen = mysqli_query($conn, "
    SELECT a.jam, s.nama_lengkap 
    FROM absen a 
    JOIN siswa s ON a.siswa_id = s.id
    WHERE a.materi_id = $materi_id
    ORDER BY a.jam DESC
");

$no = 1;
while ($row = mysqli_fetch_assoc($absen)):
    $waktuAbsen = '-';
    if (!empty($row['jam']) && $row['jam'] !== '0000-00-00 00:00:00') {
        $datetime = new DateTime($row['jam'], new DateTimeZone('Asia/Jakarta'));
        $waktuAbsen = $datetime->format('d-m-Y H:i:s');
    }
?>
    <tr>
        <td class="text-center"><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
        <td class="text-center"><?= $waktuAbsen ?></td>
    </tr>
<?php endwhile; ?>


                                        <?php if ($no === 1): ?>
                                            <tr><td colspan="3" class="text-center">Belum ada yang absen</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
       
    </div>
    <!-- End Content -->

    <?php include '../layouts/footer.php'; ?>

</div>


<!-- DataTables JS -->
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            lengthMenu: [5, 10, 20, 50],
            pageLength: 5,
            language: {
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(difilter dari total _MAX_ data)"
            }
        });
    });
</script>
