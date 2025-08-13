<?php
// hash.php
// Utility untuk generate hash password bcrypt
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_BCRYPT);
    echo '<b>Password:</b> ' . htmlspecialchars($password) . '<br>';
    echo '<b>Hash:</b> <code>' . htmlspecialchars($hash) . '</code>';
    echo '<br><a href="hash.php">Kembali</a>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate Hash Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow mt-5">
                    <div class="card-body">
                        <h4 class="mb-4 text-center">Generate Hash Password</h4>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="text" name="password" class="form-control" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Generate Hash</button>
                        </form>
                        <div class="text-muted small mt-3 text-center">Gunakan hash ini untuk di database user</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
