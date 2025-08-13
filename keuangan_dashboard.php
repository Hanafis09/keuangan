<?php
session_start();
// Cek apakah sudah login neraca
if (!isset($_SESSION['neraca_user'])) {
    header('Location: neraca_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan Klinik</title>
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
                    <li class="nav-item"><a class="nav-link active" href="keuangan_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="keuangan_pemasukan.php"><i class="fas fa-arrow-down me-2"></i> Pemasukan</a></li>
                    <li class="nav-item"><a class="nav-link" href="keuangan_pengeluaran.php"><i class="fas fa-arrow-up me-2"></i> Pengeluaran</a></li>
                    <li class="nav-item"><a class="nav-link" href="keuangan_laporan.php"><i class="fas fa-file-alt me-2"></i> Laporan Keuangan</a></li>
                    <li class="nav-item"><a class="nav-link" href="keuangan_neraca.php"><i class="fas fa-balance-scale me-2"></i> Neraca <span class="badge bg-warning text-dark ms-1">Pengembangan</span></a></li>
                    <li class="nav-item mt-3"><hr></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="neraca_logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                <h2>Dashboard Keuangan Klinik</h2>
                <p class="text-muted">Selamat datang di sistem keuangan klinik. Berikut ringkasan keuangan dan transaksi terbaru.</p>

                <?php
                require_once 'config.php';
                $db = new Database();
                // Total saldo kas
                $db->query("SELECT SUM(jumlah) as total FROM pemasukan");
                $total_pemasukan = $db->single()['total'] ?? 0;
                $db->query("SELECT SUM(jumlah) as total FROM pengeluaran");
                $total_pengeluaran = $db->single()['total'] ?? 0;
                $saldo_kas = $total_pemasukan - $total_pengeluaran;

                // Pemasukan bulan ini
                $bulan_ini = date('Y-m');
                $db->query("SELECT SUM(jumlah) as total FROM pemasukan WHERE DATE_FORMAT(tanggal,'%Y-%m') = :bulan");
                $db->bind(':bulan', $bulan_ini);
                $pemasukan_bulan = $db->single()['total'] ?? 0;
                // Pengeluaran bulan ini
                $db->query("SELECT SUM(jumlah) as total FROM pengeluaran WHERE DATE_FORMAT(tanggal,'%Y-%m') = :bulan");
                $db->bind(':bulan', $bulan_ini);
                $pengeluaran_bulan = $db->single()['total'] ?? 0;

                // Rekapan invoice pemasukan
                $db->query("SELECT COUNT(*) as total FROM pemasukan WHERE status_invoice = 'Lunas'");
                $invoice_lunas = $db->single()['total'] ?? 0;
                $db->query("SELECT COUNT(*) as total FROM pemasukan WHERE status_invoice = 'Belum Lunas'");
                $invoice_belum = $db->single()['total'] ?? 0;
                // Rekapan invoice pengeluaran
                $db->query("SELECT COUNT(*) as total FROM pengeluaran WHERE status_invoice = 'Lunas'");
                $invoice_pengeluaran_lunas = $db->single()['total'] ?? 0;
                $db->query("SELECT COUNT(*) as total FROM pengeluaran WHERE status_invoice = 'Belum Lunas'");
                $invoice_pengeluaran_belum = $db->single()['total'] ?? 0;

                // Invoice terbaru
                $db->query("SELECT tanggal, kategori, keterangan, jumlah, status_invoice FROM pemasukan ORDER BY tanggal DESC, id DESC LIMIT 5");
                $invoice_terbaru = $db->resultSet();
                // Invoice pengeluaran terbaru
                $db->query("SELECT tanggal, kategori, keterangan, jumlah, status_invoice FROM pengeluaran ORDER BY tanggal DESC, id DESC LIMIT 5");
                $invoice_pengeluaran_terbaru = $db->resultSet();
                // Transaksi terbaru
                $db->query("SELECT tanggal, kategori, keterangan, jumlah FROM pemasukan ORDER BY tanggal DESC, id DESC LIMIT 5");
                $pemasukan_terbaru = $db->resultSet();
                $db->query("SELECT tanggal, kategori, keterangan, jumlah FROM pengeluaran ORDER BY tanggal DESC, id DESC LIMIT 5");
                $pengeluaran_terbaru = $db->resultSet();
                ?>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <div class="card-title fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>Invoice Pengeluaran Lunas</div>
                                <h4><?= $invoice_pengeluaran_lunas ?> Invoice</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <div class="card-title fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>Invoice Pengeluaran Belum Lunas</div>
                                <h4><?= $invoice_pengeluaran_belum ?> Invoice</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <div class="card-title fw-bold"><i class="fas fa-file-invoice me-2"></i>Invoice Lunas</div>
                                <h4><?= $invoice_lunas ?> Invoice</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <div class="card-title fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>Invoice Belum Lunas</div>
                                <h4><?= $invoice_belum ?> Invoice</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <div class="card-title fw-bold"><i class="fas fa-wallet me-2"></i>Saldo Kas</div>
                                <h3>Rp <?= number_format($saldo_kas,0,',','.') ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <div class="card-title fw-bold"><i class="fas fa-arrow-down me-2"></i>Pemasukan Bulan Ini</div>
                                <h4>Rp <?= number_format($pemasukan_bulan,0,',','.') ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-danger mb-3">
                            <div class="card-body">
                                <div class="card-title fw-bold"><i class="fas fa-arrow-up me-2"></i>Pengeluaran Bulan Ini</div>
                                <h4>Rp <?= number_format($pengeluaran_bulan,0,',','.') ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">Invoice Pemasukan Terbaru</div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Jumlah (Rp)</th>
                                            <th>Status Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($invoice_terbaru as $inv): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($inv['tanggal']) ?></td>
                                            <td><?= htmlspecialchars($inv['kategori']) ?></td>
                                            <td><?= htmlspecialchars($inv['keterangan']) ?></td>
                                            <td class="text-end">Rp <?= number_format($inv['jumlah'],0,',','.') ?></td>
                                            <td><?= htmlspecialchars($inv['status_invoice']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div class="mt-2 text-end">
                                    <a href="keuangan_invoice.php" class="btn btn-info btn-sm"><i class="fas fa-file-invoice me-1"></i>Lihat Rekapan Invoice</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">Invoice Pengeluaran Terbaru</div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Jumlah (Rp)</th>
                                            <th>Status Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($invoice_pengeluaran_terbaru as $inv): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($inv['tanggal']) ?></td>
                                            <td><?= htmlspecialchars($inv['kategori']) ?></td>
                                            <td><?= htmlspecialchars($inv['keterangan']) ?></td>
                                            <td class="text-end">Rp <?= number_format($inv['jumlah'],0,',','.') ?></td>
                                            <td><?= htmlspecialchars($inv['status_invoice']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div class="mt-2 text-end">
                                    <a href="keuangan_invoice_pengeluaran.php" class="btn btn-warning btn-sm"><i class="fas fa-file-invoice-dollar me-1"></i>Lihat Rekapan Invoice Pengeluaran</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">Transaksi Pemasukan Terbaru</div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Jumlah (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($pemasukan_terbaru as $p): ?>
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
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-danger text-white">Transaksi Pengeluaran Terbaru</div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Jumlah (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($pengeluaran_terbaru as $p): ?>
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

                <div class="mb-4">
                    <a href="keuangan_pemasukan.php" class="btn btn-success me-2"><i class="fas fa-plus me-1"></i>Tambah Pemasukan</a>
                    <a href="keuangan_pengeluaran.php" class="btn btn-danger me-2"><i class="fas fa-plus me-1"></i>Tambah Pengeluaran</a>
                    <a href="keuangan_laporan.php" class="btn btn-primary"><i class="fas fa-file-alt me-1"></i>Laporan Keuangan</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
