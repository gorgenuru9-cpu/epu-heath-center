<?php
require_once 'config/database.php';

$conn = getDBConnection();

// Delete the two specified users by username
$usernames = ['adigu', 'atarve'];

$deleted = [];
$failed  = [];

foreach ($usernames as $uname) {
    // Don't allow deleting the currently logged-in admin
    $stmt = $conn->prepare("DELETE FROM users WHERE username = ? AND role = 'department_head'");
    $stmt->bind_param('s', $uname);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $deleted[] = $uname;
    } else {
        $failed[] = $uname;
    }
    $stmt->close();
}

$conn->close();

// Auto-redirect to admin users page after deletion
header("Location: /EPU health/admin/users.php?msg=" . urlencode(
    count($deleted) > 0
        ? 'Deleted users: ' . implode(', ', $deleted)
        : 'No users deleted.'
));
exit();
?>
