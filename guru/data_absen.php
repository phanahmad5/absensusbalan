<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    header("Location: /absensusbalan/index.php");
    exit;
}

include '../koneksi.php';
date_default_timezone_set('Asia/Jakarta');

// Ambil filter
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$materi_id = isset($_GET['materi_id']) ? $_GET['materi_id'] : '';

// Ambil list materi untuk filter
$listMateri = [];
$qMateri = mysqli_query($conn, "SELECT * FROM materi ORDER BY judul ASC");
while ($row = mysqli_fetch_assoc($qMateri)) {
    $listMateri[] = $row;
}

// Query ambil data absensi
$query = "
    SELECT 
        s.id AS siswa_id,
        s.nama_lengkap AS nama_siswa, 
        s.utusan_pac_ranting AS utusan, 
        m.judul, 
        m.nama_guru, 
        MAX(a.jam) AS jam_terakhir
    FROM absen a
    JOIN siswa s ON a.siswa_id = s.id
    JOIN materi m ON a.materi_id = m.id
";

// Filter dinamis
$where = "WHERE 1=1";
if (!empty($tanggal)) {
    $where .= " AND DATE(a.jam) = '$tanggal'";
}
if (!empty($materi_id)) {
    $where .= " AND a.materi_id = '$materi_id'";
}

// Gabungkan
$query .= "$where GROUP BY s.id, m.id ORDER BY jam_terakhir ASC";
$result = mysqli_query($conn, $query);
?>

<?php include '../layouts/header.php'; ?>
<?php include '../layouts/sidebar.php'; ?>

<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link href="../css/sb-admin-2.min.css" rel="stylesheet">
<link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" />

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include '../layouts/topbar.php'; ?>

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Daftar Kehadiran (<?= htmlspecialchars($tanggal) ?>)</h1>
            </div>

            <form method="GET" class="mb-4 d-flex align-items-center flex-wrap">
                <label class="mr-2 font-weight-bold">Tanggal:</label>
                <input type="date" name="tanggal" class="form-control w-auto mr-3" value="<?= htmlspecialchars($tanggal) ?>" required>

                <label class="mr-2 font-weight-bold">Materi:</label>
                <select name="materi_id" class="form-control w-auto mr-3">
                    <option value="">Semua Materi</option>
                    <?php foreach ($listMateri as $materi): ?>
                        <option value="<?= $materi['id'] ?>" <?= ($materi_id == $materi['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($materi['judul']) ?> (<?= htmlspecialchars($materi['hari']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>

            <!-- Tabel Absensi -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTableAbsensi" width="100%" cellspacing="0">
                            <thead class="bg-primary text-white text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Utusan</th>
                                    <th>Materi</th>
                                    <th>Nama Pemateri</th>
                                    <th>Jam Absen</th>
                                    <th>Jumlah hadir</th>
                                    <th>Sertifikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;

                                if (!$result) {
                                    echo "<tr><td colspan='8' class='text-danger text-center'>Query Error: " . mysqli_error($conn) . "</td></tr>";
                                } else {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $jam_terakhir = '-';
                                        if (!empty($row['jam_terakhir']) && $row['jam_terakhir'] !== '0000-00-00 00:00:00') {
                                            $datetime = new DateTime($row['jam_terakhir'], new DateTimeZone('Asia/Jakarta'));
                                            $jam_terakhir = $datetime->format('d-m-Y H:i:s');
                                        }

                                        $siswa_id = $row['siswa_id'];

                                        // Hitung jumlah kehadiran unik (materi yang diikuti)
                                        $qHadir = mysqli_query($conn, "
                                            SELECT COUNT(DISTINCT materi_id) AS total 
                                            FROM absen 
                                            WHERE siswa_id = '$siswa_id'
                                        ");
                                        $rHadir = mysqli_fetch_assoc($qHadir);
                                        $totalHadir = $rHadir ? $rHadir['total'] : 0;

                                        // Tampilkan tombol cetak jika memenuhi ≥ 4 materi
                                        $tombol = ($totalHadir >= 4)
                                            ? "<a href='cetak_sertifikat.php?siswa_id=$siswa_id' class='btn btn-success btn-sm' target='_blank'>
                                                    <i class='fas fa-certificate'></i> Cetak
                                               </a>"
                                            : "<span class='text-danger'>Belum Lulus</span>";

                                            

                                        echo "<tr>
                                                <td>$no</td>
                                                <td>" . htmlspecialchars($row['nama_siswa']) . "</td>
                                                <td>" . htmlspecialchars($row['utusan']) . "</td>
                                                <td>" . htmlspecialchars($row['judul']) . "</td>
                                                <td>" . htmlspecialchars($row['nama_guru']) . "</td>
                                                <td class='text-center'>$jam_terakhir</td>
                                                <td class='text-center'>$totalHadir</td>
                                                <td class='text-center'>$tombol</td>
                                              </tr>";
                                        $no++;
                                    }

                                    if ($no === 1) {
                                        echo "<tr><td colspan='8' class='text-center'>Belum ada data absen</td></tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php include '../layouts/footer.php'; ?>
</div>

<!-- JS dan DataTables -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTableAbsensi').DataTable({
        lengthMenu: [5, 10, 20, 50, 100],
        pageLength: 5,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Export Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-info btn-sm'
            }
        ],
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari total _MAX_ data)"
        }
    });
});
</script>
