<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
if (!empty($_SESSION['user_id'])) { header('Location: ' . app_url('index.php')); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id, full_name, username, password, role, status FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && $user['status'] === 'Active' && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        header('Location: ' . app_url('index.php')); exit;
    }
    $error = 'Invalid username or password, or the account is inactive.';
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login | Library</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"></head>
<body class="hold-transition login-page"><div class="login-box"><div class="login-logo"><b>Library</b> Management</div><div class="card"><div class="card-body login-card-body"><p class="login-box-msg">Sign in to start your session</p>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post"><div class="input-group mb-3"><input class="form-control" name="username" placeholder="Username" required><div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div></div>
<div class="input-group mb-3"><input class="form-control" type="password" name="password" placeholder="Password" required><div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div></div>
<button type="submit" class="btn btn-primary btn-block">Sign In</button></form><p class="mt-3 mb-0"><small>First time? Run <a href="../setup_admin.php">administrator setup</a>.</small></p></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
