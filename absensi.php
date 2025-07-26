<?php
require 'koneksi.php';
$title = "Riwayat Absensi";
ob_start();

$result = $conn->query("SELECT absensi.*, siswa.nama FROM absensi 
                        JOIN siswa ON absensi.siswa_id = siswa.id 
                        ORDER BY absensi.waktu_absen DESC");
?>

<h2>📋 Riwayat Absensi</h2>

<a href="export_excel.php" style="padding:10px 15px; background:green; color:white; text-decoration:none; border-radius:5px;">
    ⬇️ Download Excel
</a>
<br><br>

<table id="absensiTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Waktu</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= $row['waktu_absen'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#absensiTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
