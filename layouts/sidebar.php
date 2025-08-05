<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Validasi apakah 'user' sudah ada dan dalam bentuk array
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$user = $_SESSION['user'];
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        
        <div class="sidebar-brand-text mx-3">Apliaksi absensi</div>
    </a>

    <hr class="sidebar-divider my-0">

    <hr class="sidebar-divider"> 

    <div class="sidebar-heading">
        Menu Navigasi
    </div>

    <?php if ($user['role'] == 'admin'): ?>

        <li class="nav-item">
            <a class="nav-link" href="/absensusbalan/admin/dashboard.php">
                <i class="fas fa-users"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/absensusbalan/admin/siswa/index.php">
                <i class="fas fa-users"></i>
                <span>Manajemen Siswa</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/absensusbalan/admin/materi/index.php">
                <i class="fas fa-book"></i>
                <span>Manajemen Materi</span>
            </a>
        </li>
    <?php else: ?>
        <li class="nav-item active">
    <a class="nav-link" href="dashboard.php">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
</li>

        <li class="nav-item">
            <a class="nav-link" href="/absensusbalan/guru/absensi.php">
                <i class="fas fa-qrcode"></i>
                <span>Absen QR</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/absensusbalan/guru/data_absen.php">
                <i class="fas fa-list"></i>
                <span>Data Absensi</span>
            </a>
        </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item">
        <a class="nav-link" href="/absensusbalan/logout.php" onclick="return confirm('Yakin ingin logout?')">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </li>
</ul>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
