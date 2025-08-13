<?php
require_once 'config.php';
$db = new Database();
// Ambil data pengaturan klinik/instansi
$db->query("SELECT * FROM pengaturan LIMIT 1");
$pengaturan = $db->single();
// Ambil nama HRD terbaru
$db->query("SELECT nama FROM hrd ORDER BY id DESC LIMIT 1");
$hrd_row = $db->single();
$nama_hrd = $hrd_row ? $hrd_row['nama'] : '';

// Get filter values
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_pegawai = isset($_GET['pegawai_id']) ? $_GET['pegawai_id'] : '';

// Get all employees
$db->query("SELECT * FROM pegawai ORDER BY nama");
$pegawai_list = $db->resultSet();

// Build query
$query = "SELECT p.*, pg.nama, pg.nip, pg.jabatan 
          FROM penggajian p 
          JOIN pegawai pg ON p.pegawai_id = pg.id 
          WHERE p.bulan = :bulan AND p.tahun = :tahun";

if (!empty($filter_pegawai)) {
    $query .= " AND p.pegawai_id = :pegawai_id";
}

$query .= " ORDER BY pg.nama";

$db->query($query);
$db->bind(':bulan', $filter_bulan);
$db->bind(':tahun', $filter_tahun);
if (!empty($filter_pegawai)) {
    $db->bind(':pegawai_id', $filter_pegawai);
}
$laporan = $db->resultSet();

// Calculate totals
$total_gross = array_sum(array_column($laporan, 'gross_salary'));
$total_potongan = array_sum(array_column($laporan, 'total_potongan'));
$total_bersih = array_sum(array_column($laporan, 'gaji_bersih'));

