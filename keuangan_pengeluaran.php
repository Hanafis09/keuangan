<?php
session_start();
if (!isset($_SESSION['neraca_user'])) {
    header('Location: neraca_login.php');
    exit;
}
require_once 'config.php';
$db = new Database();

// Proses input pengeluaran
$error = '';
$success = '';
// Proses hapus pengeluaran
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $db->query("DELETE FROM pengeluaran WHERE id = :id");
    $db->bind(':id', $id_hapus);
    if ($db->execute()) {
        $success = 'Data pengeluaran berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus data pengeluaran.';
    }
}
// Proses input pengeluaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) {
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $kategori = trim($_POST['kategori'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $jumlah = floatval($_POST['jumlah'] ?? 0);
    $status_invoice = $_POST['status_invoice'] ?? 'Lunas';
    if ($tanggal && $kategori && $jumlah > 0) {
        $db->query("INSERT INTO pengeluaran (tanggal, kategori, keterangan, jumlah, status_invoice, created_by) VALUES (:tanggal, :kategori, :keterangan, :jumlah, :status_invoice, :created_by)");
        $db->bind(':tanggal', $tanggal);
        $db->bind(':kategori', $kategori);
        $db->bind(':keterangan', $keterangan);
        $db->bind(':jumlah', $jumlah);
        $db->bind(':status_invoice', $status_invoice);
        $db->bind(':created_by', $_SESSION['neraca_user']);
        if ($db->execute()) {
            $success = 'Pengeluaran berhasil dicatat.';
        } else {
            $error = 'Gagal mencatat pengeluaran.';
        }
    } else {
        $error = 'Semua field wajib diisi dan jumlah harus lebih dari 0!';
    }
}
// Ambil data pengeluaran
$db->query("SELECT p.*, u.nama as user_nama FROM pengeluaran p LEFT JOIN user_neraca u ON p.created_by = u.id ORDER BY p.tanggal DESC, p.id DESC");
$pengeluaran = $db->resultSet();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran Keuangan Klinik</title>
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
                    <li class="nav-item"><a class="nav-link active" href="keuangan_pengeluaran.php"><i class="fas fa-arrow-up me-2"></i> Pengeluaran</a></li>
                    <li class="nav-item"><a class="nav-link" href="keuangan_laporan.php"><i class="fas fa-file-alt me-2"></i> Laporan Keuangan</a></li>
                    <li class="nav-item mt-3"><hr></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="neraca_logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                <h2>Pencatatan Pengeluaran</h2>
                <p class="text-muted">Catat semua pengeluaran klinik, seperti operasional, gaji, pembelian obat, dan lainnya.</p>
                <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                <div class="card mb-4">
                    <div class="card-header">Input Pengeluaran</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="">- Pilih -</option>
                                        <option value="Operasional">Operasional</option>
                                        <option value="Gaji Pegawai">Gaji Pegawai</option>
                                        <option value="Pembelian Obat">Pembelian Obat</option>
                                        <option value="Peralatan Medis">Peralatan Medis</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" name="keterangan" class="form-control" placeholder="Keterangan (opsional)">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Jumlah (Rp)</label>
                                    <input type="number" name="jumlah" class="form-control" min="1" step="any" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status Invoice</label>
                                    <select name="status_invoice" class="form-select">
                                        <option value="Lunas">Lunas</option>
                                        <option value="Belum Lunas">Belum Lunas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-danger"><i class="fas fa-save me-2"></i>Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Daftar Pengeluaran</div>
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
                                    <?php foreach($pengeluaran as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($p['kategori']) ?></td>
                                        <td><?= htmlspecialchars($p['keterangan']) ?></td>
                                        <td class="text-end">Rp <?= number_format($p['jumlah'],0,',','.') ?></td>
                                        <td><?= htmlspecialchars($p['status_invoice']) ?></td>
                                        <td><?= htmlspecialchars($p['user_nama']) ?></td>
                                        <td class="text-center">
                                            <a href="keuangan_pengeluaran_edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="keuangan_pengeluaran.php?hapus=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i> Hapus</a>
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
