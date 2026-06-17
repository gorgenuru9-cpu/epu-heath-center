<?php
/**
 * DEBUG TOOL – delete after fixing login
 */
require_once 'config/database.php';

$conn = getDBConnection();

echo "<h3>1. Database connection</h3>";
echo "Connected to: <b>" . DB_NAME . "</b> on " . DB_HOST . "<br><br>";

echo "<h3>2. Users table contents</h3>";
$res = $conn->query("SELECT id, username, full_name, role, status, LENGTH(password) AS pw_len, LEFT(password,10) AS pw_start FROM users");
if (!$res) {
    echo "ERROR: " . $conn->error;
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Role</th><th>Status</th><th>PW Length</th><th>PW Start</th></tr>";
    while ($row = $res->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $v) echo "<td>" . htmlspecialchars($v) . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
}

echo "<h3>3. Test password_verify for admin / Admin@1234</h3>";
$res2 = $conn->query("SELECT password, status FROM users WHERE username='admin' LIMIT 1");
if ($row2 = $res2->fetch_assoc()) {
    $stored = $row2['password'];
    $test   = 'Admin@1234';
    $result = password_verify($test, $stored);
    echo "Stored hash: <code>" . htmlspecialchars($stored) . "</code><br>";
    echo "Hash length: " . strlen($stored) . "<br>";
    echo "Status: <b>" . $stored . "</b><br>";
    echo "password_verify result: <b style='color:" . ($result ? 'green' : 'red') . "'>" . ($result ? 'TRUE ✅' : 'FALSE ❌') . "</b><br>";
    echo "Account status: <b>" . $row2['status'] . "</b><br>";
} else {
    echo "<span style='color:red'>No admin user found in database!</span><br>";
}

echo "<br><h3>4. Fix – reset admin password now</h3>";
$newHash = password_hash('Admin@1234', PASSWORD_BCRYPT);
echo "New hash: <code>" . $newHash . "</code><br>";
echo "Verify new hash: " . (password_verify('Admin@1234', $newHash) ? '✅ OK' : '❌ FAIL') . "<br><br>";

// Apply fix
$stmt = $conn->prepare("UPDATE users SET password=?, status='active' WHERE username='admin'");
if ($stmt) {
    $stmt->bind_param('s', $newHash);
    $stmt->execute();
    echo "<span style='color:green;font-weight:bold'>✅ Admin password updated in database!</span><br>";
    $stmt->close();
} else {
    // User doesn't exist – insert
    $name = 'System Administrator';
    $stmt2 = $conn->prepare("INSERT INTO users (full_name, username, password, role, status) VALUES (?,?,?,?,?)");
    $un = 'admin'; $role = 'admin'; $status = 'active';
    $stmt2->bind_param('sssss', $name, $un, $newHash, $role, $status);
    $stmt2->execute();
    $stmt2->close();
    echo "<span style='color:green;font-weight:bold'>✅ Admin user created!</span><br>";
}

// Verify the fix
$check = $conn->query("SELECT password, status FROM users WHERE username='admin'")->fetch_assoc();
$fixed = password_verify('Admin@1234', $check['password']);
echo "<br><b>Post-fix verification: " . ($fixed ? '✅ Login will work now!' : '❌ Still broken') . "</b>";

$conn->close();
?>
<br><br>
<a href="login.php" style="font-size:18px;padding:10px 20px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:6px">
    → Go to Login Page
</a>
<br><br>
<span style="color:red;font-size:12px">⚠ Delete this file (debug_login.php) after use!</span>
