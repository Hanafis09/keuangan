<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
$db = new Database();

// Debug database connection
try {
    $db->query("SELECT 1");
    $db->execute();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] == 'generate') {
        $pegawai_id = $_POST['pegawai_id'];
        $bulan = $_POST['bulan'];
        $tahun = $_POST['tahun'];
        
        // Get employee data
        $db->query("SELECT * FROM pegawai WHERE id = :id");
        $db->bind(':id', $pegawai_id);
        $pegawai = $db->single();
        
        // Get total jasa pelayanan for this month
        $db->query("SELECT COALESCE(SUM(total_jasa), 0) as total_jasa FROM detail_jasa_pegawai WHERE pegawai_id = :pegawai_id AND bulan = :bulan AND tahun = :tahun");
        $db->bind(':pegawai_id', $pegawai_id);
        $db->bind(':bulan', $bulan);
        $db->bind(':tahun', $tahun);
        $jasa_result = $db->single();
        $total_jasa_pelayanan = $jasa_result['total_jasa'];
        
    // Calculate gross salary
    $gross_salary = $pegawai['gaji_pokok'] + $pegawai['tunjangan_jabatan'] + $pegawai['tunjangan_makan'] + $total_jasa_pelayanan;

    // Get potongan lain if submitted
    $potongan_lain = isset($_POST['potongan_lain']) ? floatval($_POST['potongan_lain']) : 0;
    $keterangan_potongan_lain = isset($_POST['keterangan_potongan_lain']) ? trim($_POST['keterangan_potongan_lain']) : null;
    
    // Potongan hanya dari gaji pokok + tunjangan jabatan + tunjangan makan (tanpa jasa pelayanan)
    $dasar_potongan = $pegawai['gaji_pokok'] + $pegawai['tunjangan_jabatan'] + $pegawai['tunjangan_makan'];
    $potongan_pajak = $dasar_potongan * (PAJAK_PPH21 / 100);
    $potongan_bpjs_kesehatan = $dasar_potongan * (BPJS_KESEHATAN / 100);
    $potongan_bpjs_tk = $dasar_potongan * (BPJS_TK / 100);

    $total_potongan = $potongan_pajak + $potongan_bpjs_kesehatan + $potongan_bpjs_tk + $potongan_lain;
    $gaji_bersih = $gross_salary - $total_potongan;
        
        // Check if payroll already exists
        $db->query("SELECT id FROM penggajian WHERE pegawai_id = :pegawai_id AND bulan = :bulan AND tahun = :tahun");
        $db->bind(':pegawai_id', $pegawai_id);
        $db->bind(':bulan', $bulan);
        $db->bind(':tahun', $tahun);
        $existing = $db->single();
        
        if ($existing) {
            // Update existing payroll
            $db->query("UPDATE penggajian SET 
                        gaji_pokok = :gaji_pokok, 
                        tunjangan_jabatan = :tunjangan_jabatan, 
                        tunjangan_makan = :tunjangan_makan, 
                        total_jasa_pelayanan = :total_jasa_pelayanan, 
                        gross_salary = :gross_salary, 
                        potongan_pajak = :potongan_pajak, 
                        potongan_bpjs_kesehatan = :potongan_bpjs_kesehatan, 
                        potongan_bpjs_tk = :potongan_bpjs_tk,
                        potongan_lain = :potongan_lain,
                        keterangan_potongan_lain = :keterangan_potongan_lain, 
                        total_potongan = :total_potongan, 
                        gaji_bersih = :gaji_bersih 
                        WHERE id = :id");
            $db->bind(':id', $existing['id']);
        } else {
            // Insert new payroll
            $db->query("INSERT INTO penggajian (pegawai_id, bulan, tahun, gaji_pokok, tunjangan_jabatan, tunjangan_makan, total_jasa_pelayanan, gross_salary, potongan_pajak, potongan_bpjs_kesehatan, potongan_bpjs_tk, potongan_lain, keterangan_potongan_lain, total_potongan, gaji_bersih) 
                        VALUES (:pegawai_id, :bulan, :tahun, :gaji_pokok, :tunjangan_jabatan, :tunjangan_makan, :total_jasa_pelayanan, :gross_salary, :potongan_pajak, :potongan_bpjs_kesehatan, :potongan_bpjs_tk, :potongan_lain, :keterangan_potongan_lain, :total_potongan, :gaji_bersih)");
            $db->bind(':pegawai_id', $pegawai_id);
            $db->bind(':bulan', $bulan);
            $db->bind(':tahun', $tahun);
        }
        
        $db->bind(':gaji_pokok', $pegawai['gaji_pokok']);
        $db->bind(':tunjangan_jabatan', $pegawai['tunjangan_jabatan']);
        $db->bind(':tunjangan_makan', $pegawai['tunjangan_makan']);
        $db->bind(':total_jasa_pelayanan', $total_jasa_pelayanan);
        $db->bind(':gross_salary', $gross_salary);
        $db->bind(':potongan_pajak', $potongan_pajak);
        $db->bind(':potongan_bpjs_kesehatan', $potongan_bpjs_kesehatan);
        $db->bind(':potongan_bpjs_tk', $potongan_bpjs_tk);
        $db->bind(':potongan_lain', $potongan_lain);
        $db->bind(':keterangan_potongan_lain', $keterangan_potongan_lain);
        $db->bind(':total_potongan', $total_potongan);
        $db->bind(':gaji_bersih', $gaji_bersih);
        
        $db->execute();
        
        $message = "Penggajian berhasil di-generate!";
        $message_type = "success";
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $db->query("DELETE FROM penggajian WHERE id = :id");
        $db->bind(':id', $_POST['id']);
        $db->execute();
        $message = "Data penggajian berhasil dihapus!";
        $message_type = "success";
    }
}

// Get filter values
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Get all employees
$db->query("SELECT * FROM pegawai ORDER BY nama");
$pegawai_list = $db->resultSet();

// Get payroll data
$db->query("SELECT p.*, pg.nama, pg.nip 
           FROM penggajian p 
           JOIN pegawai pg ON p.pegawai_id = pg.id 
           WHERE p.bulan = :bulan AND p.tahun = :tahun 
           ORDER BY pg.nama");
$db->bind(':bulan', $filter_bulan);
$db->bind(':tahun', $filter_tahun);
$penggajian = $db->resultSet();

// Calculate totals
$total_gross = array_sum(array_column($penggajian, 'gross_salary'));
$total_potongan_all = array_sum(array_column($penggajian, 'total_potongan'));
$total_bersih = array_sum(array_column($penggajian, 'gaji_bersih'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penggajian - Sistem Penggajihan</title>
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
        .salary-breakdown {
            font-size: 0.9em;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
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
                                <a class="nav-link active" href="penggajian.php">
                                    <i class="fas fa-money-check-alt me-2"></i> Penggajian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="laporan.php">
                                    <i class="fas fa-file-alt me-2"></i> Laporan
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <div class="row mb-4">
                        <div class="col">
                            <h2>Penggajian Pegawai</h2>
                            <p class="text-muted">Generate dan kelola penggajian bulanan</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateModal">
                                <i class="fas fa-calculator me-2"></i> Generate Gaji
                            </button>
                        </div>
                    </div>
                    
                    <?php if (isset($message)): ?>
                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Filter and Summary Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="mb-3">
                                <div class="row">
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
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                                    </div>
                                </div>
                            </form>
                            
                            <?php if(!empty($penggajian)): ?>
                            <hr>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h5 class="text-primary">Total Gaji Kotor</h5>
                                    <h4>Rp <?= number_format($total_gross, 0, ',', '.') ?></h4>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="text-warning">Total Potongan</h5>
                                    <h4>Rp <?= number_format($total_potongan_all, 0, ',', '.') ?></h4>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="text-success">Total Gaji Bersih</h5>
                                    <h4>Rp <?= number_format($total_bersih, 0, ',', '.') ?></h4>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Gaji Kotor</th>
                                            <th>Jasa Pelayanan</th>
                                            <th>Potongan</th>
                                            <th>Gaji Bersih</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($penggajian)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data penggajian untuk periode ini</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php $no = 1; foreach($penggajian as $p): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $p['nip'] ?></td>
                                            <td>
                                                <strong><?= $p['nama'] ?></strong>
                                                <div class="salary-breakdown text-muted">
                                                    Pokok: Rp <?= number_format($p['gaji_pokok'], 0, ',', '.') ?> |
                                                    Jabatan: Rp <?= number_format($p['tunjangan_jabatan'], 0, ',', '.') ?> |
                                                    Makan: Rp <?= number_format($p['tunjangan_makan'], 0, ',', '.') ?>
                                                </div>
                                            </td>
                                            <td><strong>Rp <?= number_format($p['gross_salary'], 0, ',', '.') ?></strong></td>
                                            <td>
                                                <?php if($p['total_jasa_pelayanan'] > 0): ?>
                                                <span class="badge bg-success">Rp <?= number_format($p['total_jasa_pelayanan'], 0, ',', '.') ?></span>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="salary-breakdown">
                                                    <div>PPh21 (<?= PAJAK_PPH21 ?>%): Rp <?= number_format($p['potongan_pajak'], 0, ',', '.') ?></div>
                                                    <div>BPJS Kes (<?= BPJS_KESEHATAN ?>%): Rp <?= number_format($p['potongan_bpjs_kesehatan'], 0, ',', '.') ?></div>
                                                    <div>BPJS TK (<?= BPJS_TK ?>%): Rp <?= number_format($p['potongan_bpjs_tk'], 0, ',', '.') ?></div>
                                                    <?php if($p['potongan_lain'] > 0): ?>
                                                    <div>Potongan Lain: Rp <?= number_format($p['potongan_lain'], 0, ',', '.') ?>
                                                    <?php if(!empty($p['keterangan_potongan_lain'])): ?>
                                                        <br><small class="text-muted">(<?= htmlspecialchars($p['keterangan_potongan_lain']) ?>)</small>
                                                    <?php endif; ?>
                                                    </div>
                                                    <?php endif; ?>
                                                    <strong>Total: Rp <?= number_format($p['total_potongan'], 0, ',', '.') ?></strong>
                                                </div>
                                            </td>
                                            <td><strong class="text-success">Rp <?= number_format($p['gaji_bersih'], 0, ',', '.') ?></strong></td>
                                            <td>
                                                <button class="btn btn-sm btn-info me-1" onclick="viewDetail(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteGaji(<?= $p['id'] ?>, '<?= $p['nama'] ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Penggajian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="generate">
                        <div class="mb-3">
                            <label class="form-label">Pegawai</label>
                            <select name="pegawai_id" class="form-select" required>
                                <option value="">Pilih Pegawai</option>
                                <?php foreach($pegawai_list as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= $p['nama'] ?> (<?= $p['nip'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Bulan</label>
                                    <select name="bulan" class="form-select" required>
                                        <option value="1" <?= date('n') == 1 ? 'selected' : '' ?>>Januari</option>
                                        <option value="2" <?= date('n') == 2 ? 'selected' : '' ?>>Februari</option>
                                        <option value="3" <?= date('n') == 3 ? 'selected' : '' ?>>Maret</option>
                                        <option value="4" <?= date('n') == 4 ? 'selected' : '' ?>>April</option>
                                        <option value="5" <?= date('n') == 5 ? 'selected' : '' ?>>Mei</option>
                                        <option value="6" <?= date('n') == 6 ? 'selected' : '' ?>>Juni</option>
                                        <option value="7" <?= date('n') == 7 ? 'selected' : '' ?>>Juli</option>
                                        <option value="8" <?= date('n') == 8 ? 'selected' : '' ?>>Agustus</option>
                                        <option value="9" <?= date('n') == 9 ? 'selected' : '' ?>>September</option>
                                        <option value="10" <?= date('n') == 10 ? 'selected' : '' ?>>Oktober</option>
                                        <option value="11" <?= date('n') == 11 ? 'selected' : '' ?>>November</option>
                                        <option value="12" <?= date('n') == 12 ? 'selected' : '' ?>>Desember</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tahun</label>
                                    <select name="tahun" class="form-select" required>
                                        <?php for($i = date('Y')-2; $i <= date('Y')+1; $i++): ?>
                                        <option value="<?= $i ?>" <?= date('Y') == $i ? 'selected' : '' ?>><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Potongan Lain</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="potongan_lain" class="form-control" value="0" min="0" step="1000">
                            </div>
                            <input type="text" name="keterangan_potongan_lain" class="form-control" placeholder="Keterangan potongan lain (opsional)">
                            <div class="form-text">Masukkan jumlah dan keterangan potongan lain (jika ada)</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Informasi Perhitungan:</strong><br>
                            • PPh21: <?= PAJAK_PPH21 ?>%<br>
                            • BPJS Kesehatan: <?= BPJS_KESEHATAN ?>%<br>
                            • BPJS Ketenagakerjaan: <?= BPJS_TK ?>%<br>
                            <span class="text-danger">Potongan PPh21, BPJS Kesehatan, dan BPJS Ketenagakerjaan hanya dihitung dari Gaji Pokok + Tunjangan. Jasa Pelayanan tidak dipotong pajak dan BPJS.</span><br>
                            Gaji bersih = (Gaji Pokok + Tunjangan + Jasa Pelayanan) - (Total Potongan + Potongan Lain)
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalTitle">Detail Penggajian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be loaded by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewDetail(data) {
            // Set judul modal dengan nama pegawai
            document.getElementById('detailModalTitle').innerHTML = `<i class='fas fa-file-invoice-dollar me-2 text-primary'></i>Slip Gaji - <span class='text-dark'>${data.nama}</span>`;
            const content = `
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 shadow-sm">
                            <h6 class="fw-bold mb-2 text-primary"><i class="fas fa-user me-1"></i> Informasi Pegawai</h6>
                            <div class="mb-1"><span class="fw-bold">NIP:</span> <span class="badge bg-light text-dark border">${data.nip}</span></div>
                            <div class="mb-1"><span class="fw-bold">Nama:</span> ${data.nama}</div>
                            <div class="mb-1"><span class="fw-bold">Periode:</span> <span class="badge bg-info text-dark">${getMonthName(data.bulan)} ${data.tahun}</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 shadow-sm">
                            <h6 class="fw-bold mb-2 text-success"><i class="fas fa-wallet me-1"></i> Komponen Gaji</h6>
                            <div class="mb-1">Gaji Pokok: <span class="badge bg-secondary">Rp ${formatNumber(data.gaji_pokok)}</span></div>
                            <div class="mb-1">Tunjangan Jabatan: <span class="badge bg-secondary">Rp ${formatNumber(data.tunjangan_jabatan)}</span></div>
                            <div class="mb-1">Tunjangan Makan: <span class="badge bg-secondary">Rp ${formatNumber(data.tunjangan_makan)}</span></div>
                            <div class="mb-1">Jasa Pelayanan: <span class="badge bg-primary">Rp ${formatNumber(data.total_jasa_pelayanan)}</span></div>
                            <div class="mt-2 border-top pt-2">Gaji Kotor: <span class="badge bg-success fs-6">Rp ${formatNumber(data.gross_salary)}</span></div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 shadow-sm">
                            <h6 class="fw-bold mb-2 text-danger"><i class="fas fa-minus-circle me-1"></i> Potongan</h6>
                            <div class="mb-1">PPh21 (${<?= PAJAK_PPH21 ?>}%): <span class="badge bg-warning text-dark">Rp ${formatNumber(data.potongan_pajak)}</span></div>
                            <div class="mb-1">BPJS Kesehatan (${<?= BPJS_KESEHATAN ?>}%): <span class="badge bg-warning text-dark">Rp ${formatNumber(data.potongan_bpjs_kesehatan)}</span></div>
                            <div class="mb-1">BPJS Ketenagakerjaan (${<?= BPJS_TK ?>}%): <span class="badge bg-warning text-dark">Rp ${formatNumber(data.potongan_bpjs_tk)}</span></div>
                            <div class="mb-1">Potongan Lain: <span class="badge bg-warning text-dark">Rp ${formatNumber(data.potongan_lain || 0)}</span>
                            ${data.keterangan_potongan_lain ? `<br><small class="text-muted">(${data.keterangan_potongan_lain})</small>` : ''}
                            </div>
                            <div class="mt-2 border-top pt-2">Total Potongan: <span class="badge bg-danger fs-6">Rp ${formatNumber(data.total_potongan)}</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 shadow-sm bg-success text-white">
                            <h6 class="fw-bold mb-2"><i class="fas fa-money-bill-wave me-1"></i> Gaji Bersih</h6>
                            <div class="display-6 fw-bold">Rp ${formatNumber(data.gaji_bersih)}</div>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('detailContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }

        function deleteGaji(id, nama) {
            if (confirm('Apakah Anda yakin ingin menghapus data penggajian untuk ' + nama + '?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
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
