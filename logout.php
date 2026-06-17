<?php
session_start();
require_once 'includes/auth.php';
logAction('Logout', 'auth');
session_destroy();
header("Location: login.php");
exit();
?>
