<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Route each role to their own landing page
    $roleHome = [
        'admin'           => 'dashboard.php',
        'doctor'          => 'dashboard.php',
        'nurse'           => 'dashboard.php',
        'receptionist'    => 'dashboard.php',
        'traige'          => 'dashboard.php',
        'pharmacist'      => 'dashboard.php',
        'lab_technician'  => 'dashboard.php',
        'department_head' => 'dashboard.php',
    ];
    $home = $roleHome[$_SESSION['role']] ?? 'dashboard.php';
    header("Location: $home");
    exit();
}
header("Location: login.php");
exit();
