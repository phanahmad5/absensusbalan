<?php
require 'vendor/autoload.php';
require 'koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data absensi
$result = $conn->query("SELECT absensi.id, siswa.nama, absensi.waktu 
                        FROM absensi 
                        JOIN siswa ON absensi.siswa_id = siswa.id 
                        ORDER BY absensi.waktu DESC");

// Buat objek spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Nama Siswa');
$sheet->setCellValue('C1', 'Waktu Absen');

// Isi data
$no = 1;
$row = 2;
while ($data = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $data['nama']);
    $sheet->setCellValue('C' . $row, $data['waktu']);
    $row++;
}

// Set judul sheet
$sheet->setTitle('Rekap Absensi');

// Output sebagai file .xlsx
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="rekap_absensi.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
