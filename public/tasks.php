<?php
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../src/utils/SessionManager.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/CoursesController.php';
require_once __DIR__ . '/../src/models/Task.php';

AuthController::requireLogin();

$user = SessionManager::getUser();
$user_id = $user['id'];
$user_role = $user['role'];

// Handle task submission by student
$error = '';
$success = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_task') {
    $task_id = (int)($_POST['task_id'] ?? 0);
    if($task_id <= 0) {
        $error = 'Ongeldige taak geselecteerd';
    } else {
        $task_obj = new Task();

        // Handle file upload if provided
        $uploaded_file = null;
        if(isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/submissions/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_name = basename($_FILES['submission_file']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip'];

            if(!in_array($file_ext, $allowed_exts)) {
                $error = 'Bestandstype niet toegestaan. Alleen: ' . implode(', ', $allowed_exts);
            } elseif($_FILES['submission_file']['size'] > 10 * 1024 * 1024) { // 10MB limit
                $error = 'Bestand te groot. Maximum 10MB.';
            } else {
                $new_file_name = 'task_' . $task_id . '_user_' . $user_id . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;

                if(move_uploaded_file($_FILES['submission_file']['tmp_name'], $upload_path)) {
                    $uploaded_file = 'uploads/submissions/' . $new_file_name;
                } else {
                    $error = 'Kon bestand niet uploaden.';
                }
            }
        }

        if(empty($error)) {
            if($task_obj->submitTask($task_id, $user_id, $uploaded_file)) {
                $success = 'Taak succesvol ingeleverd' . ($uploaded_file ? ' met bijlage' : '');
            } else {
                $error = 'Kon taak niet inleveren. Probeer opnieuw.';
            }
        }
    }
}

// Get tasks
$task_obj = new Task();
$tasks = $task_obj->getTasksByStudent($user_id);
$critical_tasks = $task_obj->getCriticalTasks($user_id);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deadlines - StudyBuddy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg">
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
                    <a href="<?php echo BASE_PATH; ?>/grades.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg mb-2 transition">
                        <span>📈</span>
                        <span>Mijn Cijfers</span>
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/tasks.php" class="flex items-center space-x-3 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg mb-2 font-medium border-l-4 border-purple-600">
                        <span>⏰</span>
                        <span>Deadlines</span>
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/materials.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg mb-2 transition">
                        <span>📚</span>
                        <span>Materialen</span>
                    </a>
                    
                    <?php if($user_role === 'admin'): ?>
                        <div class="border-t border-gray-200 my-4 pt-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase px-4 mb-2">Docent Functies</p>
                            <a href="<?php echo BASE_PATH; ?>/admin/courses.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg mb-2 transition">
                                <span>⚙️</span>
                                <span>Vakken Beheren</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>

                <div class="border-t border-gray-200 p-4">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold"><?php echo substr($user['full_name'], 0, 1); ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($user['full_name']); ?></p>
                            <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($user['student_number']); ?></p>
                        </div>
                    </div>
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
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">⏰ Deadlines</h1>
                    <p class="text-gray-600 mt-2">Alle taken gesorteerd op urgentie</p>
                </div>

                <?php if($error): ?>
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                        <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                        <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Critical Tasks Alert -->
                <?php if(!empty($critical_tasks)): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Je hebt <?php echo count($critical_tasks); ?> kritieke deadline(s)!
                                </h3>
                                <p class="text-xs text-red-700 mt-1">Minder dan 2 dagen te gaan.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- All Tasks -->
                <?php if(empty($tasks)): ?>
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Geen taken</h3>
                        <p class="mt-1 text-gray-600">Je hebt geen openstaande taken!</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach($tasks as $task): ?>
                            <?php
                                $urgency = $task_obj->getUrgency($task['deadline'], $task['status']);
                                $class = '';
                                $icon = '';
                                if($urgency === 'critical') {
                                    $class = 'border-l-4 border-red-500 bg-red-50';
                                    $icon = '🔴';
                                } elseif($urgency === 'completed') {
                                    $class = 'border-l-4 border-green-500 opacity-60';
                                    $icon = '✅';
                                } else {
                                    $class = 'border-l-4 border-blue-500 bg-blue-50';
                                    $icon = '📅';
                                }
                            ?>
                            <div class="bg-white rounded-lg shadow hover:shadow-md transition p-4 <?php echo $class; ?>">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span class="text-xl"><?php echo $icon; ?></span>
                                            <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($task['title']); ?></h3>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($task['course_name']); ?></p>
                                        <p class="text-sm font-medium text-gray-700">
                                            📅 <?php echo date('d-m-Y H:i', strtotime($task['deadline'])); ?>
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <?php if($task['status'] === 'completed'): ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Voltooid
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                Openstaand
                                            </span>
                                            <form method="POST" enctype="multipart/form-data" class="mt-2 space-y-2">
                                                <input type="hidden" name="action" value="submit_task">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <div>
                                                    <label for="file_<?php echo $task['id']; ?>" class="block text-xs text-gray-600 mb-1">Bijlage (optioneel)</label>
                                                    <input type="file" id="file_<?php echo $task['id']; ?>" name="submission_file" 
                                                           accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip" 
                                                           class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                </div>
                                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white rounded-md text-xs py-1 transition">Inleveren</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
