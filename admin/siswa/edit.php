<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$q = mysqli_query($conn, "SELECT * FROM siswa WHERE id='$id'");
$d = mysqli_fetch_assoc($q);

if (!$d) {
    echo "<div class='alert alert-danger'>Data siswa tidak ditemukan.</div>";
    exit;
}

if (isset($_POST['update'])) {
    // Escape semua inputan
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $no_kta = mysqli_real_escape_string($conn, $_POST['no_kta']);
    $ttl = mysqli_real_escape_string($conn, $_POST['ttl']);
    $alamat_lengkap = mysqli_real_escape_string($conn, $_POST['alamat_lengkap']);
    $tempat_pkd = mysqli_real_escape_string($conn, $_POST['tempat_pkd']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $utusan_pac_ranting = mysqli_real_escape_string($conn, $_POST['utusan_pac_ranting']);
    $pelatihan = mysqli_real_escape_string($conn, $_POST['pelatihan']);

    $update = mysqli_query($conn, "UPDATE siswa SET
        nama_lengkap='$nama_lengkap',
        nik='$nik',
        no_kta='$no_kta',
        ttl='$ttl',
        alamat_lengkap='$alamat_lengkap',
        tempat_pkd='$tempat_pkd',
        no_hp='$no_hp',
        utusan_pac_ranting='$utusan_pac_ranting',
        pelatihan='$pelatihan'
        WHERE id='$id'");

    if ($update) {
        $_SESSION['success'] = "Data siswa berhasil diupdate.";
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal update data: " . mysqli_error($conn);
    }
}
?>

<?php include '../../layouts/header.php'; ?>
<?php include '../../layouts/sidebar.php'; ?>

<div id="content">
    <?php include '../../layouts/topbar.php'; ?>

    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Edit Data Siswa</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="POST" class="user">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="<?= htmlspecialchars($d['nama_lengkap']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nik">NIK</label>
                        <input type="text" name="nik" id="nik" class="form-control" value="<?= htmlspecialchars($d['nik']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="no_kta">No. KTA</label>
                        <input type="text" name="no_kta" id="no_kta" class="form-control" value="<?= htmlspecialchars($d['no_kta']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="ttl">Tempat, Tanggal Lahir</label>
                        <input type="text" name="ttl" id="ttl" class="form-control" value="<?= htmlspecialchars($d['ttl']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="alamat_lengkap">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" rows="3"><?= htmlspecialchars($d['alamat_lengkap']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tempat_pkd">Tempat PKD/Diklatsar</label>
                        <input type="text" name="tempat_pkd" id="tempat_pkd" class="form-control" value="<?= htmlspecialchars($d['tempat_pkd']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="no_hp">No HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" value="<?= htmlspecialchars($d['no_hp']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="utusan_pac_ranting">Utusan PAC/Ranting</label>
                        <input type="text" name="utusan_pac_ranting" id="utusan_pac_ranting" class="form-control" value="<?= htmlspecialchars($d['utusan_pac_ranting']) ?>">
                    </div>


                    <button type="submit" name="update" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Update Data
                    </button>
                    <a href="index.php" class="btn btn-secondary btn-block mt-2">Batal</a>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
