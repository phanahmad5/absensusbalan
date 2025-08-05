<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: /absensusbalan/index.php");
    exit;
}

include '../koneksi.php';
?>

<?php include '../layouts/header.php'; ?>
<?php include '../layouts/sidebar.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">

<!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- SB Admin 2 CSS -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <div id="content">

        <?php include '../layouts/topbar.php'; ?>

        
        <div class="container-fluid">

            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
            </div>
           
            <div class="row">
                <!-- Card Total Siswa -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Siswa</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        $qSiswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
                                        $dataSiswa = mysqli_fetch_assoc($qSiswa);
                                        echo $dataSiswa['total'];
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Materi -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Materi</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        $qMateri = mysqli_query($conn, "SELECT COUNT(*) as total FROM materi");
                                        $dataMateri = mysqli_fetch_assoc($qMateri);
                                        echo $dataMateri['total'];
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-book fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Total Guru -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Guru</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        $qGuru = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='guru'");
                                        $dataGuru = mysqli_fetch_assoc($qGuru);
                                        echo $dataGuru['total'];
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Selamat Datang Admin!</h6>
                        </div>
                        <div class="card-body">
                            <p>Gunakan menu navigasi di sebelah kiri untuk mengelola data 
                                <strong>Siswa</strong>, <strong>Materi</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
      

    </div>
 

    <?php include '../layouts/footer.php'; ?>

</div>

