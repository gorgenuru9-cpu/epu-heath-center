<?php
/**
 * One-time admin password reset tool.
 * Visit: http://localhost/EPU health/reset_admin.php
 * DELETE THIS FILE after use.
 */
require_once 'config/database.php';

$password = 'Admin@1234';
$hash     = password_hash($password, PASSWORD_BCRYPT);

$conn = getDBConnection();

// Check if admin user exists
$res  = $conn->query("SELECT id, username, password FROM users WHERE username = 'admin' LIMIT 1");
$user = $res->fetch_assoc();

if ($user) {
    // Update existing admin
    $stmt = $conn->prepare("UPDATE users SET password = ?, status = 'active' WHERE username = 'admin'");
    $stmt->bind_param('s', $hash);
    $stmt->execute();
    $stmt->close();
    $msg = "✅ Admin password reset to <strong>Admin@1234</strong>";
} else {
    // Insert fresh admin
    $stmt = $conn->prepare(
        "INSERT INTO users (full_name, username, password, role, status) VALUES (?, 'admin', ?, 'admin', 'active')"
    );
    $name = 'System Administrator';
    $stmt->bind_param('ss', $name, $hash);
    $stmt->execute();
    $stmt->close();
    $msg = "✅ Admin account created with password <strong>Admin@1234</strong>";
}

// Verify it works
$verify = $conn->query("SELECT password FROM users WHERE username = 'admin'")->fetch_assoc();
$ok     = password_verify($password, $verify['password']);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reset – EPU Health</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="card shadow p-4" style="max-width:480px;width:100%">
    <h5 class="fw-bold text-center mb-3">EPU Health – Admin Reset</h5>

    <div class="alert <?= $ok ? 'alert-success' : 'alert-danger' ?>">
        <?= $msg ?><br>
        <strong>Login verification:</strong> <?= $ok ? '✅ Password verifies correctly' : '❌ Verification FAILED' ?>
    </div>

    <table class="table table-sm table-bordered">
        <tr><th>URL</th><td><code>http://localhost/EPU health/login.php</code></td></tr>
        <tr><th>Username</th><td><code>admin</code></td></tr>
        <tr><th>Password</th><td><code>Admin@1234</code></td></tr>
    </table>

    <div class="d-grid gap-2">
        <a href="login.php" class="btn btn-primary">Go to Login</a>
    </div>

    <p class="text-danger text-center small mt-3 mb-0">
        ⚠ Delete <strong>reset_admin.php</strong> after use.
    </p>
</div>
</body>
</html>