// Month names
$bulan_names = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penggajian - Sistem Penggajihan</title>
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
        @media print {
            .no-print { display: none !important; }
            .sidebar { display: none !important; }
            .main-content { width: 100% !important; margin: 0 !important; padding: 0 !important; }
            body { background: white !important; }
        }
        .slip-gaji {
            border: 2px solid #000;
            padding: 20px;
            margin: 10px 0;
            background: white;
        }
        .slip-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 no-print">
                <div class="sidebar">
                    <div class="p-3">
                        <h4 class="text-white text-center mb-4">
                            <i class="fas fa-calculator"></i>
                            Sistem Gaji
                        </h4>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">
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
                                    <i class="fas fa-clipboard-list me-2"></i> Input Jasa Pegawai
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="penggajian.php">
                                    <i class="fas fa-money-check-alt me-2"></i> Penggajian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="laporan.php">
                                    <i class="fas fa-file-alt me-2"></i> Laporan
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="p-4">
                    <div class="row mb-4 no-print">
                        <div class="col">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-alt fa-2x text-primary me-2"></i>
                                <h2 class="fw-bold mb-0">Laporan Penggajian</h2>
                            </div>
                            <div class="border-bottom mb-2" style="height:3px;width:60px;background:linear-gradient(90deg,#667eea,#764ba2)"></div>
                            <p class="text-muted mb-0">Laporan dan slip gaji pegawai</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-success" onclick="window.print()">
                                <i class="fas fa-print me-2"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filter Card -->
                    <div class="card mb-4 no-print">
                        <div class="card-body bg-light">
                            <form method="GET">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Bulan</label>
                                        <select name="bulan" class="form-select">
                                            <option value="1" <?= $filter_bulan == 1 ? 'selected' : '' ?>>Januari</option>
                                            <option value="2" <?= $filter_bulan == 2 ? 'selected' : '' ?>>Februari</option>
                                            <option value="3" <?= $filter_bulan == 3 ? 'selected' : '' ?>>Maret</option>
                                            <option value="4" <?= $filter_bulan == 4 ? 'selected' : '' ?>>April</option>
                                            <option value="5" <?= $filter_bulan == 5 ? 'selected' : '' ?>>Mei</option>
                                            <option value="6" <?= $filter_bulan == 6 ? 'selected' : '' ?>>Juni</option>
                                            <option value="7" <?= $filter_bulan == 7 ? 'selected' : '' ?>>Juli</option>
                                            <option value="8" <?= $filter_bulan == 8 ? 'selected' : '' ?>>Agustus</option>
                                            <option value="9" <?= $filter_bulan == 9 ? 'selected' : '' ?>>September</option>
                                            <option value="10" <?= $filter_bulan == 10 ? 'selected' : '' ?>>Oktober</option>
                                            <option value="11" <?= $filter_bulan == 11 ? 'selected' : '' ?>>November</option>
                                            <option value="12" <?= $filter_bulan == 12 ? 'selected' : '' ?>>Desember</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tahun</label>
                                        <select name="tahun" class="form-select">
                                            <?php for($i = date('Y')-2; $i <= date('Y')+1; $i++): ?>
                                            <option value="<?= $i ?>" <?= $filter_tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Pegawai</label>
                                        <select name="pegawai_id" class="form-select">
                                            <option value="">Semua Pegawai</option>
                                            <?php foreach($pegawai_list as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= $filter_pegawai == $p['id'] ? 'selected' : '' ?>><?= $p['nama'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Report Header -->
                    <div class="text-center mb-4">
                        <h3>LAPORAN PENGGAJIAN PEGAWAI</h3>
                        <h5>Periode: <?= $bulan_names[$filter_bulan] ?> <?= $filter_tahun ?></h5>
                        <hr>
                    </div>
                    
                    <?php if (empty($laporan)): ?>
                    <div class="alert alert-warning text-center">
                        <h5>Tidak ada data penggajian untuk periode yang dipilih</h5>
                        <p>Silakan pilih periode lain atau generate penggajian terlebih dahulu.</p>
                    </div>
                    <?php else: ?>
                    
                    <!-- Summary Table -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Ringkasan Penggajian</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h6>Jumlah Pegawai</h6>
                                    <h4 class="text-primary"><?= count($laporan) ?> Orang</h4>
                                </div>
                                <div class="col-md-3">
                                    <h6>Total Gaji Kotor</h6>
                                    <h4 class="text-info">Rp <?= number_format($total_gross, 0, ',', '.') ?></h4>
                                </div>
                                <div class="col-md-3">
                                    <h6>Total Potongan</h6>
                                    <h4 class="text-warning">Rp <?= number_format($total_potongan, 0, ',', '.') ?></h4>
                                </div>
                                <div class="col-md-3">
                                    <h6>Total Gaji Bersih</h6>
                                    <h4 class="text-success">Rp <?= number_format($total_bersih, 0, ',', '.') ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Table -->
                    <div class="card mb-4 no-print">
                        <div class="card-header">
                            <h5 class="mb-0">Detail Penggajian</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="bg-primary text-white">No</th>
                                            <th class="bg-info text-dark">NIP</th>
                                            <th class="bg-info text-dark">Nama</th>
                                            <th class="bg-info text-dark">Jabatan</th>
                                            <th class="bg-secondary text-white">Gaji Pokok</th>
                                            <th class="bg-secondary text-white">Tunjangan</th>
                                            <th class="bg-primary text-white">Jasa Pelayanan</th>
                                            <th class="bg-success text-white">Gaji Kotor</th>
                                            <th class="bg-danger text-white">Potongan</th>
                                            <th class="bg-info text-dark">Gaji Bersih</th>
                                            <th class="bg-warning text-dark no-print">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach($laporan as $l): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $l['nip'] ?></td>
                                            <td><?= $l['nama'] ?></td>
                                            <td><?= $l['jabatan'] ?></td>
                                            <td>Rp <?= number_format($l['gaji_pokok'], 0, ',', '.') ?></td>
                                            <td>Rp <?= number_format($l['tunjangan_jabatan'] + $l['tunjangan_makan'], 0, ',', '.') ?></td>
                                            <td>Rp <?= number_format($l['total_jasa_pelayanan'], 0, ',', '.') ?></td>
                                            <td><strong>Rp <?= number_format($l['gross_salary'], 0, ',', '.') ?></strong></td>
                                            <td>
                                                Rp <?= number_format($l['total_potongan'], 0, ',', '.') ?>
                                                <?php if ($l['potongan_lain'] > 0): ?>
                                                    <br><small class="text-muted">
                                                        (Termasuk Potongan Lain: Rp <?= number_format($l['potongan_lain'], 0, ',', '.') ?>
                                                        <?= !empty($l['keterangan_potongan_lain']) ? ' - ' . htmlspecialchars($l['keterangan_potongan_lain']) : '' ?>)
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong class="text-success">Rp <?= number_format($l['gaji_bersih'], 0, ',', '.') ?></strong></td>
                                            <td class="no-print">
                                                <button class="btn btn-sm btn-info" onclick="printSlip(<?= htmlspecialchars(json_encode($l), ENT_QUOTES) ?>)">
                                                    <i class="fas fa-print"></i> Slip
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th colspan="7" class="text-end">TOTAL:</th>
                                            <th>Rp <?= number_format($total_gross, 0, ',', '.') ?></th>
                                            <th>Rp <?= number_format($total_potongan, 0, ',', '.') ?></th>
                                            <th>Rp <?= number_format($total_bersih, 0, ',', '.') ?></th>
                                        </tr>
                                                                <td><span class="badge bg-primary">Rp <?= number_format($l['total_jasa_pelayanan'], 0, ',', '.') ?></span></td>
                                                                <td><span class="badge bg-success">Rp <?= number_format($l['gross_salary'], 0, ',', '.') ?></span></td>
                                                                <td><span class="badge bg-danger">Rp <?= number_format($l['total_potongan'], 0, ',', '.') ?></span></td>
                                                                <td><span class="badge bg-info text-dark">Rp <?= number_format($l['gaji_bersih'], 0, ',', '.') ?></span></td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- Ringkasan Total -->
                                                <div class="row mt-3">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="alert alert-success py-2 mb-0"><strong>Total Gaji Kotor:</strong> Rp <?= number_format($total_gross, 0, ',', '.') ?></div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="alert alert-danger py-2 mb-0"><strong>Total Potongan:</strong> Rp <?= number_format($total_potongan, 0, ',', '.') ?></div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="alert alert-info py-2 mb-0"><strong>Total Gaji Bersih:</strong> Rp <?= number_format($total_bersih, 0, ',', '.') ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                    
                    <!-- Footer -->
                    <div class="text-center mt-4 no-print">
                        <small class="text-muted">
                            Laporan digenerate pada <?= date('d/m/Y H:i:s') ?> | 
                            Sistem Penggajihan Pegawai
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printSlip(data) {
            // Create a new window with the slip content
            const printWindow = window.open('', '_blank');
            const slipContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Slip Gaji - ${data.nama}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background: #f8f9fa; }
                        .slip-gaji {
                            border: 1.5px solid #667eea;
                            border-radius: 14px;
                            box-shadow: 0 6px 32px rgba(102,126,234,0.13);
                            padding: 28px 24px;
                            background: #fff;
                            max-width: 600px;
                            margin: 0 auto;
                            position: relative;
                            overflow: hidden;
                        }
                        .slip-gaji::before {
                            content: '';
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            width: 340px;
                            height: 340px;
                            background: url('<?= !empty($pengaturan['logo']) && file_exists($pengaturan['logo']) ? $pengaturan['logo'] : '' ?>') center center no-repeat;
                            background-size: 180px 180px;
                            opacity: 0.07;
                            transform: translate(-50%, -50%);
                            pointer-events: none;
                            z-index: 0;
                        }
                        .slip-header {
                            border-bottom: 1.5px solid #d1d5db;
                            padding-bottom: 15px;
                            margin-bottom: 22px;
                            text-align: center;
                            background: linear-gradient(90deg,rgba(102,126,234,0.97),rgba(118,75,162,0.93) 80%);
                            color: #fff;
                            border-radius: 12px 12px 0 0;
                            box-shadow: 0 2px 12px rgba(102,126,234,0.10);
                            position: relative;
                            z-index: 1;
                        }
                        .logo-slip {
                            max-height:60px;
                            max-width:100px;
                            display:block;
                            margin-left:auto;
                            margin-right:auto;
                            background:#fff;
                            border-radius:8px;
                            box-shadow:0 2px 8px rgba(0,0,0,0.07);
                        }
                        .identitas-slip {
                            font-size: 1.08em;
                            color: #fff;
                            display:inline-block;
                            text-align:center;
                        }
                        .slip-section-title {
                            font-weight:600;
                            color:#764ba2;
                            margin-top:14px;
                            margin-bottom:7px;
                            letter-spacing:0.5px;
                        }
                        .table th, .table td {
                            vertical-align: middle !important;
                            border-color: #e0e7ff !important;
                        }
                        .badge-slip {
                            font-size:1em;
                            padding:.45em 1em;
                            border-radius:1em;
                            background: linear-gradient(90deg,#e0e7ff,#c7d2fe);
                            color: #333;
                            box-shadow: 0 1px 4px rgba(102,126,234,0.07);
                            border: 1px solid #c7d2fe;
                            font-weight: 500;
                            transition: box-shadow 0.2s;
                        }
                        .badge-slip:hover {
                            box-shadow: 0 2px 8px rgba(102,126,234,0.18);
                        }
                        .gaji-bersih-box {
                            background: linear-gradient(90deg,#667eea,#764ba2);
                            color:#fff;
                            border-radius:10px;
                            padding:20px 0 16px 0;
                            font-size:1.35em;
                            font-weight:bold;
                            box-shadow:0 2px 12px rgba(102,126,234,0.13);
                            margin-top:18px;
                            border: 1.5px solid #c7d2fe;
                        }
                        .slip-ttd {
                            margin-top:32px;
                        }
                        .slip-ttd .col-6 {
                            text-align:center;
                        }
                        .slip-ttd p {
                            margin-bottom:38px;
                        }
                        .slip-ttd .border-top {
                            margin-top:18px;
                            border-top: 1.2px dashed #764ba2 !important;
                            display: inline-block;
                            min-width: 120px;
                        }
                        @media print {
                            body { margin: 0; background: #fff; font-size: 12px; }
                            .slip-gaji {
                                box-shadow: none;
                                page-break-inside: avoid;
                                page-break-after: avoid;
                                width: 100%;
                                max-width: 100%;
                                min-height: unset;
                                padding: 12px 10px !important;
                                border-width: 1.2px;
                            }
                            .slip-header { padding-bottom: 8px !important; margin-bottom: 12px !important; }
                            .slip-section-title { margin-top: 10px !important; margin-bottom: 4px !important; font-size: 1em !important; }
                            .badge-slip { font-size: 0.95em !important; padding: .3em .7em !important; }
                            .gaji-bersih-box { font-size: 1.1em !important; padding: 10px 0 !important; margin-top: 10px !important; }
                            .slip-ttd { margin-top: 20px !important; }
                            .slip-ttd p { margin-bottom: 30px !important; }
                            .slip-ttd .border-top { margin-top: 10px !important; }
                            table { margin-bottom: 10px !important; }
                            html, body { width: 210mm; height: 297mm; }
                            @page { size: A4; margin: 10mm 8mm 10mm 8mm; }
                        }
                        @media print { body { margin: 0; } }
                    </style>
                </head>
                <body>
                    <div class="slip-gaji">
                        <div class="slip-header mb-3 p-3">
                            <?php if(!empty($pengaturan['logo']) && file_exists($pengaturan['logo'])): ?>
                                <img src="<?= $pengaturan['logo'] ?>" class="logo-slip mb-2">
                            <?php endif; ?>
                            <div class="identitas-slip">
                                <div class="fw-bold" style="font-size:1.2em;"><?= addslashes($pengaturan['nama_klinik'] ?? '') ?></div>
                                <div><?= addslashes($pengaturan['alamat'] ?? '') ?></div>
                                <div>Telp: <?= addslashes($pengaturan['no_telp'] ?? '') ?> | Email: <?= addslashes($pengaturan['email'] ?? '') ?></div>
                            </div>
                            <hr class="my-2" style="border-color:#fff;">
                            <div class="text-center">
                                <h3 class="mb-1" style="font-weight:700; letter-spacing:1px;">SLIP GAJI PEGAWAI</h3>
                                <h5 style="font-weight:500;">Periode: ${getMonthName(data.bulan)} ${data.tahun}</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <span class="slip-section-title">Data Pegawai</span>
                                <div><strong>NIP:</strong> ${data.nip}</div>
                                <div><strong>Nama:</strong> ${data.nama}</div>
                                <div><strong>Jabatan:</strong> ${data.jabatan}</div>
                            </div>
                            <div class="col-6 text-end">
                                <span class="slip-section-title">Tanggal Cetak</span>
                                <div>${new Date().toLocaleDateString('id-ID')}</div>
                            </div>
                        </div>
                        <hr style="border-top: 1.2px dashed #c7d2fe; margin: 10px 0 18px 0;">
                        <span class="slip-section-title">Rincian Pendapatan</span>
                        <table class="table table-bordered mb-3">
                            <tr class="table-light">
                                <th colspan="2" class="text-center">PENDAPATAN</th>
                            </tr>
                            <tr><td>Gaji Pokok</td><td class="text-end"><span class="badge bg-secondary badge-slip">Rp ${formatNumber(data.gaji_pokok)}</span></td></tr>
                            <tr><td>Tunjangan Jabatan</td><td class="text-end"><span class="badge bg-secondary badge-slip">Rp ${formatNumber(data.tunjangan_jabatan)}</span></td></tr>
                            <tr><td>Tunjangan Makan</td><td class="text-end"><span class="badge bg-secondary badge-slip">Rp ${formatNumber(data.tunjangan_makan)}</span></td></tr>
                            <tr><td>Jasa Pelayanan</td><td class="text-end"><span class="badge bg-primary badge-slip">Rp ${formatNumber(data.total_jasa_pelayanan)}</span></td></tr>
                            <tr class="table-success">
                                <th>Total Pendapatan (Bruto)</th>
                                <th class="text-end"><span class="badge bg-success badge-slip">Rp ${formatNumber(data.gross_salary)}</span></th>
                            </tr>
                        </table>
                        <span class="slip-section-title">Rincian Potongan</span>
                        <table class="table table-bordered mb-3">
                            <tr class="table-light"><th colspan="2" class="text-center">POTONGAN</th></tr>
                            <tr><td>PPh21 (${<?= PAJAK_PPH21 ?>}%)</td><td class="text-end"><span class="badge bg-warning text-dark badge-slip">Rp ${formatNumber(data.potongan_pajak)}</span></td></tr>
                            <tr><td>BPJS Kesehatan (${<?= BPJS_KESEHATAN ?>}%)</td><td class="text-end"><span class="badge bg-warning text-dark badge-slip">Rp ${formatNumber(data.potongan_bpjs_kesehatan)}</span></td></tr>
                            <tr><td>BPJS Ketenagakerjaan (${<?= BPJS_TK ?>}%)</td><td class="text-end"><span class="badge bg-warning text-dark badge-slip">Rp ${formatNumber(data.potongan_bpjs_tk)}</span></td></tr>
                            ${data.potongan_lain > 0 ? `
                            <tr>
                                <td>Potongan Lain${data.keterangan_potongan_lain ? ` (${data.keterangan_potongan_lain})` : ''}</td>
                                <td class="text-end"><span class="badge bg-warning text-dark badge-slip">Rp ${formatNumber(data.potongan_lain)}</span></td>
                            </tr>
                            ` : ''}
                            <tr class="table-warning">
                                <th>Total Potongan</th>
                                <th class="text-end"><span class="badge bg-danger badge-slip">Rp ${formatNumber(data.total_potongan)}</span></th>
                            </tr>
                        </table>
                        <div class="gaji-bersih-box text-center">
                            TAKE HOME PAY (GAJI BERSIH): <br>Rp ${formatNumber(data.gaji_bersih)}
                        </div>
                        <div class="row slip-ttd">
                            <div class="col-6">
                                <p>Pegawai,</p>
                                <p class="border-top d-inline-block px-3">${data.nama}</p>
                            </div>
                            <div class="col-6">
                                <p>HRD,</p>
                                <p class="border-top d-inline-block px-3"><?= addslashes($nama_hrd) ?: '(.....................)' ?></p>
                            </div>
                        </div>
                    </div>
                    <script>window.onload = function() { window.print(); window.close(); }<\/script>
                </body>
                </html>
            `;
            
            // Kirim data pengaturan ke window slip
            printWindow.pengaturanLogo = "<?= !empty($pengaturan['logo']) && file_exists($pengaturan['logo']) ? $pengaturan['logo'] : '' ?>";
            printWindow.pengaturanNama = "<?= addslashes($pengaturan['nama_klinik'] ?? '') ?>";
            printWindow.pengaturanAlamat = "<?= addslashes($pengaturan['alamat'] ?? '') ?>";
            printWindow.pengaturanTelp = "<?= addslashes($pengaturan['no_telp'] ?? '') ?>";
            printWindow.pengaturanEmail = "<?= addslashes($pengaturan['email'] ?? '') ?>";
            printWindow.document.write(slipContent);
            printWindow.document.close();
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function getMonthName(month) {
            const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return months[parseInt(month)];
        }
    </script>
</body>
</html>
