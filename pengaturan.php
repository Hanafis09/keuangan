<?php
require_once 'config.php';
$db = new Database();

// Ambil data pengaturan jika ada
$db->query("SELECT * FROM pengaturan LIMIT 1");
$pengaturan = $db->single();

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_klinik = $_POST['nama_klinik'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $email = $_POST['email'];
    $logo = $pengaturan['logo'] ?? '';

    // Handle upload logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo_name = 'logo_klinik_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo_name);
        $logo = $logo_name;
    }

    // Simpan ke database (insert/update)
    if ($pengaturan) {
        $db->query("UPDATE pengaturan SET nama_klinik=:nama_klinik, alamat=:alamat, no_telp=:no_telp, email=:email, logo=:logo");
    } else {
        $db->query("INSERT INTO pengaturan (nama_klinik, alamat, no_telp, email, logo) VALUES (:nama_klinik, :alamat, :no_telp, :email, :logo)");
    }
    $db->bind(':nama_klinik', $nama_klinik);
    $db->bind(':alamat', $alamat);
    $db->bind(':no_telp', $no_telp);
    $db->bind(':email', $email);
    $db->bind(':logo', $logo);
    $db->execute();
    header('Location: pengaturan.php?success=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Klinik/Instansi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Pengaturan Klinik / Instansi</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success">Pengaturan berhasil disimpan!</div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Klinik / Instansi</label>
                                <input type="text" name="nama_klinik" class="form-control" required value="<?= htmlspecialchars($pengaturan['nama_klinik'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" required><?= htmlspecialchars($pengaturan['alamat'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="no_telp" class="form-control" required value="<?= htmlspecialchars($pengaturan['no_telp'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($pengaturan['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo Klinik/Instansi</label><br>
                                <?php if(!empty($pengaturan['logo']) && file_exists($pengaturan['logo'])): ?>
                                    <img src="<?= $pengaturan['logo'] ?>" alt="Logo" style="max-height:80px;max-width:120px;" class="mb-2"><br>
                                <?php endif; ?>
                                <input type="file" name="logo" class="form-control">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti logo.</small>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">Simpan Pengaturan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
