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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg overflow-y-auto">
            <div class="h-full flex flex-col">
                <div class="flex items-center space-x-2 p-6 border-b border-gray-200">
                    <div class="bg-purple-600 rounded-lg p-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">StudyBuddy</span>
                </div>

                <nav class="flex-1 px-4 py-6">
                    <a href="<?php echo BASE_PATH; ?>/dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg mb-2 transition">
                        <span>📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/admin/courses.php" class="flex items-center space-x-3 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg mb-2 font-medium border-l-4 border-purple-600">
                        <span>⚙️</span>
                        <span>Vakken Beheren</span>
                    </a>
                </nav>

                <div class="border-t border-gray-200 p-4">
                    <a href="<?php echo BASE_PATH; ?>/logout.php" class="w-full flex items-center justify-center space-x-2 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition text-sm font-medium">
                        <span>🚪</span>
                        <span>Uitloggen</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <div class="p-8">
                <a href="<?php echo BASE_PATH; ?>/admin/courses.php" class="text-purple-600 hover:text-purple-700 font-medium flex items-center space-x-1 mb-4">
                    <span>←</span>
                    <span>Terug naar vakken</span>
                </a>

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($course['name']); ?></h1>
                    <p class="text-gray-600 mt-1">Code: <span class="font-semibold"><?php echo htmlspecialchars($course['code']); ?></span></p>
                </div>

                <?php if($error): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                        <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Tasks Section -->
                <section class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">📋 Taken</h2>
                        <button onclick="document.getElementById('taskModal').classList.remove('hidden')"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center space-x-2">
                            <span>+</span>
                            <span>Taak Toevoegen</span>
                        </button>
                    </div>

                    <?php if(empty($tasks)): ?>
                        <div class="bg-white rounded-lg shadow p-8 text-center">
                            <p class="text-gray-600">Nog geen taken voor dit vak aangemaakt.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach($tasks as $task): ?>
                                <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition border-l-4 border-blue-500">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($task['title']); ?></h3>
                                            <div class="flex items-center space-x-2 mt-2">
                                                <span class="text-sm text-gray-600">📅</span>
                                                <span class="text-sm text-gray-600">
                                                    <?php echo date('d-m-Y H:i', strtotime($task['deadline'])); ?>
                                                </span>
                                            </div>
                                            <?php if(!empty($task['description'])): ?>
                                                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($task['description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_task">
                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                            <button type="submit" onclick="return confirm('Zeker weten dat je deze taak wilt verwijderen?')"
                                                    class="text-red-600 hover:text-red-900 text-lg flex-shrink-0">
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
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">📚 Studiemateriaal</h2>
                        <button onclick="document.getElementById('materialModal').classList.remove('hidden')"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition flex items-center space-x-2">
                            <span>+</span>
                            <span>Materiaal Toevoegen</span>
                        </button>
                    </div>

                    <?php if(empty($materials)): ?>
                        <div class="bg-white rounded-lg shadow p-8 text-center">
                            <p class="text-gray-600">Nog geen materiaal voor dit vak toegevoegd.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach($materials as $material): ?>
                                <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition border-l-4 border-green-500">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 break-words"><?php echo htmlspecialchars($material['title']); ?></h3>
                                            <a href="<?php echo htmlspecialchars($material['url']); ?>" target="_blank" rel="noopener noreferrer"
                                               class="text-sm text-purple-600 hover:text-purple-700 mt-2 break-all line-clamp-1">
                                                🔗 <?php echo htmlspecialchars(substr($material['url'], 0, 60)); ?>...
                                            </a>
                                            <span class="text-xs text-gray-500 mt-2 inline-block">
                                                Type: <?php echo htmlspecialchars($material['file_type'] ?? 'link'); ?>
                                            </span>
                                        </div>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_material">
                                            <input type="hidden" name="material_id" value="<?php echo $material['id']; ?>">
                                            <button type="submit" onclick="return confirm('Zeker weten dat je dit materiaal wilt verwijderen?')"
                                                    class="text-red-600 hover:text-red-900 text-lg flex-shrink-0">
                                                ✕
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>

    <!-- Task Modal -->
    <div id="taskModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Nieuwe Taak</h2>
                <button onclick="document.getElementById('taskModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
            </div>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add_task">

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Titel <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                        Deadline <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="deadline" name="deadline" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Beschrijving (optioneel)
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Taak Toevoegen
                    </button>
                    <button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition">
                        Annuleren
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Material Modal -->
    <div id="materialModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Nieuw Materiaal</h2>
                <button onclick="document.getElementById('materialModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
            </div>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add_material">

                <div>
                    <label for="mat_title" class="block text-sm font-medium text-gray-700 mb-1">
                        Titel <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="mat_title" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-1">
                        URL <span class="text-red-500">*</span>
                    </label>
                    <input type="url" id="url" name="url" required
                           placeholder="https://example.com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label for="file_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Type
                    </label>
                    <select id="file_type" name="file_type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="link">Link</option>
                        <option value="pdf">PDF</option>
                        <option value="video">Video</option>
                        <option value="audio">Audio</option>
                    </select>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Materiaal Toevoegen
                    </button>
                    <button type="button" onclick="document.getElementById('materialModal').classList.add('hidden')"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition">
                        Annuleren
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Close modals when clicking outside
        document.getElementById('taskModal').addEventListener('click', function(e) {
            if(e.target === this) {
                this.classList.add('hidden');
            }
        });
        document.getElementById('materialModal').addEventListener('click', function(e) {
            if(e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
