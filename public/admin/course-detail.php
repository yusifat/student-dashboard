<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../src/utils/SessionManager.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/models/Course.php';
require_once __DIR__ . '/../src/models/Task.php';
require_once __DIR__ . '/../src/models/Material.php';

AuthController::requireLogin();
AuthController::requireAdmin();

$user = SessionManager::getUser();
$admin_id = $user['id'];

// Get course
$course_id = (int)($_GET['id'] ?? 0);
$course_obj = new Course();
$course = $course_obj->getCourseById($course_id);

if(!$course || $course['admin_id'] != $admin_id) {
    header('Location: /admin/courses.php');
    exit;
}

$error = '';
$success = '';

// Handle task creation
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_task') {
    if(empty($_POST['title']) || empty($_POST['deadline'])) {
        $error = 'Titel en deadline zijn verplicht';
    } else {
        $task = new Task();
        $task->course_id = $course_id;
        $task->title = $_POST['title'];
        $task->description = $_POST['description'] ?? '';
        $task->deadline = $_POST['deadline'];
        
        if($task->createTask()) {
            $success = 'Taak succesvol aangemaakt';
        } else {
            $error = 'Fout bij aanmaken taak';
        }
    }
}

// Handle material creation
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_material') {
    if(empty($_POST['title']) || empty($_POST['url'])) {
        $error = 'Titel en URL zijn verplicht';
    } else {
        $material = new Material();
        $material->course_id = $course_id;
        $material->title = $_POST['title'];
        $material->url = $_POST['url'];
        $material->file_type = $_POST['file_type'] ?? 'link';
        
        if($material->addMaterial()) {
            $success = 'Materiaal succesvol toegevoegd';
        } else {
            $error = 'Fout bij toevoegen materiaal';
        }
    }
}

// Handle task deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_task') {
    $task_id = (int)$_POST['task_id'];
    $task_obj = new Task();
    if($task_obj->deleteTask($task_id)) {
        $success = 'Taak verwijderd';
    } else {
        $error = 'Fout bij verwijderen taak';
    }
}

// Handle material deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_material') {
    $material_id = (int)$_POST['material_id'];
    $material_obj = new Material();
    if($material_obj->deleteMaterial($material_id)) {
        $success = 'Materiaal verwijderd';
    } else {
        $error = 'Fout bij verwijderen materiaal';
    }
}

// Get tasks and materials
$task_obj = new Task();
$tasks = $task_obj->getTasksByCourse($course_id);

$material_obj = new Material();
$materials = $material_obj->getMaterialByCourse($course_id);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vak: <?php echo htmlspecialchars($course['name']); ?> - StudyBuddy</title>
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="/dashboard.php" class="sidebar-item" title="Dashboard">📊</a>
            <a href="/admin/courses.php" class="sidebar-item active" title="Vakken Beheren">⚙️</a>
            <a href="/logout.php" class="sidebar-item logout-btn" title="Uitloggen">🚪</a>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <a href="/admin/courses.php" style="color: var(--primary-color); margin-bottom: 20px; text-decoration: none;">← Terug naar vakken</a>
            
            <h1 style="font-size: 32px; margin-bottom: 10px;"><?php echo htmlspecialchars($course['name']); ?></h1>
            <p style="color: #999; margin-bottom: 30px;">Code: <?php echo htmlspecialchars($course['code']); ?></p>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <!-- Tasks Section -->
            <section class="tasks-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="section-title" style="margin: 0; padding: 0; border: none;">📋 Taken</h2>
                    <button class="btn btn-primary" onclick="document.getElementById('taskModal').classList.add('active')">
                        + Taak Toevoegen
                    </button>
                </div>
                
                <?php if(empty($tasks)): ?>
                    <p style="color: #999;">Nog geen taken voor dit vak aangemaakt.</p>
                <?php else: ?>
                    <div style="display: grid; gap: 10px;">
                        <?php foreach($tasks as $task): ?>
                            <div style="padding: 15px; border: 1px solid var(--border-color); border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <div>
                                        <h4 style="margin: 0 0 5px 0;"><?php echo htmlspecialchars($task['title']); ?></h4>
                                        <small style="color: #999;">
                                            📅 <?php echo date('d-m-Y H:i', strtotime($task['deadline'])); ?>
                                        </small>
                                    </div>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_task">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" onclick="return confirm('Verwijderen?')" 
                                                style="background: var(--danger-color); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Materials Section -->
            <section class="tasks-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="section-title" style="margin: 0; padding: 0; border: none;">📚 Studiemateriaal</h2>
                    <button class="btn btn-primary" onclick="document.getElementById('materialModal').classList.add('active')">
                        + Materiaal Toevoegen
                    </button>
                </div>
                
                <?php if(empty($materials)): ?>
                    <p style="color: #999;">Nog geen materiaal voor dit vak toegevoegd.</p>
                <?php else: ?>
                    <div style="display: grid; gap: 10px;">
                        <?php foreach($materials as $material): ?>
                            <div style="padding: 15px; border: 1px solid var(--border-color); border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 5px 0;"><?php echo htmlspecialchars($material['title']); ?></h4>
                                        <small style="color: #999; word-break: break-all;">
                                            🔗 <?php echo htmlspecialchars($material['url']); ?>
                                        </small>
                                    </div>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_material">
                                        <input type="hidden" name="material_id" value="<?php echo $material['id']; ?>">
                                        <button type="submit" onclick="return confirm('Verwijderen?')" 
                                                style="background: var(--danger-color); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    
    <!-- Task Modal -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('taskModal').classList.remove('active')">&times;</span>
            
            <h2 style="margin-top: 0;">Nieuwe Taak Toevoegen</h2>
            
            <form method="POST">
                <input type="hidden" name="action" value="add_task">
                
                <div class="form-group">
                    <label for="title">Titel</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="deadline">Deadline</label>
                    <input type="datetime-local" id="deadline" name="deadline" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Beschrijving (optioneel)</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Taak Toevoegen</button>
            </form>
        </div>
    </div>
    
    <!-- Material Modal -->
    <div id="materialModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('materialModal').classList.remove('active')">&times;</span>
            
            <h2 style="margin-top: 0;">Nieuw Materiaal Toevoegen</h2>
            
            <form method="POST">
                <input type="hidden" name="action" value="add_material">
                
                <div class="form-group">
                    <label for="mat_title">Titel</label>
                    <input type="text" id="mat_title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="url">URL</label>
                    <input type="url" id="url" name="url" required 
                           placeholder="https://example.com">
                </div>
                
                <div class="form-group">
                    <label for="file_type">Type</label>
                    <select id="file_type" name="file_type">
                        <option value="link">Link</option>
                        <option value="pdf">PDF</option>
                        <option value="video">Video</option>
                        <option value="audio">Audio</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Materiaal Toevoegen</button>
            </form>
        </div>
    </div>
    
    <script>
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if(e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
