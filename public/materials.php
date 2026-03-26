<?php
require_once __DIR__ . '/src/utils/SessionManager.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/CoursesController.php';
require_once __DIR__ . '/src/models/Material.php';

AuthController::requireLogin();

$user = SessionManager::getUser();
$user_id = $user['id'];
$user_role = $user['role'];

// Get student courses
$courses = CoursesController::getStudentCourses($user_id);

// Get materials for each course
$materials_by_course = array();
foreach($courses as $course) {
    $material = new Material();
    $materials = $material->getMaterialByCourse($course['id']);
    
    if(!empty($materials)) {
        $materials_by_course[$course['id']] = array(
            'course_name' => $course['name'],
            'materials' => $materials
        );
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studiemateriaal - StudyBuddy</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="/dashboard.php" class="sidebar-item" title="Dashboard">📊</a>
            <a href="/grades.php" class="sidebar-item" title="Mijn Cijfers">📈</a>
            <a href="/tasks.php" class="sidebar-item" title="Deadlines">⏰</a>
            <a href="/materials.php" class="sidebar-item active" title="Materialen">📚</a>
            <?php if($user_role === 'admin'): ?>
                <a href="/admin/courses.php" class="sidebar-item" title="Vakken Beheren">⚙️</a>
            <?php endif; ?>
            <a href="/logout.php" class="sidebar-item logout-btn" title="Uitloggen">🚪</a>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <h1 style="font-size: 32px; margin-bottom: 10px;">Studiemateriaal</h1>
            <p style="color: #999; margin-bottom: 30px;">Alle links en documenten per vak</p>
            
            <?php if(empty($materials_by_course)): ?>
                <div style="padding: 40px; text-align: center; background: white; border-radius: 12px;">
                    <p style="color: #999; font-size: 16px;">Voor je vakken is nog geen studiemateriaal toegevoegd.</p>
                </div>
            <?php else: ?>
                <?php foreach($materials_by_course as $course_id => $course_data): ?>
                    <section class="tasks-container">
                        <h2 class="section-title">📖 <?php echo htmlspecialchars($course_data['course_name']); ?></h2>
                        
                        <div style="display: grid; gap: 15px;">
                            <?php foreach($course_data['materials'] as $material): ?>
                                <a href="<?php echo htmlspecialchars($material['url']); ?>" target="_blank" 
                                   style="padding: 15px; border-radius: 8px; background: white; border: 1px solid var(--border-color); text-decoration: none; color: inherit; transition: all 0.3s ease; display: block;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="font-size: 28px;">
                                            <?php 
                                                $file_type = strtolower($material['file_type'] ?? 'url');
                                                if(strpos($file_type, 'pdf') !== false) echo '📄';
                                                elseif(strpos($file_type, 'video') !== false) echo '🎥';
                                                elseif(strpos($file_type, 'audio') !== false) echo '🎵';
                                                else echo '🔗';
                                            ?>
                                        </div>
                                        <div style="flex: 1;">
                                            <h3 style="margin: 0 0 5px 0; font-size: 16px;">
                                                <?php echo htmlspecialchars($material['title']); ?>
                                            </h3>
                                            <small style="color: #999; word-break: break-all;">
                                                <?php echo htmlspecialchars(substr($material['url'], 0, 50)); ?>...
                                            </small>
                                        </div>
                                        <span style="color: var(--primary-color); font-size: 18px;">→</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
