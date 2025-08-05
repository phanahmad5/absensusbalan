<?php
include '../koneksi.php';

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$materi_id = isset($_GET['materi_id']) ? $_GET['materi_id'] : '';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=absensi_$tanggal.xls");

echo "<h3>Daftar Kehadiran Tanggal: $tanggal</h3>";
echo "<table border='1' cellspacing='0' cellpadding='5'>";
echo "<tr>
        <th>No</th>
        <th>Nama</th>
        <th>Utusan</th>
        <th>Materi</th>
        <th>Jam Absen</th>
      </tr>";

$query = "
    SELECT siswa.nama, siswa.utusan, absen.jam, materi.judul
    FROM absen 
    JOIN siswa ON absen.siswa_id = siswa.id 
    JOIN materi ON absen.materi_id = materi.id
    WHERE absen.tanggal = '$tanggal'
";

if ($materi_id != '') {
    $query .= " AND absen.materi_id = '$materi_id' ";
}

$query .= " ORDER BY absen.jam ASC";

$result = mysqli_query($conn, $query);
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>$no</td>
            <td>{$row['nama']}</td>
            <td>{$row['utusan']}</td>
            <td>{$row['judul']}</td>
            <td>{$row['jam']}</td>
          </tr>";
    $no++;
}

if ($no === 1) {
    echo "<tr><td colspan='5'>Belum ada yang absen</td></tr>";
}

echo "</table>";
?>
