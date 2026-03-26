<?php
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../src/utils/SessionManager.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/CoursesController.php';

// Check authenticatie
AuthController::requireLogin();

$user = SessionManager::getUser();
$user_id = $user['id'];
$user_role = $user['role'];

// Get courses
$courses = CoursesController::getStudentCourses($user_id);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StudyBuddy</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="/dashboard.php" class="sidebar-item active" title="Dashboard">📊</a>
            <a href="/grades.php" class="sidebar-item" title="Mijn Cijfers">📈</a>
            <a href="/tasks.php" class="sidebar-item" title="Deadlines">⏰</a>
            <a href="/materials.php" class="sidebar-item" title="Materialen">📚</a>
            <?php if($user_role === 'admin'): ?>
                <a href="/admin/courses.php" class="sidebar-item" title="Vakken Beheren">⚙️</a>
            <?php endif; ?>
            <a href="/logout.php" class="sidebar-item logout-btn" title="Uitloggen">🚪</a>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 32px;">Welkom, <?php echo htmlspecialchars($user['full_name']); ?></h1>
                <small style="color: #999;"><?php echo htmlspecialchars($user['student_number']); ?></small>
            </div>
            
            <!-- Courses Section -->
            <section>
                <h2 class="section-title">Mijn Vakken</h2>
                
                <?php if(empty($courses)): ?>
                    <div style="padding: 40px; text-align: center; background: white; border-radius: 12px;">
                        <p style="color: #999; font-size: 16px;">Je bent nog ingeschreven voor geen vakken.</p>
                    </div>
                <?php else: ?>
                    <div class="courses-grid">
                        <?php foreach($courses as $course): ?>
                            <div class="course-card">
                                <div class="course-code"><?php echo htmlspecialchars($course['code']); ?></div>
                                <div class="course-name"><?php echo htmlspecialchars($course['name']); ?></div>
                                <div class="course-students">
                                    📍 <?php echo isset($course['description']) ? htmlspecialchars(substr($course['description'], 0, 50)) : 'Geen omschrijving'; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
