<?php
session_start();

// Redirect logica
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // User is logged in
    if($_SESSION['role'] === 'admin') {
        header('Location: /admin/courses.php');
    } else {
        header('Location: /dashboard.php');
    }
    exit;
} else {
    // User not logged in
    header('Location: /login.php');
    exit;
}
?>
