<?php
// hrd.php - Menu HRD
require_once 'config.php';
$db = new Database();

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $kontak = $_POST['kontak'];
    $email = $_POST['email'];
    $db->query("INSERT INTO hrd (nama, jabatan, kontak, email) VALUES (:nama, :jabatan, :kontak, :email)");
    $db->bind(':nama', $nama);
    $db->bind(':jabatan', $jabatan);
    $db->bind(':kontak', $kontak);
    $db->bind(':email', $email);
    $db->execute();
    header('Location: hrd.php?success=1');
    exit;
}

// Ambil data HRD
$db->query("SELECT * FROM hrd ORDER BY id DESC");
$hrd_list = $db->resultSet();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu HRD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">Data HRD</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success">Data HRD berhasil ditambahkan!</div>
                        <?php endif; ?>
                        <form method="POST" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <input type="text" name="nama" class="form-control" placeholder="Nama HRD" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="jabatan" class="form-control" placeholder="Jabatan" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="kontak" class="form-control" placeholder="No. Kontak" required>
                            </div>
                            <div class="col-md-2">
                                <input type="email" name="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-info">Tambah HRD</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Kontak</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($hrd_list as $h): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($h['nama']) ?></td>
                                        <td><?= htmlspecialchars($h['jabatan']) ?></td>
                                        <td><?= htmlspecialchars($h['kontak']) ?></td>
                                        <td><?= htmlspecialchars($h['email']) ?></td>
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
</body>
</html>
