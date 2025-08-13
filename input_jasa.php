<?php
require_once 'config.php';
$db = new Database();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            // Calculate total jasa
            $total_jasa = $_POST['jumlah_unit'] * $_POST['tarif_per_unit'];
            
            // Check if record exists for this pegawai, jasa, bulan, tahun
            $db->query("SELECT id FROM detail_jasa_pegawai WHERE pegawai_id = :pegawai_id AND jasa_id = :jasa_id AND bulan = :bulan AND tahun = :tahun");
            $db->bind(':pegawai_id', $_POST['pegawai_id']);
            $db->bind(':jasa_id', $_POST['jasa_id']);
            $db->bind(':bulan', $_POST['bulan']);
            $db->bind(':tahun', $_POST['tahun']);
            $existing = $db->single();
            
            if ($existing) {
                // Update existing record
                $db->query("UPDATE detail_jasa_pegawai SET jumlah_unit = jumlah_unit + :jumlah_unit, total_jasa = total_jasa + :total_jasa WHERE id = :id");
                $db->bind(':id', $existing['id']);
                $db->bind(':jumlah_unit', $_POST['jumlah_unit']);
                $db->bind(':total_jasa', $total_jasa);
                $db->execute();
                $message = "Data jasa pelayanan berhasil ditambahkan ke existing record!";
            } else {
                // Insert new record
                $db->query("INSERT INTO detail_jasa_pegawai (pegawai_id, jasa_id, bulan, tahun, jumlah_unit, total_jasa) VALUES (:pegawai_id, :jasa_id, :bulan, :tahun, :jumlah_unit, :total_jasa)");
                $db->bind(':pegawai_id', $_POST['pegawai_id']);
                $db->bind(':jasa_id', $_POST['jasa_id']);
                $db->bind(':bulan', $_POST['bulan']);
                $db->bind(':tahun', $_POST['tahun']);
                $db->bind(':jumlah_unit', $_POST['jumlah_unit']);
                $db->bind(':total_jasa', $total_jasa);
                $db->execute();
                $message = "Data jasa pelayanan berhasil ditambahkan!";
            }
            $message_type = "success";
        }
        
            if ($_POST['action'] == 'edit') {
                $db->query("UPDATE detail_jasa_pegawai SET pegawai_id = :pegawai_id, jasa_id = :jasa_id, bulan = :bulan, tahun = :tahun, jumlah_unit = :jumlah_unit, total_jasa = :total_jasa WHERE id = :id");
                $db->bind(':id', $_POST['id']);
                $db->bind(':pegawai_id', $_POST['pegawai_id']);
                $db->bind(':jasa_id', $_POST['jasa_id']);
                $db->bind(':bulan', $_POST['bulan']);
                $db->bind(':tahun', $_POST['tahun']);
                $db->bind(':jumlah_unit', $_POST['jumlah_unit']);
                $db->bind(':total_jasa', $_POST['jumlah_unit'] * $_POST['tarif_per_unit']);
                $db->execute();
                $message = "Data jasa pelayanan berhasil diupdate!";
                $message_type = "success";
            }
            if ($_POST['action'] == 'delete') {
                $db->query("DELETE FROM detail_jasa_pegawai WHERE id = :id");
                $db->bind(':id', $_POST['id']);
                $db->execute();
                $message = "Data jasa pelayanan berhasil dihapus!";
                $message_type = "success";
            }
    }
}

// Get filter values
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_pegawai = isset($_GET['pegawai_id']) ? $_GET['pegawai_id'] : '';

// Get all employees
$db->query("SELECT * FROM pegawai ORDER BY nama");
$pegawai_list = $db->resultSet();

// Get all services
$db->query("SELECT * FROM jasa_pelayanan ORDER BY kode_jasa");
$jasa_list = $db->resultSet();

// Get filtered data
$query = "SELECT djp.*, p.nama, p.nip, jp.kode_jasa, jp.nama_jasa, jp.tarif_per_unit 
          FROM detail_jasa_pegawai djp 
          JOIN pegawai p ON djp.pegawai_id = p.id 
          JOIN jasa_pelayanan jp ON djp.jasa_id = jp.id 
          WHERE djp.bulan = :bulan AND djp.tahun = :tahun";

