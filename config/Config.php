<?php
/**
 * Application Configuration
 */

// Detecteer base path automatisch
$script_path = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_PATH', rtrim($script_path, '/'));

// Database info
define('DB_HOST', 'localhost');
define('DB_NAME', 'study_buddy');
define('DB_USER', 'root');
define('DB_PASS', '');
?>
