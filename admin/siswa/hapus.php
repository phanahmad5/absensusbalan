<?php
include '../../koneksi.php';
$id = $_GET['id'];
unlink("../../assets/qr/{$id}.png"); // hapus file QR
mysqli_query($conn, "DELETE FROM siswa WHERE id='$id'");
header("Location: index.php");
