<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    header("Location: /absensusbalan/index.php");
    exit;
}

include '../koneksi.php';
?>

<?php include '../layouts/header.php'; ?>
<?php include '../layouts/sidebar.php'; ?>


<!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- SB Admin 2 CSS -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    

    <!-- Main Content -->
    <div id="content">

        <?php include '../layouts/topbar.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard Guru</h1>
            </div>

               
            <!-- Info Card -->
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Selamat Datang Guru!</h6>
                        </div>
                        <div class="card-body">
                            <p>Gunakan menu navigasi di sebelah kiri untuk melakukan 
                                <strong>Absen QR</strong>, melihat <strong>Data Absensi</strong>, dan mengelola <strong>Materi Anda</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <?php include '../layouts/footer.php'; ?>

</div>
<!-- End of Content Wrapper -->
