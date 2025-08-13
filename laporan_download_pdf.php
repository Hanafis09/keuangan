<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';

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
	$db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE tanggal BETWEEN :awal AND :akhir");
	$db->bind(':awal', $tgl_awal);
	$db->bind(':akhir', $tgl_akhir);
	$total_pemasukan = $db->single()['total'] ?? 0;
	$db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE tanggal BETWEEN :awal AND :akhir");
	$db->bind(':awal', $tgl_awal);
	$db->bind(':akhir', $tgl_akhir);
	$total_pengeluaran = $db->single()['total'] ?? 0;
} else {
	$db->query("SELECT tanggal, kategori, keterangan, jumlah FROM pemasukan ORDER BY tanggal DESC, id DESC");
	$pemasukan = $db->resultSet();
	$db->query("SELECT tanggal, kategori, keterangan, jumlah FROM pengeluaran ORDER BY tanggal DESC, id DESC");
	$pengeluaran = $db->resultSet();
	$db->query("SELECT SUM(jumlah) as total FROM pemasukan");
	$total_pemasukan = $db->single()['total'] ?? 0;
	$db->query("SELECT SUM(jumlah) as total FROM pengeluaran");
	$total_pengeluaran = $db->single()['total'] ?? 0;
}
$saldo = ($total_pemasukan - $total_pengeluaran);

$periode_text = '';
if ($tgl_awal && $tgl_akhir) {
	$periode_text = '<p style="text-align:center">Periode: '.htmlspecialchars($tgl_awal).' s/d '.htmlspecialchars($tgl_akhir).'</p>';
}
// Ambil pengaturan klinik
$db->query("SELECT * FROM pengaturan LIMIT 1");
$pengaturan = $db->single();
$logo_html = '';
if (!empty($pengaturan['logo']) && file_exists($pengaturan['logo'])) {
	$logo_html = '<img src="' . $pengaturan['logo'] . '" style="height:60px;max-width:120px;">';
}
$html = '<div style="text-align:center;margin-bottom:10px">';
$html .= $logo_html;
if (!empty($pengaturan['nama_klinik'])) {
	$html .= '<h2 style="margin:0">' . htmlspecialchars($pengaturan['nama_klinik']) . '</h2>';
}
if (!empty($pengaturan['alamat'])) {
	$html .= '<div style="font-size:13px">' . nl2br(htmlspecialchars($pengaturan['alamat'])) . '</div>';
}
if (!empty($pengaturan['no_telp']) || !empty($pengaturan['email'])) {
	$html .= '<div style="font-size:12px">';
	if (!empty($pengaturan['no_telp'])) $html .= 'Telp: ' . htmlspecialchars($pengaturan['no_telp']) . ' ';
	if (!empty($pengaturan['email'])) $html .= 'Email: ' . htmlspecialchars($pengaturan['email']);
	$html .= '</div>';
}
$html .= '</div>';
$html .= '<h2 style="text-align:center">Laporan Keuangan Klinik</h2>';
$html .= $periode_text;
$html .= '<h4>Pemasukan</h4>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
$html .= '<thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>Jumlah (Rp)</th></tr></thead><tbody>';
foreach($pemasukan as $p) {
	$html .= '<tr>';
	$html .= '<td>' . htmlspecialchars($p['tanggal']) . '</td>';
	$html .= '<td>' . htmlspecialchars($p['kategori']) . '</td>';
	$html .= '<td>' . htmlspecialchars($p['keterangan']) . '</td>';
	$html .= '<td style="text-align:right">Rp ' . number_format($p['jumlah'],0,',','.') . '</td>';
	$html .= '</tr>';
}
$html .= '</tbody></table>';


$html .= '<br><h4>Pengeluaran</h4>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
$html .= '<thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>Jumlah (Rp)</th></tr></thead><tbody>';
foreach($pengeluaran as $p) {
	$html .= '<tr>';
	$html .= '<td>' . htmlspecialchars($p['tanggal']) . '</td>';
	$html .= '<td>' . htmlspecialchars($p['kategori']) . '</td>';
	$html .= '<td>' . htmlspecialchars($p['keterangan']) . '</td>';
	$html .= '<td style="text-align:right">Rp ' . number_format($p['jumlah'],0,',','.') . '</td>';
	$html .= '</tr>';
}
$html .= '</tbody></table>';

$html .= '<br><h4>Ringkasan</h4>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="50%" style="margin-top:10px">';
$html .= '<tr><td><strong>Total Pemasukan</strong></td><td style="text-align:right">Rp ' . number_format($total_pemasukan,0,',','.') . '</td></tr>';
$html .= '<tr><td><strong>Total Pengeluaran</strong></td><td style="text-align:right">Rp ' . number_format($total_pengeluaran,0,',','.') . '</td></tr>';
$html .= '<tr><td><strong>Saldo Akhir</strong></td><td style="text-align:right">Rp ' . number_format($saldo,0,',','.') . '</td></tr>';
$html .= '</table>';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->SetTitle('Laporan Keuangan Klinik');
$mpdf->Output('Laporan_Keuangan_Klinik.pdf', 'D'); // Download file
exit;
