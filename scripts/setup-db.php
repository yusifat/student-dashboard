<?php
/**
 * Setup script voor StudyBuddy (eenmalig draaien)
 *
 * Gebruik:
 * php scripts/setup-db.php
 * of via browser: http://localhost/student-dashboard/scripts/setup-db.php
 */

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$pdo = $database->connect();

// Importeer schema
$schemaPath = __DIR__ . '/../database/schema.sql';
if (!file_exists($schemaPath)) {
    die('Fout: schema.sql niet gevonden in database/schema.sql');
}

$schemaSql = file_get_contents($schemaPath);
$pdo->exec($schemaSql);

// Helperfunctie for safe insert via EXISTS check
function upsertUser($pdo, $email, $student_number, $full_name, $password, $role = 'student') {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO users (student_number, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)');
    $insert->execute([$student_number, $email, $hash, $full_name, $role]);
}

upsertUser($pdo, 'admin@localhost', '0000000000', 'Admin User', 'admin123', 'admin');
upsertUser($pdo, 'student@localhost', '1111111111', 'Student User', 'student123', 'student');

// Standaard vak/les
$courseId = null;
$courseCheck = $pdo->prepare('SELECT id FROM courses WHERE code = ?');
$courseCheck->execute(['IT101']);
$course = $courseCheck->fetch();
if ($course) {
    $courseId = $course['id'];
} else {
    // admin user id
    $adminStmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $adminStmt->execute(['admin@localhost']);
    $adminId = $adminStmt->fetchColumn();

    $insertCourse = $pdo->prepare('INSERT INTO courses (code, name, description, admin_id) VALUES (?, ?, ?, ?)');
    $insertCourse->execute(['IT101', 'Introductie Programmeer', 'Basis programmeerconcepten', $adminId]);
    $courseId = $pdo->lastInsertId();
}

// Rooster student inschrijving
$studentStmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$studentStmt->execute(['student@localhost']);
$studentId = $studentStmt->fetchColumn();

$enrollStmt = $pdo->prepare('INSERT IGNORE INTO student_courses (student_id, course_id) VALUES (?, ?)');
$enrollStmt->execute([$studentId, $courseId]);

// Voeg voorbeeldtaken toe (alleen als nog niet bestaat)
$taskCheck = $pdo->prepare('SELECT id FROM tasks WHERE title = ? AND course_id = ?');
$taskCheck->execute(['Eerste opdracht', $courseId]);
if (!$taskCheck->fetch()) {
    $insertTask = $pdo->prepare('INSERT INTO tasks (course_id, title, description, deadline) VALUES (?, ?, ?, ?)');
    $insertTask->execute([$courseId, 'Eerste opdracht', 'Maak de eerste programmeeropdracht af.', date('Y-m-d H:i:s', strtotime('+1 week'))]);
}

// Voeg voorbeeldcijfer toe (als nog niet bestaat)
$gradeCheck = $pdo->prepare('SELECT id FROM grades WHERE student_id = ? AND course_id = ?');
$gradeCheck->execute([$studentId, $courseId]);
if (!$gradeCheck->fetch()) {
    $insertGrade = $pdo->prepare('INSERT INTO grades (student_id, course_id, grade_value, weight) VALUES (?, ?, ?, ?)');
    $insertGrade->execute([$studentId, $courseId, 7.5, 1]);
}

// Voorbeeldmateriaal
$materialCheck = $pdo->prepare('SELECT id FROM materials WHERE course_id = ? AND title = ?');
$materialCheck->execute([$courseId, 'Leerboek link']);
if (!$materialCheck->fetch()) {
    $insertMaterial = $pdo->prepare('INSERT INTO materials (course_id, title, url, file_type) VALUES (?, ?, ?, ?)');
    $insertMaterial->execute([$courseId, 'Leerboek link', 'https://example.com/study-guide', 'link']);
}

echo "Setup voltooid.\n";
echo "Login admin: admin@localhost / admin123\n";
echo "Login student: student@localhost / student123\n";
