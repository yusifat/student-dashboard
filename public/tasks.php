<?php
require_once __DIR__ . '/src/utils/SessionManager.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/TasksController.php';

AuthController::requireLogin();

$user = SessionManager::getUser();
$user_id = $user['id'];
$user_role = $user['role'];

// Get tasks
$all_tasks = TasksController::getStudentTasks($user_id);
$critical_tasks = TasksController::getCriticalTasks($user_id);

// Group by status
$pending_tasks = array();
$completed_tasks = array();

foreach($all_tasks as $task) {
    if($task['status'] === 'completed') {
        $completed_tasks[] = $task;
    } else {
        $pending_tasks[] = $task;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deadlines - StudyBuddy</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="/dashboard.php" class="sidebar-item" title="Dashboard">📊</a>
            <a href="/grades.php" class="sidebar-item" title="Mijn Cijfers">📈</a>
            <a href="/tasks.php" class="sidebar-item active" title="Deadlines">⏰</a>
            <a href="/materials.php" class="sidebar-item" title="Materialen">📚</a>
            <?php if($user_role === 'admin'): ?>
                <a href="/admin/courses.php" class="sidebar-item" title="Vakken Beheren">⚙️</a>
            <?php endif; ?>
            <a href="/logout.php" class="sidebar-item logout-btn" title="Uitloggen">🚪</a>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <h1 style="font-size: 32px; margin-bottom: 10px;">Mijn Deadlines</h1>
            <p style="color: #999; margin-bottom: 30px;">Alle taken en deadlines in één overzicht</p>
            
            <!-- Critical Tasks Alert -->
            <?php if(!empty($critical_tasks)): ?>
                <div style="background-color: #FFE5E5; border-left: 4px solid var(--danger-color); padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                    <p style="color: var(--danger-color); font-weight: 600; margin: 0;">
                        ⚠️ Je hebt <?php echo count($critical_tasks); ?> kritieke taak(en) - deadline binnen 2 dagen!
                    </p>
                </div>
            <?php endif; ?>
            
            <!-- Pending Tasks -->
            <?php if(!empty($pending_tasks)): ?>
                <section class="tasks-container">
                    <h2 class="section-title">⏳ Openstaande Taken</h2>
                    
                    <?php foreach($pending_tasks as $task): 
                        $urgency = TasksController::getUrgencyClass($task['deadline'], $task['status']);
                        $is_critical = ($urgency === 'critical');
                    ?>
                        <div class="task-item">
                            <div class="task-status <?php echo $urgency; ?>"></div>
                            <div class="task-info" style="flex: 1;">
                                <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                                <div class="task-meta">
                                    <?php echo htmlspecialchars($task['course_name']); ?>
                                </div>
                            </div>
                            <div class="task-deadline <?php if($is_critical) echo 'critical'; ?>">
                                <?php if($is_critical): ?>
                                    🔴 <?php echo TasksController::formatDeadline($task['deadline']); ?>
                                <?php else: ?>
                                    📅 <?php echo TasksController::formatDeadline($task['deadline']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
            
            <!-- Completed Tasks -->
            <?php if(!empty($completed_tasks)): ?>
                <section class="tasks-container">
                    <h2 class="section-title">✅ Voltooide Taken</h2>
                    
                    <?php foreach($completed_tasks as $task): ?>
                        <div class="task-item">
                            <div class="task-status completed"></div>
                            <div class="task-info" style="flex: 1;">
                                <div class="task-title" style="text-decoration: line-through; color: var(--success-color);">
                                    <?php echo htmlspecialchars($task['title']); ?>
                                </div>
                                <div class="task-meta">
                                    <?php echo htmlspecialchars($task['course_name']); ?>
                                </div>
                            </div>
                            <div class="task-deadline completed">
                                ✓ <?php echo TasksController::formatDeadline($task['deadline']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
            
            <!-- No Tasks -->
            <?php if(empty($all_tasks)): ?>
                <div style="padding: 40px; text-align: center; background: white; border-radius: 12px;">
                    <p style="color: #999; font-size: 16px;">Je hebt geen taken. Alles ziet er rustig uit! 🌟</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
