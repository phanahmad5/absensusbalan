<?php
require 'koneksi.php';
$title = "Data Siswa";

// Proses hapus jika ada parameter `hapus`
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // Cek dulu apakah data siswa ada
    $cek = $conn->query("SELECT * FROM siswa WHERE id = $id");
    if ($cek->num_rows > 0) {
        // Hapus file QR jika ada
        $qrPath = "assets/qrcodes/qr_" . $id . ".png";
        if (file_exists($qrPath)) unlink($qrPath);

        // Hapus data dari tabel siswa
        $conn->query("DELETE FROM siswa WHERE id = $id");
        echo "<script>alert('✅ Data siswa berhasil dihapus'); window.location='siswa.php';</script>";
        exit;
    }
}

ob_start();
?>

<h2>👨‍🎓 Daftar Siswa</h2>

<a href="generate_qr.php" style="padding: 10px 15px; background: #2196F3; color: white; text-decoration: none; border-radius: 5px;">+ Tambah Siswa & QR</a>

<br><br>

<table id="siswaTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>QR Code</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $data = $conn->query("SELECT * FROM siswa");
        $no = 1;
        while ($d = $data->fetch_assoc()) {
            echo "<tr>
                <td>$no</td>
                <td>{$d['nama']}</td>
                <td><img src='assets/qrcodes/qr_{$d['id']}.png' width='80'></td>
                <td>
                    <a href='siswa_detail.php?id={$d['id']}' style='margin-right: 10px;'>🔍 Detail</a>
                    <a href='?hapus={$d['id']}' onclick='return confirm(\"Yakin ingin menghapus?\")' style='color: red;'>🗑️ Hapus</a>
                </td>
            </tr>";
            $no++;
        }
        ?>
    </tbody>
</table>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#siswaTable').DataTable();
    });
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
