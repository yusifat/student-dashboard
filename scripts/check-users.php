<?php
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$pdo = $database->connect();

try {
    $stmt = $pdo->query('SELECT id, student_number, email, role FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "users:\n";
    foreach ($users as $u) {
        echo sprintf("%s | %s | %s | %s\n", $u['id'], $u['student_number'], $u['email'], $u['role']);
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
