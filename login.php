<?php
session_start();
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi!';
    } else {
        // Login ke tabel user
        $db = new Database();
        $db->query('SELECT * FROM user WHERE username = :username LIMIT 1');
        $db->bind(':username', $username);
        $user = $db->single();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Penggajihan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(270deg, #667eea, #764ba2, #a5b4fc, #667eea);
            background-size: 400% 400%;
            animation: gradientMove 12s ease-in-out infinite;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes gradientMove {
            0% {background-position:0% 50%}
            50% {background-position:100% 50%}
            100% {background-position:0% 50%}
        }
        .login-card {
            max-width: 410px;
            margin: 60px auto;
            border-radius: 22px;
            box-shadow: 0 8px 40px rgba(102,126,234,0.18);
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(8px);
            border: 1.5px solid #c7d2fe;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 1.1s cubic-bezier(.23,1.01,.32,1) 0.2s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: none;
            }
        }
        .login-header {
            background: linear-gradient(90deg,#667eea,#764ba2);
            color: #fff;
            border-radius: 22px 22px 0 0;
            text-align: center;
            padding: 28px 0 12px 0;
            box-shadow: 0 2px 12px rgba(102,126,234,0.10);
        }
        .login-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 10px;
            border-radius: 12px;
            box-shadow: 0 2px 12px #fff8;
            background: #fff;
            animation: bounceIn 1.2s cubic-bezier(.23,1.01,.32,1);
        }
        .login-header i {
            font-size: 2.2rem;
            margin-bottom: 8px;
            filter: drop-shadow(0 2px 8px #fff8);
            animation: bounceIn 1.2s cubic-bezier(.23,1.01,.32,1);
        }
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.7) translateY(-40px);
            }
            60% {
                opacity: 1;
                transform: scale(1.15) translateY(8px);
            }
            80% {
                transform: scale(0.95) translateY(-4px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        .login-body {
            padding: 36px 32px 32px 32px;
            background: rgba(255,255,255,0.92);
            border-radius: 0 0 22px 22px;
            box-shadow: 0 2px 12px rgba(102,126,234,0.07);
        }
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.2rem rgba(118,75,162,.13);
        }
        .btn-primary {
            background: linear-gradient(90deg,#667eea,#764ba2);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(102,126,234,0.13);
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg,#764ba2,#667eea);
            box-shadow: 0 4px 16px rgba(102,126,234,0.18);
        }
        .input-group-text {
            background: #f3f4fa;
            border-color: #c7d2fe;
        }
        .alert-danger {
            border-radius: 10px;
            font-size: 1em;
        }
        .login-header h3 {
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        .login-header .small {
            opacity: 0.92;
        }
        .login-body label {
            font-weight: 500;
            color: #764ba2;
        }
        .login-body .form-control {
            border-radius: 8px;
        }
        .login-body .input-group-text {
            border-radius: 8px 0 0 8px;
        }
        .login-body .input-group .form-control {
            border-radius: 0 8px 8px 0;
        }
        .login-body button[type="submit"] {
            font-size: 1.1em;
            padding: 0.7em 0;
        }
        .login-card .text-center span {
            font-size: 1.13rem;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px #e0e7ff;
        }
    </style>
</head>
<body>
    <div class="login-card shadow-lg">
        <div class="login-header">
            <!-- Ganti src logo_klinik_1755021708.png jika ingin logo lain -->
            <img src="logo_klinik_1755021708.png" alt="Logo Klinik" class="login-logo">
            <h3 class="mb-0">Sistem Penggajihan</h3>
            <div class="small mt-1">Silakan login untuk melanjutkan</div>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" required autofocus autocomplete="username">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group" id="show_hide_password">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" required autocomplete="current-password" id="passwordInput">
                        <span class="input-group-text" style="cursor:pointer;" onclick="togglePassword()"><i class="fas fa-eye" id="toggleIcon"></i></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Login <i class="fas fa-sign-in-alt ms-1"></i></button>
            </form>
            <script>
            function togglePassword() {
                var input = document.getElementById('passwordInput');
                var icon = document.getElementById('toggleIcon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
            </script>
        </div>
        <div class="text-center mt-3 mb-2">
            <span style="color:#764ba2; font-weight:bold; font-size:1.1rem; letter-spacing:1px; text-shadow:0 1px 6px #e0e7ff;">
                <i class="fas fa-user-edit me-1"></i> Create By Hanafi
            </span>
        </div>
    </div>
</body>
</html>
