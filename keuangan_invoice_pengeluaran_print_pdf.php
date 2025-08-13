<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';
$db = new Database();
$status = isset($_GET['status']) ? $_GET['status'] : '';
if ($status === 'Lunas' || $status === 'Belum Lunas') {
    $db->query("SELECT p.*, u.nama as user_nama FROM pengeluaran p LEFT JOIN user_neraca u ON p.created_by = u.id WHERE p.status_invoice = :status ORDER BY p.tanggal DESC, p.id DESC");
    $db->bind(':status', $status);
    $pengeluaran = $db->resultSet();
    $judul = "Rekapan Invoice Pengeluaran ($status)";
} else {
    $db->query("SELECT p.*, u.nama as user_nama FROM pengeluaran p LEFT JOIN user_neraca u ON p.created_by = u.id ORDER BY p.tanggal DESC, p.id DESC");
    $pengeluaran = $db->resultSet();
    $judul = "Rekapan Invoice Pengeluaran (Semua Status)";
}
$html = '<h2 style="text-align:center">' . $judul . '</h2>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
$html .= '<thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>Jumlah (Rp)</th><th>Status Invoice</th><th>Dicatat Oleh</th></tr></thead><tbody>';
foreach($pengeluaran as $p) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($p['tanggal']) . '</td>';
    $html .= '<td>' . htmlspecialchars($p['kategori']) . '</td>';
    $html .= '<td>' . htmlspecialchars($p['keterangan']) . '</td>';
    $html .= '<td style="text-align:right">Rp ' . number_format($p['jumlah'],0,',','.') . '</td>';
    $html .= '<td>' . htmlspecialchars($p['status_invoice']) . '</td>';
    $html .= '<td>' . htmlspecialchars($p['user_nama']) . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->SetTitle($judul);
$mpdf->Output('Rekapan_Invoice_Pengeluaran.pdf', 'I');
exit;
