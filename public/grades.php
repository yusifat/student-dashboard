<?php
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../src/utils/SessionManager.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/GradesController.php';

AuthController::requireLogin();

$user = SessionManager::getUser();
$user_id = $user['id'];
$user_role = $user['role'];

// Get data
$grades = GradesController::getStudentGrades($user_id);
$overall_average = GradesController::getOverallAverage($user_id);

// Group grades by course for averages
$grades_by_course = array();
foreach($grades as $grade) {
    $course_id = $grade['course_id'];
    if(!isset($grades_by_course[$course_id])) {
        $grades_by_course[$course_id] = array('course_name' => $grade['course_name'], 'grades' => array());
    }
    $grades_by_course[$course_id]['grades'][] = $grade;
}

// Calculate per-course averages
$course_averages = array();
foreach($grades_by_course as $course_id => $course_data) {
    $course_averages[$course_id] = GradesController::getCourseAverage($user_id, $course_id);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Cijfers - StudyBuddy</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="/dashboard.php" class="sidebar-item" title="Dashboard">📊</a>
            <a href="/grades.php" class="sidebar-item active" title="Mijn Cijfers">📈</a>
            <a href="/tasks.php" class="sidebar-item" title="Deadlines">⏰</a>
            <a href="/materials.php" class="sidebar-item" title="Materialen">📚</a>
            <?php if($user_role === 'admin'): ?>
                <a href="/admin/courses.php" class="sidebar-item" title="Vakken Beheren">⚙️</a>
            <?php endif; ?>
            <a href="/logout.php" class="sidebar-item logout-btn" title="Uitloggen">🚪</a>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <h1 style="font-size: 32px; margin-bottom: 10px;">Mijn Cijfers</h1>
            <p style="color: #999; margin-bottom: 30px;">Overzicht van alle behaalde resultaten</p>
            
            <!-- Overall Average Card -->
            <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; display: inline-block; min-width: 250px;">
                <p style="color: #999; margin-bottom: 8px; font-size: 14px;">ALGEMEEN GEMIDDELDE</p>
                <h2 style="font-size: 48px; color: var(--primary-color); margin: 0;">
                    <?php echo round($overall_average, 2); ?>
                </h2>
            </div>
            
            <!-- Grades by Course -->
            <?php if(empty($grades_by_course)): ?>
                <div style="padding: 40px; text-align: center; background: white; border-radius: 12px;">
                    <p style="color: #999; font-size: 16px;">Je hebt nog geen cijfers ingevoerd.</p>
                </div>
            <?php else: ?>
                <?php foreach($grades_by_course as $course_id => $course_data): ?>
                    <section class="tasks-container">
                        <h2 class="section-title">
                            <?php echo htmlspecialchars($course_data['course_name']); ?>
                            <span style="font-size: 18px; color: var(--primary-color); float: right;">
                                Gemiddelde: <?php echo round($course_averages[$course_id], 2); ?>
                            </span>
                        </h2>
                        
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Beschrijving</th>
                                    <th>Weging</th>
                                    <th>Cijfer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($course_data['grades'] as $grade): ?>
                                    <tr>
                                        <td style="font-size: 13px; color: #999;">
                                            <?php echo htmlspecialchars(substr($grade['created_at'], 0, 10)); ?>
                                        </td>
                                        <td style="color: #666; font-size: 14px;">
                                            Beoordeling
                                        </td>
                                        <td><?php echo htmlspecialchars($grade['weight']); ?>x</td>
                                        <td>
                                            <span class="grade-value <?php echo GradesController::getGradeClass($grade['grade_value']); ?>">
                                                <?php echo round($grade['grade_value'], 2); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
