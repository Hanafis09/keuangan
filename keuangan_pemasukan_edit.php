<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['neraca_user'])) {
    header('Location: neraca_login.php');
    exit;
}
$db = new Database();
$error = '';
$success = '';

// Ambil data pemasukan berdasarkan id
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: keuangan_pemasukan.php');
    exit;
}
$db->query("SELECT * FROM pemasukan WHERE id = :id");
$db->bind(':id', $id);
$pemasukan = $db->single();
if (!$pemasukan) {
    header('Location: keuangan_pemasukan.php');
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $kategori = trim($_POST['kategori'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $jumlah = floatval($_POST['jumlah'] ?? 0);
    $status_invoice = $_POST['status_invoice'] ?? 'Belum Lunas';
    if ($tanggal && $kategori && $jumlah > 0 && in_array($status_invoice, ['Lunas','Belum Lunas'])) {
        $db->query("UPDATE pemasukan SET tanggal = :tanggal, kategori = :kategori, keterangan = :keterangan, jumlah = :jumlah, status_invoice = :status_invoice WHERE id = :id");
        $db->bind(':tanggal', $tanggal);
        $db->bind(':kategori', $kategori);
        $db->bind(':keterangan', $keterangan);
        $db->bind(':jumlah', $jumlah);
        $db->bind(':status_invoice', $status_invoice);
        $db->bind(':id', $id);
        if ($db->execute()) {
            $success = 'Data pemasukan berhasil diupdate.';
            // Ambil data terbaru
            $db->query("SELECT * FROM pemasukan WHERE id = :id");
            $db->bind(':id', $id);
            $pemasukan = $db->single();
        } else {
            $error = 'Gagal update data pemasukan.';
        }
    } else {
        $error = 'Semua field wajib diisi dan jumlah harus lebih dari 0!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemasukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border: 1px solid rgba(0,0,0,.125); }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-warning text-dark"><i class="fas fa-edit me-2"></i>Edit Pemasukan</div>
                <div class="card-body">
                    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($pemasukan['tanggal']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">- Pilih -</option>
                                    <option value="Pasien Umum" <?= $pemasukan['kategori']=='Pasien Umum'?'selected':'' ?>>Pasien Umum</option>
                                    <option value="BPJS" <?= $pemasukan['kategori']=='BPJS'?'selected':'' ?>>BPJS</option>
                                    <option value="Asuransi" <?= $pemasukan['kategori']=='Asuransi'?'selected':'' ?>>Asuransi</option>
                                    <option value="Penjualan Obat" <?= $pemasukan['kategori']=='Penjualan Obat'?'selected':'' ?>>Penjualan Obat</option>
                                    <option value="Lainnya" <?= $pemasukan['kategori']=='Lainnya'?'selected':'' ?>>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" value="<?= htmlspecialchars($pemasukan['keterangan']) ?>" placeholder="Keterangan (opsional)">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Jumlah (Rp)</label>
                                <input type="number" name="jumlah" class="form-control" min="1" step="any" value="<?= htmlspecialchars($pemasukan['jumlah']) ?>" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Status Invoice</label>
                                <select name="status_invoice" class="form-select" required>
                                    <option value="Lunas" <?= $pemasukan['status_invoice']=='Lunas'?'selected':'' ?>>Lunas</option>
                                    <option value="Belum Lunas" <?= $pemasukan['status_invoice']=='Belum Lunas'?'selected':'' ?>>Belum Lunas</option>
                                </select>
                            </div>
                            <div class="col-md-8 text-end align-self-end">
                                <a href="keuangan_pemasukan.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
