<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}
$db = new Database();
// Tambah user
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $role = trim($_POST['role'] ?? 'operator');
    if ($username === '' || $password === '' || $nama === '') {
        $error = 'Semua field wajib diisi!';
    } else {
        $db->query('SELECT id FROM user WHERE username = :username');
        $db->bind(':username', $username);
        if ($db->single()) {
            $error = 'Username sudah terdaftar!';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $db->query('INSERT INTO user (username, password, nama, role) VALUES (:username, :password, :nama, :role)');
            $db->bind(':username', $username);
            $db->bind(':password', $hash);
            $db->bind(':nama', $nama);
            $db->bind(':role', $role);
            $db->execute();
            $success = 'User berhasil ditambahkan!';
        }
    }
}
// Hapus user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 1) { // Jangan hapus user id 1 (admin utama)
        $db->query('DELETE FROM user WHERE id = :id');
        $db->bind(':id', $id);
        $db->execute();
        header('Location: user.php?deleted=1');
        exit;
    }
}
if (isset($_GET['deleted'])) {
    $success = 'User berhasil dihapus!';
}
// List user
$db->query('SELECT * FROM user ORDER BY id ASC');
$users = $db->resultSet();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f3f4fa; }
        .user-card { max-width: 600px; margin: 40px auto; border-radius: 16px; box-shadow: 0 4px 24px rgba(102,126,234,0.13); }
        .user-header { background: linear-gradient(90deg,#667eea,#764ba2); color: #fff; border-radius: 16px 16px 0 0; text-align: center; padding: 24px 0 12px 0; }
        .user-header h3 { font-weight: 700; letter-spacing: 1px; }
        .user-body { padding: 28px 24px 24px 24px; background: #fff; border-radius: 0 0 16px 16px; }
        .table th, .table td { vertical-align: middle; }
        .badge-admin { background: #764ba2; }
        .badge-operator { background: #667eea; }
    </style>
</head>
<body>
    <div class="user-card shadow-lg">
        <div class="text-end pt-4 pe-4">
            <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
        </div>
        <div class="user-header">
            <h3><i class="fas fa-users-cog me-2"></i>Manajemen User</h3>
            <div class="small">Tambah, lihat, dan hapus user aplikasi</div>
        </div>
        <div class="user-body">
            <?php if ($success): ?>
            <div class="alert alert-success"> <i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($success) ?> </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert alert-danger"> <i class="fas fa-exclamation-triangle me-1"></i> <?= htmlspecialchars($error) ?> </div>
            <?php endif; ?>
            <form method="post" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="operator">Operator</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['nama']) ?></td>
                            <td>
                                <span class="badge <?= $u['role']==='admin'?'badge-admin':'badge-operator' ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if($u['id'] > 1): ?>
                                <a href="user.php?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus user ini?')"><i class="fas fa-trash"></i></a>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
