<?php
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../config/Database.php';
$db = (new Database())->connect();
$st = $db->prepare('UPDATE materials SET url = ? WHERE url = ?');
$st->execute(['https://www.w3schools.com', 'https://example.com/study-guide']);
$updated = $st->rowCount();
echo "updated $updated materials\n";