if (!empty($filter_pegawai)) {
    $query .= " AND djp.pegawai_id = :pegawai_id";
}

$query .= " ORDER BY p.nama, jp.kode_jasa";

$db->query($query);
$db->bind(':bulan', $filter_bulan);
$db->bind(':tahun', $filter_tahun);
if (!empty($filter_pegawai)) {
    $db->bind(':pegawai_id', $filter_pegawai);
}
$detail_jasa = $db->resultSet();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Jasa Pegawai - Sistem Penggajihan</title>
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
                                <a class="nav-link active" href="input_jasa.php">
                                    <i class="fas fa-clipboard-list me-2"></i> Input Jasa Pegawai
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
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <div class="row mb-4">
                        <div class="col">
                            <h2>Input Jasa Pelayanan Pegawai</h2>
                            <p class="text-muted">Kelola jasa pelayanan yang dikerjakan pegawai</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fas fa-plus me-2"></i> Tambah Jasa
                            </button>
                        </div>
                    </div>
                    
                    <?php if (isset($message)): ?>
                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Filter Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET">
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
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Data Jasa Pelayanan Pegawai</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama Pegawai</th>
                                            <th>Jasa</th>
                                            <th>Jumlah Unit</th>
                                            <th>Tarif</th>
                                            <th>Total</th>
                                            <th>Periode</th>
                                            <th>Tanggal Input</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($detail_jasa)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data jasa pelayanan</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php $no = 1; foreach($detail_jasa as $d): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $d['nip'] ?></td>
                                            <td><?= $d['nama'] ?></td>
                                            <td>
                                                <span class="badge bg-primary me-1"><?= $d['kode_jasa'] ?></span>
                                                <?= $d['nama_jasa'] ?>
                                            </td>
                                            <td><span class="badge bg-info text-dark"><?= $d['jumlah_unit'] ?></span></td>
                                            <td><span class="badge bg-secondary">Rp <?= number_format($d['tarif_per_unit'], 0, ',', '.') ?></span></td>
                                            <td><span class="badge bg-success">Rp <?= number_format($d['total_jasa'], 0, ',', '.') ?></span></td>
                                            <td><span class="badge bg-light text-dark border"><?= date('M Y', mktime(0,0,0,$d['bulan'],1,$d['tahun'])) ?></span></td>
                                            <td><span class="badge bg-secondary text-white"><?= date('d-m-Y H:i', strtotime($d['created_at'])) ?></span></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" title="Edit" onclick="editJasa(<?= htmlspecialchars(json_encode($d), ENT_QUOTES) ?>)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-outline-danger" title="Hapus" onclick="deleteJasa(<?= $d['id'] ?>, '<?= $d['nama'] ?>', '<?= $d['nama_jasa'] ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jasa Pelayanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" onsubmit="return validateForm()">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pegawai</label>
                                    <select name="pegawai_id" id="edit_pegawai_id" class="form-select" required>
                                        <option value="">Pilih Pegawai</option>
                                        <?php foreach($pegawai_list as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= $p['nama'] ?> (<?= $p['nip'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jasa Pelayanan</label>
                                    <select name="jasa_id" id="edit_jasa_id" class="form-select" required onchange="updateTarifEdit()">
                                        <option value="">Pilih Jasa</option>
                                        <?php foreach($jasa_list as $j): ?>
                                        <option value="<?= $j['id'] ?>" data-tarif="<?= $j['tarif_per_unit'] ?>"><?= $j['kode_jasa'] ?> - <?= $j['nama_jasa'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Bulan</label>
                                    <select name="bulan" id="edit_bulan" class="form-select" required>
                                        <?php for($i=1;$i<=12;$i++): ?>
                                        <option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,1)) ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tahun</label>
                                    <select name="tahun" id="edit_tahun" class="form-select" required>
                                        <?php for($i = date('Y')-2; $i <= date('Y')+1; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Unit</label>
                                    <input type="number" class="form-control" name="jumlah_unit" id="edit_jumlah_unit" min="1" required onchange="calculateTotalEdit()">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tarif Per Unit</label>
                                    <input type="number" class="form-control" name="tarif_per_unit" id="edit_tarif_per_unit" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Jasa</label>
                            <input type="text" class="form-control" id="edit_total_preview" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    function editJasa(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_pegawai_id').value = data.pegawai_id;
        document.getElementById('edit_jasa_id').value = data.jasa_id;
        document.getElementById('edit_bulan').value = data.bulan;
        document.getElementById('edit_tahun').value = data.tahun;
        document.getElementById('edit_jumlah_unit').value = data.jumlah_unit;
        document.getElementById('edit_tarif_per_unit').value = data.tarif_per_unit;
        document.getElementById('edit_total_preview').value = 'Rp ' + (data.jumlah_unit * data.tarif_per_unit).toLocaleString('id-ID');
        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    }
    function updateTarifEdit() {
        var jasaSelect = document.getElementById('edit_jasa_id');
        var selectedOption = jasaSelect.options[jasaSelect.selectedIndex];
        var tarif = selectedOption.getAttribute('data-tarif') || 0;
        document.getElementById('edit_tarif_per_unit').value = tarif;
        calculateTotalEdit();
    }
    function calculateTotalEdit() {
        var jumlah = document.getElementById('edit_jumlah_unit').value || 0;
        var tarif = document.getElementById('edit_tarif_per_unit').value || 0;
        var total = jumlah * tarif;
        document.getElementById('edit_total_preview').value = 'Rp ' + total.toLocaleString('id-ID');
    }
    </script>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jasa Pelayanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" onsubmit="return validateForm()">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pegawai</label>
                                    <select name="pegawai_id" class="form-select" required>
                                        <option value="">Pilih Pegawai</option>
                                        <?php foreach($pegawai_list as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= $p['nama'] ?> (<?= $p['nip'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jasa Pelayanan</label>
                                    <select name="jasa_id" id="jasa_id" class="form-select" required onchange="updateTarif()">
                                        <option value="">Pilih Jasa</option>
                                        <?php foreach($jasa_list as $j): ?>
                                        <option value="<?= $j['id'] ?>" data-tarif="<?= $j['tarif_per_unit'] ?>"><?= $j['kode_jasa'] ?> - <?= $j['nama_jasa'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tahun</label>
                                    <select name="tahun" class="form-select" required>
                                        <?php for($i = date('Y')-2; $i <= date('Y')+1; $i++): ?>
                                        <option value="<?= $i ?>" <?= date('Y') == $i ? 'selected' : '' ?>><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Unit</label>
                                    <input type="number" class="form-control" name="jumlah_unit" id="jumlah_unit" min="1" required onchange="calculateTotal()">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tarif Per Unit</label>
                                    <input type="number" class="form-control" name="tarif_per_unit" id="tarif_per_unit" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Jasa</label>
                            <input type="text" class="form-control" id="total_preview" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
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
        function updateTarif() {
            var jasaSelect = document.getElementById('jasa_id');
            var selectedOption = jasaSelect.options[jasaSelect.selectedIndex];
            var tarif = selectedOption.getAttribute('data-tarif') || 0;
            document.getElementById('tarif_per_unit').value = tarif;
            calculateTotal();
        }

        function calculateTotal() {
            var jumlah = document.getElementById('jumlah_unit').value || 0;
            var tarif = document.getElementById('tarif_per_unit').value || 0;
            var total = jumlah * tarif;
            document.getElementById('total_preview').value = 'Rp ' + total.toLocaleString('id-ID');
        }

        function validateForm() {
            var jumlahTambah = document.getElementById('jumlah_unit') ? document.getElementById('jumlah_unit').value : null;
            var jumlahEdit = document.getElementById('edit_jumlah_unit') ? document.getElementById('edit_jumlah_unit').value : null;
            if (jumlahTambah !== null && jumlahTambah !== '' && (!/^[0-9]+$/.test(jumlahTambah) || parseInt(jumlahTambah) < 1)) {
                alert('Jumlah unit minimal 1');
                return false;
            }
            if (jumlahEdit !== null && jumlahEdit !== '' && (!/^[0-9]+$/.test(jumlahEdit) || parseInt(jumlahEdit) < 1)) {
                alert('Jumlah unit minimal 1');
                return false;
            }
            return true;
        }

        function deleteJasa(id, nama, jasa) {
            if (confirm('Apakah Anda yakin ingin menghapus jasa ' + jasa + ' untuk pegawai ' + nama + '?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
