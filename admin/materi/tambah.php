<?php
include '../../koneksi.php';
session_start();
if ($_SESSION['user']['role'] != 'admin') die("Akses ditolak");

if (isset($_POST['simpan'])) {
    $nama_guru = $_POST['nama_guru'];
    $judul = $_POST['judul'];
    $hari = $_POST['hari'];
    $jam = $_POST['jam'];

    $stmt = $conn->prepare("INSERT INTO materi (nama_guru, judul, hari, jam) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_guru, $judul, $hari, $jam);
    $stmt->execute();
    $stmt->close();

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
        <h1 class="h3 mb-4 text-gray-800">Tambah Materi</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="nama_guru">Nama Pemateri</label>
                        <input type="text" name="nama_guru" id="nama_guru" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="judul">Judul Materi</label>
                        <input type="text" name="judul" id="judul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="hari">Hari</label>
                        <select name="hari" id="hari" class="form-control" required>
                            <option>Senin</option>
                            <option>Selasa</option>
                            <option>Rabu</option>
                            <option>Kamis</option>
                            <option>Jumat</option>
                            <option>Sabtu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jam">Jam</label>
                        <input type="time" name="jam" id="jam" class="form-control" required>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
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
