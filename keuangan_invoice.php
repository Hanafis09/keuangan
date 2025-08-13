<?php
session_start();
if (!isset($_SESSION['neraca_user'])) {
    header('Location: neraca_login.php');
    exit;
}
require_once 'config.php';

$db = new Database();
// Proses hapus pemasukan
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $hapus_id = intval($_GET['hapus']);
    $db->query("DELETE FROM pemasukan WHERE id = :id");
    $db->bind(':id', $hapus_id);
    $db->execute();
    header('Location: keuangan_invoice.php?status=' . urlencode($status));
    exit;
}

// Filter status invoice
$status = isset($_GET['status']) ? $_GET['status'] : '';
if ($status === 'Lunas' || $status === 'Belum Lunas') {
    $db->query("SELECT p.*, u.nama as user_nama FROM pemasukan p LEFT JOIN user_neraca u ON p.created_by = u.id WHERE p.status_invoice = :status ORDER BY p.tanggal DESC, p.id DESC");
    $db->bind(':status', $status);
    $pemasukan = $db->resultSet();
} else {
    $db->query("SELECT p.*, u.nama as user_nama FROM pemasukan p LEFT JOIN user_neraca u ON p.created_by = u.id ORDER BY p.tanggal DESC, p.id DESC");
    $pemasukan = $db->resultSet();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapan Invoice Pemasukan</title>
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
                    <li class="nav-item"><a class="nav-link" href="keuangan_laporan.php"><i class="fas fa-file-alt me-2"></i> Laporan Keuangan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="keuangan_invoice.php"><i class="fas fa-file-invoice me-2"></i> Rekapan Invoice</a></li>
                    <li class="nav-item mt-3"><hr></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="neraca_logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                <h2>Rekapan Invoice Pemasukan</h2>
                <p class="text-muted">Daftar pemasukan berdasarkan status invoice (lunas/belum lunas).</p>
                <div class="mb-3">
                    <a href="keuangan_invoice_print_pdf.php?status=<?= urlencode($status) ?>" target="_blank" class="btn btn-primary"><i class="fas fa-print me-2"></i>Print PDF</a>
                </div>
                <form method="GET" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Filter Status Invoice</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">- Semua -</option>
                                <option value="Lunas" <?= $status=='Lunas'?'selected':'' ?>>Lunas</option>
                                <option value="Belum Lunas" <?= $status=='Belum Lunas'?'selected':'' ?>>Belum Lunas</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="card">
                    <div class="card-header">Daftar Invoice Pemasukan</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kategori</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah (Rp)</th>
                                        <th>Status Invoice</th>
                                        <th>Dicatat Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($pemasukan as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($p['kategori']) ?></td>
                                        <td><?= htmlspecialchars($p['keterangan']) ?></td>
                                        <td class="text-end">Rp <?= number_format($p['jumlah'],0,',','.') ?></td>
                                        <td><?= htmlspecialchars($p['status_invoice']) ?></td>
                                        <td><?= htmlspecialchars($p['user_nama']) ?></td>
                                        <td>
                                            <a href="keuangan_pemasukan_edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="keuangan_invoice.php?hapus=<?= $p['id'] ?>&status=<?= urlencode($status) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                        </td>
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
</body>
</html>
