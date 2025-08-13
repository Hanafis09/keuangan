<?php
session_start();
if (!isset($_SESSION['neraca_user'])) {
    header('Location: neraca_login.php');
    exit;
}
require_once 'config.php';
$db = new Database();

// Filter
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

// Total pemasukan
$db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE tanggal BETWEEN :awal AND :akhir");
$db->bind(':awal', $tgl_awal);
$db->bind(':akhir', $tgl_akhir);
$total_pemasukan = $db->single()['total'] ?? 0;

// Total pengeluaran
$db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE tanggal BETWEEN :awal AND :akhir");
$db->bind(':awal', $tgl_awal);
$db->bind(':akhir', $tgl_akhir);
$total_pengeluaran = $db->single()['total'] ?? 0;

// Saldo akhir
$saldo = $total_pemasukan - $total_pengeluaran;

// Data pemasukan & pengeluaran
$db->query("SELECT * FROM pemasukan WHERE tanggal BETWEEN :awal AND :akhir ORDER BY tanggal DESC, id DESC");
$db->bind(':awal', $tgl_awal);
$db->bind(':akhir', $tgl_akhir);
$pemasukan = $db->resultSet();

$db->query("SELECT * FROM pengeluaran WHERE tanggal BETWEEN :awal AND :akhir ORDER BY tanggal DESC, id DESC");
$db->bind(':awal', $tgl_awal);
$db->bind(':akhir', $tgl_akhir);
$pengeluaran = $db->resultSet();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Klinik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .nav-link { color: rgba(255,255,255,0.8) !important; padding: 10px 20px !important; margin: 5px 0 !important; border-radius: 5px; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255,255,255,0.1); color: white !important; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border: 1px solid rgba(0,0,0,.125); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar p-3">
                <h4 class="text-white text-center mb-4"><i class="fas fa-balance-scale"></i> Keuangan Klinik</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="keuangan_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="keuangan_pemasukan.php"><i class="fas fa-arrow-down me-2"></i> Pemasukan</a></li>
                    <li class="nav-item"><a class="nav-link" href="keuangan_pengeluaran.php"><i class="fas fa-arrow-up me-2"></i> Pengeluaran</a></li>
                    <li class="nav-item"><a class="nav-link active" href="keuangan_laporan.php"><i class="fas fa-file-alt me-2"></i> Laporan Keuangan</a></li>
                    <li class="nav-item mt-3"><hr></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="neraca_logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                <h2>Laporan Keuangan Klinik</h2>
                <p class="text-muted">Laporan kas, neraca sederhana, dan laba rugi berdasarkan periode.</p>
                    <div class="mb-3">
                        <a id="btnPrintPDF" href="#" target="_blank" class="btn btn-primary"><i class="fas fa-print me-2"></i>Print PDF</a>
                        <a id="btnDownloadPDF" href="#" class="btn btn-success"><i class="fas fa-file-pdf me-2"></i>Download PDF</a>
                        <a id="btnExportExcel" href="#" class="btn btn-warning"><i class="fas fa-file-excel me-2"></i>Export Excel</a>
                    </div>
                <div class="card mb-4">
                    <div class="card-header">Filter Periode</div>
                    <div class="card-body">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tgl_awal" class="form-control" value="<?= htmlspecialchars($tgl_awal) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tgl_akhir" class="form-control" value="<?= htmlspecialchars($tgl_akhir) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-2"></i>Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="alert alert-info"><strong>Total Pemasukan:</strong><br>Rp <?= number_format($total_pemasukan,0,',','.') ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-danger"><strong>Total Pengeluaran:</strong><br>Rp <?= number_format($total_pengeluaran,0,',','.') ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-success"><strong>Saldo Akhir:</strong><br>Rp <?= number_format($saldo,0,',','.') ?></div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">Rincian Pemasukan</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kategori</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pemasukan as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($p['kategori']) ?></td>
                                        <td><?= htmlspecialchars($p['keterangan']) ?></td>
                                        <td class="text-end">Rp <?= number_format($p['jumlah'],0,',','.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">Rincian Pengeluaran</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kategori</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pengeluaran as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($p['kategori']) ?></td>
                                        <td><?= htmlspecialchars($p['keterangan']) ?></td>
                                        <td class="text-end">Rp <?= number_format($p['jumlah'],0,',','.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function getPeriodeParams() {
        var tglAwal = document.querySelector('[name="tgl_awal"]')?.value;
        var tglAkhir = document.querySelector('[name="tgl_akhir"]')?.value;
        if (tglAwal && tglAkhir) {
            return '?tgl_awal=' + encodeURIComponent(tglAwal) + '&tgl_akhir=' + encodeURIComponent(tglAkhir);
        }
        return '';
    }
    document.getElementById('btnPrintPDF').onclick = function(e) {
        this.href = 'laporan_print_pdf.php' + getPeriodeParams();
    };
    document.getElementById('btnDownloadPDF').onclick = function(e) {
        this.href = 'laporan_download_pdf.php' + getPeriodeParams();
    };
    document.getElementById('btnExportExcel').onclick = function(e) {
        this.href = 'laporan_export_excel.php' + getPeriodeParams();
    };
</script>
</body>
</html>
