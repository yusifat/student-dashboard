<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../src/utils/SessionManager.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/CoursesController.php';
require_once __DIR__ . '/../src/models/Course.php';

AuthController::requireLogin();
AuthController::requireAdmin();

$user = SessionManager::getUser();
$admin_id = $user['id'];

$error = '';
$success = '';

// Handle delete
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $course_id = (int)$_POST['course_id'];
    $course = new Course();
    if($course->delete($course_id)) {
        $success = 'Vak succesvol verwijderd';
    } else {
        $error = 'Fout bij verwijderen vak';
    }
}

// Handle create
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $result = CoursesController::createCourse($admin_id, $_POST);
    if($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['error'];
    }
}

// Get admin courses
$courses = CoursesController::getAdminCourses($admin_id);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vakken Beheren - StudyBuddy</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .close-btn {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #999;
            line-height: 20px;
        }
        .close-btn:hover {
            color: var(--text-color);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="/dashboard.php" class="sidebar-item" title="Dashboard">📊</a>
            <a href="/grades.php" class="sidebar-item" title="Mijn Cijfers">📈</a>
            <a href="/tasks.php" class="sidebar-item" title="Deadlines">⏰</a>
            <a href="/materials.php" class="sidebar-item" title="Materialen">📚</a>
            <a href="/admin/courses.php" class="sidebar-item active" title="Vakken Beheren">⚙️</a>
            <a href="/logout.php" class="sidebar-item logout-btn" title="Uitloggen">🚪</a>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 32px;">Vakken Beheren</h1>
                <button class="btn btn-primary" onclick="document.getElementById('newCourseModal').classList.add('active')">
                    + Nieuw Vak
                </button>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <!-- Create Modal -->
            <div id="newCourseModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="document.getElementById('newCourseModal').classList.remove('active')">&times;</span>
                    
                    <h2 style="margin-top: 0;">Nieuw Vak Toevoegen</h2>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" id="code" name="code" required 
                                   placeholder="bijv. PHP-101" pattern="[A-Z0-9-]{3,20}">
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Naam</label>
                            <input type="text" id="name" name="name" required 
                                   placeholder="bijv. PHP Programmeren">
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Beschrijving</label>
                            <textarea id="description" name="description" rows="4"
                                      placeholder="Korte omschrijving van het vak..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Vak Aanmaken</button>
                    </form>
                </div>
            </div>
            
            <!-- Courses Table -->
            <?php if(empty($courses)): ?>
                <div style="padding: 40px; text-align: center; background: white; border-radius: 12px;">
                    <p style="color: #999; font-size: 16px;">Je hebt nog geen vakken aangemaakt.</p>
                </div>
            <?php else: ?>
                <div class="tasks-container">
                    <table class="grades-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Naam</th>
                                <th>Studenten</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($courses as $course): 
                                $course_obj = new Course();
                                $student_count = $course_obj->getStudentCount($course['id']);
                            ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--primary-color);">
                                        <?php echo htmlspecialchars($course['code']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($course['name']); ?></td>
                                    <td><?php echo $student_count; ?> student(en)</td>
                                    <td>
                                        <a href="/admin/course-detail.php?id=<?php echo $course['id']; ?>" 
                                           class="btn" style="padding: 8px 12px; font-size: 12px; background: var(--primary-color); text-decoration: none; border-radius: 4px; display: inline-block;">
                                            Beheren
                                        </a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <button type="submit" onclick="return confirm('Zeker weten?')" 
                                                    class="btn" style="padding: 8px 12px; font-size: 12px; background: var(--danger-color); color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">
                                                Verwijderen
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
        // Close modal when clicking outside
        document.getElementById('newCourseModal').addEventListener('click', function(e) {
            if(e.target === this) {
                this.classList.remove('active');
            }
        });
    </script>
</body>
</html>
