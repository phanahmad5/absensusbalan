<?php
include '../koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$materi_id = intval($_GET['materi_id']);
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
