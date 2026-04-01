<?php
/**
 * Application Configuration
 */

// Detecteer base path automatisch (altijd tot public root)
$script_path = dirname($_SERVER['SCRIPT_NAME']);

if (basename($script_path) === 'admin') {
    $script_path = dirname($script_path);
}

define('BASE_PATH', rtrim($script_path, '/'));

// Database info
define('DB_HOST', 'localhost');
define('DB_NAME', 'study_buddy');
define('DB_USER', 'root');
define('DB_PASS', '');
?>
