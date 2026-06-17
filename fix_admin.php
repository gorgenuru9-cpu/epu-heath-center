<?php
require_once 'config/database.php';
$conn = getDBConnection();

$hash = password_hash('Admin@1234', PASSWORD_BCRYPT);

// Check if admin exists
$res  = $conn->query("SELECT id FROM users WHERE username='admin' LIMIT 1");
$user = $res->fetch_assoc();

if ($user) {
    $conn->query("UPDATE users SET password='$hash', status='active' WHERE username='admin'");
} else {
    $conn->query("INSERT INTO users (full_name, username, password, role, status)
                  VALUES ('System Administrator','admin','$hash','admin','active')");
}

// Confirm
$row = $conn->query("SELECT username, status, password FROM users WHERE username='admin'")->fetch_assoc();
$ok  = password_verify('Admin@1234', $row['password']);
$conn->close();

if ($ok && $row['status'] === 'active') {
    header("Location: login.php");
    exit();
}
echo "Something went wrong. Status: {$row['status']}, Verify: " . ($ok ? 'OK' : 'FAIL');
?>
