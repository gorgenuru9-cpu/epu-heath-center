<?php
session_start();
require_once 'includes/auth.php';

// Route already-logged-in users to their home
if (isLoggedIn()) {
    $home = $_SESSION['role'] === 'department_head'
        ? '/EPU health/dept_head/dashboard.php'
        : '/EPU health/dashboard.php';
    header("Location: $home");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password']       ?? '';

    if ($username && $password) {
        if (loginUser($username, $password)) {
            logAction('Login', 'auth');
            // Send each role to their own home page
            $home = $_SESSION['role'] === 'department_head'
                ? '/EPU health/dept_head/dashboard.php'
                : '/EPU health/dashboard.php';
            header("Location: $home");
            exit();
        } else {
            $error = 'Invalid username or password, or account is inactive.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – EPU Health Center</title>
    <link href="/EPU health/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/EPU health/assets/fonts/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a6eb5 0%, #0d3d6e 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            max-width: 430px; width: 100%;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .logo-circle {
            width: 80px; height: 80px;
            background: #1a6eb5;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
<div class="card login-card p-4">
    <div class="card-body">
        <div class="logo-circle">
            <i class="bi bi-heart-pulse-fill text-white fs-1"></i>
        </div>
        <h4 class="text-center fw-bold text-primary mb-0">EPU Health Center</h4>
        <p class="text-center text-muted small mb-4">
            Ethiopia Police University – Clinic Management System
        </p>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           placeholder="Enter username" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control"
                           placeholder="Enter password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
            </button>
        </form>

        <p class="text-center text-muted small mt-3 mb-0">
            Default login &mdash; Username: <code>admin</code>
            &nbsp;|&nbsp; Password: <code>Admin@1234</code>
        </p>
    </div>
</div>
<script src="/EPU health/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
