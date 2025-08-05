<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak");
}
?>

<?php include '../../layouts/header.php'; ?>
<?php include '../../layouts/sidebar.php'; ?>

<!-- Main Content -->           
<div id="content">

    <?php include '../../layouts/topbar.php'; ?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Data Siswa</h1>

        <!-- Tombol Aksi -->
        <a href="tambah.php" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Tambah Siswa
        </a>
        <a href="download_semua_qr.php" class="btn btn-success mb-3">
            <i class="fas fa-qrcode"></i> Unduh Semua QR
        </a>
        <a href="input_excel.php" class="btn btn-warning mb-3">
            <i class="fas fa-file-excel"></i> Input Data Excel
        </a>

        <!-- Card Data Siswa -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">Tabel Data Siswa</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
    <tr>
        <th>No</th>
        <th>Nama Lengkap</th>
        <th>NIK</th>
        <th>No. KTA</th>
        <th>TTL</th>
        <th>Alamat</th>
        <th>Tempat PKD</th>
        <th>No HP</th>
        <th>Utusan</th>
        <th>Bukti Transfer</th>
        <th>Surat Bukti</th>
        <th>QR</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
<?php
$no = 1;
$q = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");
while ($d = mysqli_fetch_assoc($q)):
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($d['nama_lengkap']) ?></td>
    <td><?= htmlspecialchars($d['nik']) ?></td>
    <td><?= htmlspecialchars($d['no_kta']) ?></td>
    <td><?= htmlspecialchars($d['ttl']) ?></td>
    <td><?= nl2br(htmlspecialchars($d['alamat_lengkap'])) ?></td>
    <td><?= htmlspecialchars($d['tempat_pkd']) ?></td>
    <td><?= htmlspecialchars($d['no_hp']) ?></td>
    <td><?= htmlspecialchars($d['utusan_pac_ranting']) ?></td>
    <td>
        <?php if (!empty($d['bukti_transfer'])): ?>
            <a href="../../upload/bukti_transfer/<?= $d['bukti_transfer'] ?>" target="_blank" class="btn btn-sm btn-outline-info">Lihat</a>
        <?php else: ?>
            <span class="text-muted">Kosong</span>
        <?php endif; ?>
    </td>
    <td>
        <?php if (!empty($d['surat_bukti'])): ?>
            <a href="../../uploads/surat_bukti/<?= $d['surat_bukti'] ?>" target="_blank" class="btn btn-sm btn-outline-info">Lihat</a>
        <?php else: ?>
            <span class="text-muted">Kosong</span>
        <?php endif; ?>
    </td>
    <td><img src="../../assets/qr/<?= $d['id'] ?>.png" width="60"></td>
    <td>
        <a href="edit.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning mb-1">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="hapus.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Yakin ingin menghapus data ini?')">
            <i class="fas fa-trash"></i> Hapus
        </a>
        <a href="download_sertifikat.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-info mb-1" target="_blank">
            <i class="fas fa-download"></i> Sertifikat
        </a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php include '../../layouts/footer.php'; ?>

<script>
$(document).ready(function() {
  if (! $.fn.DataTable.isDataTable('#dataTable')) {
    $('#dataTable').DataTable({
      "pageLength": 300,
      "lengthMenu": [ [10, 25, 50, 100, 300, -1], [10, 25, 50, 100, 300, "All"] ],
      "order": [[0, "desc"]],
      "columnDefs": [
          { "orderable": false, "targets": [9,10,11,12] }
      ]
    });
  }
});
</script>


