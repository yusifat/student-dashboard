<?php
// Redirect vanuit admin-folder naar normale logout
require_once __DIR__ . '/../config/Config.php';
header('Location: ' . BASE_PATH . '/logout.php');
exit;
