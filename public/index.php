<?php
session_start();

// Get base path
$base_path = rtrim(dirname($_SERVER['PHP_SELF']), '/');

// Redirect logica
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // User is logged in
    if($_SESSION['role'] === 'admin') {
        header('Location: ' . $base_path . '/admin/courses.php');
    } else {
        header('Location: ' . $base_path . '/dashboard.php');
    }
    exit;
} else {
    // User not logged in
    header('Location: ' . $base_path . '/login.php');
    exit;
}
?>
