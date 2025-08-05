<?php
include '../../koneksi.php';
session_start();
if ($_SESSION['user']['role'] != 'admin') die("Akses ditolak");

$id = $_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM materi WHERE id='$id'");
$d = mysqli_fetch_assoc($q);

if (isset($_POST['update'])) {
    $nama_guru = $_POST['nama_guru'];
    $judul = $_POST['judul'];
    $hari = $_POST['hari'];
    $jam = $_POST['jam'];

    mysqli_query($conn, "UPDATE materi SET 
        nama_guru='$nama_guru', 
        judul='$judul', 
        hari='$hari', 
        jam='$jam' 
        WHERE id='$id'");
    header("Location: index.php");
    exit;
}
?>

<?php include '../../layouts/header.php'; ?>
<?php include '../../layouts/sidebar.php'; ?>

<!-- Main Content -->
<div id="content">

    <?php include '../../layouts/topbar.php'; ?>

    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Edit Materi</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="nama_guru">Nama Guru</label>
                        <input type="text" name="nama_guru" id="nama_guru" class="form-control" 
                               value="<?= htmlspecialchars($d['nama_guru']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="judul">Judul Materi</label>
                        <input type="text" name="judul" id="judul" class="form-control" 
                               value="<?= htmlspecialchars($d['judul']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="hari">Hari</label>
                        <select name="hari" id="hari" class="form-control" required>
                            <?php
                            $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                            foreach ($hariList as $h):
                            ?>
                                <option value="<?= $h ?>" <?= $d['hari'] == $h ? 'selected' : '' ?>><?= $h ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jam">Jam</label>
                        <input type="time" name="jam" id="jam" class="form-control" 
                               value="<?= htmlspecialchars($d['jam']) ?>" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End of Main Content -->

<?php include '../../layouts/footer.php'; ?>
