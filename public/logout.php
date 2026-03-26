<?php
require_once __DIR__ . '/src/controllers/AuthController.php';

AuthController::handleLogout();

header('Location: /login.php');
exit;
?>
