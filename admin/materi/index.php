<?php
include '../../koneksi.php';
session_start();
if ($_SESSION['user']['role'] != 'admin') die("Akses ditolak");
?>

<?php include '../../layouts/header.php'; ?>
<?php include '../../layouts/sidebar.php'; ?>

<!-- Main Content -->
<div id="content">

    <?php include '../../layouts/topbar.php'; ?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Data Materi</h1>

        <a href="tambah.php" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Tambah Materi
        </a>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Guru</th>
                                <th>Judul</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $q = mysqli_query($conn, "SELECT * FROM materi");
                            while ($d = mysqli_fetch_assoc($q)):
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($d['nama_guru']) ?></td>
                                <td><?= htmlspecialchars($d['judul']) ?></td>
                                <td><?= htmlspecialchars($d['hari']) ?></td>
                                <td><?= htmlspecialchars($d['jam']) ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">
                                        <i class="fas fa-trash"></i> Hapus
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
