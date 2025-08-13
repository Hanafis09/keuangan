<?php
require_once 'config.php';
$db = new Database();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $db->query("INSERT INTO pegawai (nip, nama, jabatan, gaji_pokok, tunjangan_jabatan, tunjangan_makan) VALUES (:nip, :nama, :jabatan, :gaji_pokok, :tunjangan_jabatan, :tunjangan_makan)");
            $db->bind(':nip', $_POST['nip']);
            $db->bind(':nama', $_POST['nama']);
            $db->bind(':jabatan', $_POST['jabatan']);
            $db->bind(':gaji_pokok', $_POST['gaji_pokok']);
            $db->bind(':tunjangan_jabatan', $_POST['tunjangan_jabatan']);
            $db->bind(':tunjangan_makan', $_POST['tunjangan_makan']);
            $db->execute();
            $message = "Data pegawai berhasil ditambahkan!";
            $message_type = "success";
        }
        
        if ($_POST['action'] == 'edit') {
            $db->query("UPDATE pegawai SET nip = :nip, nama = :nama, jabatan = :jabatan, gaji_pokok = :gaji_pokok, tunjangan_jabatan = :tunjangan_jabatan, tunjangan_makan = :tunjangan_makan WHERE id = :id");
            $db->bind(':id', $_POST['id']);
            $db->bind(':nip', $_POST['nip']);
            $db->bind(':nama', $_POST['nama']);
            $db->bind(':jabatan', $_POST['jabatan']);
            $db->bind(':gaji_pokok', $_POST['gaji_pokok']);
            $db->bind(':tunjangan_jabatan', $_POST['tunjangan_jabatan']);
            $db->bind(':tunjangan_makan', $_POST['tunjangan_makan']);
            $db->execute();
            $message = "Data pegawai berhasil diupdate!";
            $message_type = "success";
        }
        
        if ($_POST['action'] == 'delete') {
            $db->query("DELETE FROM pegawai WHERE id = :id");
            $db->bind(':id', $_POST['id']);
            $db->execute();
            $message = "Data pegawai berhasil dihapus!";
            $message_type = "success";
        }
    }
}

// Get all employees
$db->query("SELECT * FROM pegawai ORDER BY nama");
$pegawai = $db->resultSet();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai - Sistem Penggajihan</title>
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
                                <a class="nav-link active" href="pegawai.php">
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
                            <h2>Data Pegawai</h2>
                            <p class="text-muted">Kelola data pegawai dan informasi gaji</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fas fa-plus me-2"></i> Tambah Pegawai
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
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Gaji Pokok</th>
                                            <th>Tunjangan Jabatan</th>
                                            <th>Tunjangan Makan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach($pegawai as $p): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $p['nip'] ?></td>
                                            <td><?= $p['nama'] ?></td>
                                            <td><?= $p['jabatan'] ?></td>
                                            <td>Rp <?= number_format($p['gaji_pokok'], 0, ',', '.') ?></td>
                                            <td>Rp <?= number_format($p['tunjangan_jabatan'], 0, ',', '.') ?></td>
                                            <td>Rp <?= number_format($p['tunjangan_makan'], 0, ',', '.') ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" onclick="editPegawai(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deletePegawai(<?= $p['id'] ?>, '<?= $p['nama'] ?>')">
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
                    <h5 class="modal-title">Tambah Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fas fa-id-badge me-1"></i> NIP</label>
                                <input type="text" class="form-control" name="nip" required placeholder="Nomor Induk Pegawai">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fas fa-user me-1"></i> Nama</label>
                                <input type="text" class="form-control" name="nama" required placeholder="Nama Lengkap">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fas fa-briefcase me-1"></i> Jabatan</label>
                                <input type="text" class="form-control" name="jabatan" required placeholder="Jabatan Pegawai">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fas fa-money-bill-wave me-1"></i> Gaji Pokok</label>
                                <input type="number" class="form-control" name="gaji_pokok" required min="0" placeholder="Gaji Pokok">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fas fa-gift me-1"></i> Tunjangan Jabatan</label>
                                <input type="number" class="form-control" name="tunjangan_jabatan" value="0" min="0" placeholder="Tunjangan Jabatan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fas fa-utensils me-1"></i> Tunjangan Makan</label>
                                <input type="number" class="form-control" name="tunjangan_makan" value="0" min="0" placeholder="Tunjangan Makan">
                            </div>
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
                    <h5 class="modal-title">Edit Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" class="form-control" name="nip" id="edit_nip" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" id="edit_nama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" class="form-control" name="jabatan" id="edit_jabatan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gaji Pokok</label>
                            <input type="number" class="form-control" name="gaji_pokok" id="edit_gaji_pokok" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tunjangan Jabatan</label>
                            <input type="number" class="form-control" name="tunjangan_jabatan" id="edit_tunjangan_jabatan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tunjangan Makan</label>
                            <input type="number" class="form-control" name="tunjangan_makan" id="edit_tunjangan_makan">
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
        function editPegawai(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_nip').value = data.nip;
            document.getElementById('edit_nama').value = data.nama;
            document.getElementById('edit_jabatan').value = data.jabatan;
            document.getElementById('edit_gaji_pokok').value = data.gaji_pokok;
            document.getElementById('edit_tunjangan_jabatan').value = data.tunjangan_jabatan;
            document.getElementById('edit_tunjangan_makan').value = data.tunjangan_makan;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function deletePegawai(id, nama) {
            if (confirm('Apakah Anda yakin ingin menghapus data pegawai ' + nama + '?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
