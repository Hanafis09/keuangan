<?php
session_start();
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
    <title>Neraca Klinik (Dalam Pengembangan)</title>
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
                    <li class="nav-item"><a class="nav-link active" href="keuangan_neraca.php"><i class="fas fa-balance-scale me-2"></i> Neraca (Pengembangan)</a></li>
                    <li class="nav-item mt-3"><hr></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="neraca_logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                <h2>Neraca Klinik</h2>
                <div class="alert alert-warning mt-4">
                    <h5 class="mb-2"><i class="fas fa-tools me-2"></i>Halaman Masih Dikembangkan</h5>
                    <p>Fitur neraca klinik (aset, hutang, modal, dll) <b>masih dalam proses pengembangan oleh Hanafi</b>.<br>
                    Silakan gunakan menu lain yang sudah aktif. Terima kasih atas kesabarannya.</p>
                </div>
                <div class="card mt-4">
                    <div class="card-header">Form Neraca (Dummy)</div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Aset</label>
                                <input type="text" class="form-control" placeholder="Aset (contoh: Kas, Bank, Piutang, dll)" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hutang</label>
                                <input type="text" class="form-control" placeholder="Hutang (contoh: Hutang Usaha, Hutang Bank, dll)" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Modal</label>
                                <input type="text" class="form-control" placeholder="Modal (contoh: Modal Awal, Laba Ditahan, dll)" disabled>
                            </div>
                            <button type="button" class="btn btn-secondary" disabled>Simpan (Pengembangan)</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
