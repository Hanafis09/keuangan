<?php
require_once 'config.php';
$db = new Database();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $db->query("INSERT INTO jasa_pelayanan (kode_jasa, nama_jasa, tarif_per_unit) VALUES (:kode_jasa, :nama_jasa, :tarif_per_unit)");
            $db->bind(':kode_jasa', $_POST['kode_jasa']);
            $db->bind(':nama_jasa', $_POST['nama_jasa']);
            $db->bind(':tarif_per_unit', $_POST['tarif_per_unit']);
            $db->execute();
            $message = "Data jasa pelayanan berhasil ditambahkan!";
            $message_type = "success";
        }
        
        if ($_POST['action'] == 'edit') {
            $db->query("UPDATE jasa_pelayanan SET kode_jasa = :kode_jasa, nama_jasa = :nama_jasa, tarif_per_unit = :tarif_per_unit WHERE id = :id");
            $db->bind(':id', $_POST['id']);
            $db->bind(':kode_jasa', $_POST['kode_jasa']);
            $db->bind(':nama_jasa', $_POST['nama_jasa']);
            $db->bind(':tarif_per_unit', $_POST['tarif_per_unit']);
            $db->execute();
            $message = "Data jasa pelayanan berhasil diupdate!";
            $message_type = "success";
        }
        
        if ($_POST['action'] == 'delete') {
            $db->query("DELETE FROM jasa_pelayanan WHERE id = :id");
            $db->bind(':id', $_POST['id']);
            $db->execute();
            $message = "Data jasa pelayanan berhasil dihapus!";
            $message_type = "success";
        }
    }
}

// Get all services
$db->query("SELECT * FROM jasa_pelayanan ORDER BY kode_jasa");
$jasa_pelayanan = $db->resultSet();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasa Pelayanan - Sistem Penggajihan</title>
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
                                <a class="nav-link active" href="jasa_pelayanan.php">
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
                            <h2>Jasa Pelayanan</h2>
                            <p class="text-muted">Kelola jenis jasa pelayanan dan tarifnya</p>
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
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Jasa</th>
                                            <th>Nama Jasa</th>
                                            <th>Tarif Per Unit</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach($jasa_pelayanan as $j): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><span class="badge bg-primary"><?= $j['kode_jasa'] ?></span></td>
                                            <td><?= $j['nama_jasa'] ?></td>
                                            <td>Rp <?= number_format($j['tarif_per_unit'], 0, ',', '.') ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" onclick="editJasa(<?= htmlspecialchars(json_encode($j), ENT_QUOTES) ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteJasa(<?= $j['id'] ?>, '<?= $j['nama_jasa'] ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jasa Pelayanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Kode Jasa</label>
                            <input type="text" class="form-control" name="kode_jasa" required>
                            <div class="form-text">Contoh: ECG, USG, LAB, dll</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Jasa</label>
                            <input type="text" class="form-control" name="nama_jasa" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarif Per Unit</label>
                            <input type="number" class="form-control" name="tarif_per_unit" required>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jasa Pelayanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Kode Jasa</label>
                            <input type="text" class="form-control" name="kode_jasa" id="edit_kode_jasa" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Jasa</label>
                            <input type="text" class="form-control" name="nama_jasa" id="edit_nama_jasa" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarif Per Unit</label>
                            <input type="number" class="form-control" name="tarif_per_unit" id="edit_tarif_per_unit" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Update</button>
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
        function editJasa(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_kode_jasa').value = data.kode_jasa;
            document.getElementById('edit_nama_jasa').value = data.nama_jasa;
            document.getElementById('edit_tarif_per_unit').value = data.tarif_per_unit;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function deleteJasa(id, nama) {
            if (confirm('Apakah Anda yakin ingin menghapus jasa pelayanan ' + nama + '?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
