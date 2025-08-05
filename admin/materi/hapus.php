<?php
include '../../koneksi.php';
session_start();
if ($_SESSION['user']['role'] != 'admin') die("Akses ditolak");

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM materi WHERE id='$id'");
header("Location: index.php");
