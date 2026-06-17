<?php
require_once 'config/database.php';

$conn     = getDBConnection();
$username = 'meg';
$password = '12345678';
$role     = 'department_head';
$name     = 'Meg';

// Check if username already exists
$check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$check->bind_param('s', $username);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
$check->close();

if ($exists) {
    // Update existing
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare(
        "UPDATE users SET password=?, role=?, status='active' WHERE username=?"
    );
    $stmt->bind_param('sss', $hash, $role, $username);
    $stmt->execute();
    $stmt->close();
    $action = 'updated';
} else {
    // Insert new
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare(
        "INSERT INTO users (full_name, username, password, role, status) VALUES (?,?,?,?,'active')"
    );
    $stmt->bind_param('ssss', $name, $username, $hash, $role);
    $stmt->execute();
    $stmt->close();
    $action = 'created';
}

// Verify
$row = $conn->query("SELECT * FROM users WHERE username='meg'")->fetch_assoc();
$ok  = password_verify($password, $row['password']);
$conn->close();

if ($ok && $row['status'] === 'active') {
    // Auto redirect to login
    header("Location: login.php");
    exit();
}
echo "Something went wrong. Verify: " . ($ok ? 'OK' : 'FAIL') . " | Status: " . $row['status'];
?>
