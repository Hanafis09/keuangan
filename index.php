<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penggajihan Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 10px 20px !important;
            margin: 5px 0 !important;
            border-radius: 5px;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white !important;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0,0,0,.125);
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }
        .btn-success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <?php if (isset($_SESSION['nama'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert" style="font-size:1.1em;">
            <i class="fas fa-smile-beam me-2"></i>
            Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>! Semoga harimu menyenangkan 😊
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-3">
                        <h4 class="text-white text-center mb-4">
                            <i class="fas fa-calculator"></i>
                            Sistem Gaji
                        </h4>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="hrd.php">
                                    <i class="fas fa-user-tie me-2"></i> HRD
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="index.php">
                                    <i class="fas fa-home me-2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="pegawai.php">
                                    <i class="fas fa-users me-2"></i> Data Pegawai
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="jasa_pelayanan.php">
                                    <i class="fas fa-hand-holding-medical me-2"></i> Jasa Pelayanan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="input_jasa.php">
                                    <i class="fas fa-clipboard-list me-2"></i> Input Jasa Pelayanan Pegawai
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="penggajian.php">
                                    <i class="fas fa-money-check-alt me-2"></i> Penggajian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="laporan.php">
                                    <i class="fas fa-file-alt me-2"></i> Laporan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="pengaturan.php">
                                    <i class="fas fa-cog me-2"></i> Pengaturan
                                </a>
                            </li>
                            <li class="nav-item mt-3">
                                <hr>
                            </li>
                            <?php if(isset($_SESSION['user'])): ?>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                            <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link text-success" href="login.php">
                                    <i class="fas fa-sign-in-alt me-2"></i> Login
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="user.php">
                                    <i class="fas fa-users-cog me-2"></i> Manajemen User
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header Klinik/Instansi -->
                    <?php
                    require_once 'config.php';
                    $db = new Database();
                    $db->query("SELECT * FROM pengaturan LIMIT 1");
                    $pengaturan = $db->single();
                    ?>
                    <div class="row mb-4 align-items-center">
                        <div class="col-auto">
                            <?php if(!empty($pengaturan['logo']) && file_exists($pengaturan['logo'])): ?>
                                <img src="<?= $pengaturan['logo'] ?>" alt="Logo" style="max-height:70px;max-width:110px;" class="rounded shadow-sm me-3">
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <?php if($pengaturan): ?>
                                <div class="mb-1 fw-bold" style="font-size:1.2rem; color:#333;">
                                    <?= htmlspecialchars($pengaturan['nama_klinik']) ?>
                                </div>
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($pengaturan['alamat']) ?>
                                </div>
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-phone me-1"></i> <?= htmlspecialchars($pengaturan['no_telp']) ?>
                                    &nbsp; <i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($pengaturan['email']) ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Selamat datang di sistem penggajihan pegawai</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php
                    require_once 'config.php';
                    $db = new Database();
                    
                    // Statistik
                    $db->query("SELECT COUNT(*) as total FROM pegawai");
                    $total_pegawai = $db->single()['total'];
                    
                    $db->query("SELECT COUNT(*) as total FROM jasa_pelayanan");
                    $total_jasa = $db->single()['total'];
                    
                    $db->query("SELECT COUNT(*) as total FROM penggajian WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
                    $gaji_bulan_ini = $db->single()['total'];
                    ?>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Total Pegawai</h5>
                                            <h2 class="mb-0"><?= $total_pegawai ?></h2>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Jasa Pelayanan</h5>
                                            <h2 class="mb-0"><?= $total_jasa ?></h2>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-hand-holding-medical fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Gaji Bulan Ini</h5>
                                            <h2 class="mb-0"><?= $gaji_bulan_ini ?></h2>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-money-check-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0 fw-bold">
                                        <i class="fas fa-users me-2 text-info"></i>Data Pegawai Terbaru
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $db->query("SELECT * FROM pegawai ORDER BY created_at DESC LIMIT 5");
                                    $pegawai = $db->resultSet();
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>NIP</th>
                                                    <th>Nama</th>
                                                    <th>Jabatan</th>
                                                    <th>Gaji Pokok</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($pegawai as $p): ?>
                                                <tr>
                                                    <td><?= $p['nip'] ?></td>
                                                    <td><?= $p['nama'] ?></td>
                                                    <td><?= $p['jabatan'] ?></td>
                                                    <td>Rp <?= number_format($p['gaji_pokok'], 0, ',', '.') ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end">
                                        <a href="pegawai.php" class="btn btn-success btn-sm">Lihat Semua</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0 fw-bold">
                                        <i class="fas fa-bolt me-2 text-warning"></i>Menu Cepat
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="pegawai.php" class="btn btn-outline-primary">
                                            <i class="fas fa-user-plus me-2"></i> Tambah Pegawai
                                        </a>
                                        <a href="input_jasa.php" class="btn btn-outline-success">
                                            <i class="fas fa-plus-circle me-2"></i> Input Jasa Pelayanan
                                        </a>
                                        <a href="penggajian.php" class="btn btn-outline-warning">
                                            <i class="fas fa-calculator me-2"></i> Hitung Gaji
                                        </a>
                                        <a href="laporan.php" class="btn btn-outline-info">
                                            <i class="fas fa-print me-2"></i> Cetak Laporan
                                        </a>
                                        <div class="dropdown mt-2">
                                            <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="neracaDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-balance-scale me-2"></i> Neraca Klinik
                                            </button>
                                            <ul class="dropdown-menu w-100" aria-labelledby="neracaDropdown">
                                                <li><a class="dropdown-item" href="neraca_login.php">
                                                    <i class="fas fa-sign-in-alt me-2"></i> Login Neraca
                                                </a></li>
                                                <li><a class="dropdown-item" href="neraca_register.php">
                                                    <i class="fas fa-user-plus me-2"></i> Daftar Akun Neraca
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="neraca_info.php">
                                                    <i class="fas fa-info-circle me-2"></i> Informasi Neraca
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Footer -->
    <footer class="text-center py-3 mt-4" style="background: rgba(255,255,255,0.7); position:fixed; left:0; bottom:0; width:100%; z-index:999; font-size:15px;">
        <marquee behavior="scroll" direction="left" scrollamount="6" style="width:100%;">
            <span style="font-weight:bold; color:#007bff;">Create By Hanafi</span>
            &nbsp;|&nbsp;
            <span style="color:#333;">
                "Jangan pernah meremehkan kekuatan mimpi dan doa. Setiap langkah kecil yang kamu ambil hari ini adalah pondasi untuk masa depan yang lebih baik. Teruslah berusaha, karena keberhasilan tidak datang secara instan, melainkan melalui proses panjang yang penuh pembelajaran dan pengalaman."
                &nbsp;|&nbsp;"Jangan pernah menyerah, karena setiap kegagalan adalah pelajaran."
                &nbsp;|&nbsp;"Percayalah pada diri sendiri, kamu punya potensi besar."
                &nbsp;|&nbsp;"Setiap hari adalah kesempatan baru untuk menjadi lebih baik."
                &nbsp;|&nbsp;"Fokus pada tujuan, bukan pada rintangan."
                &nbsp;|&nbsp;"Bekerja keras hari ini, untuk kesuksesan di masa depan."
                &nbsp;|&nbsp;"Jangan biarkan rasa takut menghentikan langkahmu."
                &nbsp;|&nbsp;"Berani mencoba adalah langkah pertama menuju kesuksesan."
                &nbsp;|&nbsp;"Nikmati setiap proses, karena perjalanan itu penting."
                &nbsp;|&nbsp;"Bersyukur atas apa yang kamu miliki, dan teruslah berusaha."
                &nbsp;|&nbsp;"Jadikan setiap hari sebagai hari yang penuh semangat."
            </span>
        </marquee>
    </footer>

</body>
</html>
