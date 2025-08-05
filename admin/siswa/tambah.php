<?php
include '../../koneksi.php';
require '../../vendor/autoload.php'; // Endroid QR Code

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (isset($_POST['simpan'])) {
    // Ambil input
    $nama_lengkap = $_POST['nama_lengkap'];
    $nik = $_POST['nik'];
    $no_kta = $_POST['no_kta'];
    $ttl = $_POST['ttl'];
    $alamat_lengkap = $_POST['alamat_lengkap'];
    $tempat_pkd = $_POST['tempat_pkd'];
    $no_hp = $_POST['no_hp'];
    $utusan = $_POST['utusan_pac_ranting'];

    // Upload file bukti transfer
    $bukti_transfer = '';
    if ($_FILES['bukti_transfer']['error'] === 0) {
        $bukti_transfer = time() . '_bt_' . $_FILES['bukti_transfer']['name'];
        move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], '../../uploads/bukti_transfer/' . $bukti_transfer);
    }

    // Upload surat bukti
    $surat_bukti = '';
    if ($_FILES['surat_bukti']['error'] === 0) {
        $surat_bukti = time() . '_sb_' . $_FILES['surat_bukti']['name'];
        move_uploaded_file($_FILES['surat_bukti']['tmp_name'], '../../uploads/surat_bukti/' . $surat_bukti);
    }

    // QR content hanya nama_lengkap dan utusan_pac_ranting
    $qrContent = "$nama_lengkap|$utusan";

    // Simpan ke database
    $sql = "INSERT INTO siswa 
        (nama_lengkap, nik, no_kta, ttl, alamat_lengkap, tempat_pkd, no_hp, utusan_pac_ranting, bukti_transfer, surat_bukti, qr_code) 
        VALUES 
        ('$nama_lengkap', '$nik', '$no_kta', '$ttl', '$alamat_lengkap', '$tempat_pkd', '$no_hp', '$utusan', '$bukti_transfer', '$surat_bukti', '$qrContent')";
    
    mysqli_query($conn, $sql);
    $id = mysqli_insert_id($conn);

    // Generate QR
    $qr = new QrCode($qrContent);
    $writer = new PngWriter();
    $result = $writer->write($qr);
    file_put_contents("../../assets/qr/{$id}.png", $result->getString());

    header("Location: index.php");
    exit;
}

?>

<?php include '../../layouts/header.php'; ?>
<?php include '../../layouts/sidebar.php'; ?>

<div id="content">
    <?php include '../../layouts/topbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Tambah Siswa</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>No. KTA</label>
                        <input type="text" name="no_kta" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tempat, Tanggal Lahir</label>
                        <input type="text" name="ttl" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Tempat PKD/Diklatsar</label>
                        <input type="text" name="tempat_pkd" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Utusan PAC dan Ranting</label>
                        <input type="text" name="utusan_pac_ranting" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Bukti Transfer (Opsional)</label>
                        <input type="file" name="bukti_transfer" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label>Upload Surat Bukti (Opsional)</label>
                        <input type="file" name="surat_bukti" class="form-control-file">
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

<?php include '../../layouts/footer.php'; ?>
