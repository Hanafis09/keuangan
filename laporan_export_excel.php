<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$db = new Database();

// Ambil filter periode dari GET
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';

// Query pemasukan sesuai periode
if ($tgl_awal && $tgl_akhir) {
	$db->query("SELECT tanggal, kategori, keterangan, jumlah FROM pemasukan WHERE tanggal BETWEEN :awal AND :akhir ORDER BY tanggal DESC, id DESC");
	$db->bind(':awal', $tgl_awal);
	$db->bind(':akhir', $tgl_akhir);
	$pemasukan = $db->resultSet();
	$db->query("SELECT tanggal, kategori, keterangan, jumlah FROM pengeluaran WHERE tanggal BETWEEN :awal AND :akhir ORDER BY tanggal DESC, id DESC");
	$db->bind(':awal', $tgl_awal);
	$db->bind(':akhir', $tgl_akhir);
	$pengeluaran = $db->resultSet();
} else {
	$db->query("SELECT tanggal, kategori, keterangan, jumlah FROM pemasukan ORDER BY tanggal DESC, id DESC");
	$pemasukan = $db->resultSet();
	$db->query("SELECT tanggal, kategori, keterangan, jumlah FROM pengeluaran ORDER BY tanggal DESC, id DESC");
	$pengeluaran = $db->resultSet();
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Keuangan Klinik');

$row = 1;
$sheet->setCellValue('A'.$row, 'Laporan Keuangan Klinik');
$row++;
if ($tgl_awal && $tgl_akhir) {
	$sheet->setCellValue('A'.$row, 'Periode: '.$tgl_awal.' s/d '.$tgl_akhir);
	$row++;
}

$row++;
$sheet->setCellValue('A'.$row, 'PEMASUKAN');
$row++;
$sheet->setCellValue('A'.$row, 'Tanggal');
$sheet->setCellValue('B'.$row, 'Kategori');
$sheet->setCellValue('C'.$row, 'Keterangan');
$sheet->setCellValue('D'.$row, 'Jumlah (Rp)');
$row++;
foreach($pemasukan as $p) {
	$sheet->setCellValue('A'.$row, $p['tanggal']);
	$sheet->setCellValue('B'.$row, $p['kategori']);
	$sheet->setCellValue('C'.$row, $p['keterangan']);
	$sheet->setCellValue('D'.$row, $p['jumlah']);
	$row++;
}

$row++;
$sheet->setCellValue('A'.$row, 'PENGELUARAN');
$row++;
$sheet->setCellValue('A'.$row, 'Tanggal');
$sheet->setCellValue('B'.$row, 'Kategori');
$sheet->setCellValue('C'.$row, 'Keterangan');
$sheet->setCellValue('D'.$row, 'Jumlah (Rp)');
$row++;
foreach($pengeluaran as $p) {
	$sheet->setCellValue('A'.$row, $p['tanggal']);
	$sheet->setCellValue('B'.$row, $p['kategori']);
	$sheet->setCellValue('C'.$row, $p['keterangan']);
	$sheet->setCellValue('D'.$row, $p['jumlah']);
	$row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Keuangan_Klinik.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
