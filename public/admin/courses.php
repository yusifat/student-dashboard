<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../src/utils/SessionManager.php';
require_once __DIR__ . '/../../src/controllers/AuthController.php';
require_once __DIR__ . '/../../src/controllers/CoursesController.php';
require_once __DIR__ . '/../../src/models/Course.php';

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
                    <a href="<?php echo BASE_PATH; ?>/tasks.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg mb-2 transition">
                        <span>⏰</span>
                        <span>Deadlines</span>
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/materials.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg mb-2 transition">
                        <span>📚</span>
                        <span>Materialen</span>
                    </a>
                    
                    <div class="border-t border-gray-200 my-4 pt-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase px-4 mb-2">Docent Functies</p>
                        <a href="<?php echo BASE_PATH; ?>/admin/courses.php" class="flex items-center space-x-3 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg mb-2 font-medium border-l-4 border-purple-600">
                            <span>⚙️</span>
                            <span>Vakken Beheren</span>
                        </a>
                    </div>
                </nav>

                <div class="border-t border-gray-200 p-4">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold"><?php echo substr($user['full_name'], 0, 1); ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($user['full_name']); ?></p>
                            <p class="text-xs text-gray-500">Docent</p>
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
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">⚙️ Vakken Beheren</h1>
                        <p class="text-gray-600 mt-2">Maak en wijzig je vakken, beheer studenten</p>
                    </div>
                    <button onclick="document.getElementById('newCourseModal').classList.remove('hidden')"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-6 rounded-lg transition flex items-center space-x-2">
                        <span>+</span>
                        <span>Nieuw Vak</span>
                    </button>
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

                <!-- Modal -->
                <div id="newCourseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Nieuw Vak Toevoegen</h2>
                            <button onclick="document.getElementById('newCourseModal').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
                        </div>

                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="create">

                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                    Vak Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="code" name="code" required
                                       placeholder="bijv. PHP-101"
                                       pattern="[A-Z0-9-]{3,20}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Naam <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" required
                                       placeholder="bijv. PHP Programmeren"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Beschrijving
                                </label>
                                <textarea id="description" name="description" rows="3"
                                          placeholder="Korte omschrijving van het vak..."
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"></textarea>
                            </div>

                            <div class="flex space-x-3 pt-4">
                                <button type="submit"
                                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                    Vak Aanmaken
                                </button>
                                <button type="button" onclick="document.getElementById('newCourseModal').classList.add('hidden')"
                                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition">
                                    Annuleren
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Courses Table -->
                <?php if(empty($courses)): ?>
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Geen vakken</h3>
                        <p class="mt-1 text-gray-600">Je hebt nog geen vakken aangemaakt.</p>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Studenten</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach($courses as $course):
                                    $course_obj = new Course();
                                    $student_count = $course_obj->getStudentCount($course['id']);
                                ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-purple-600">
                                            <?php echo htmlspecialchars($course['code']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($course['name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo $student_count; ?> student(en)
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="<?php echo BASE_PATH; ?>/admin/course-detail.php?id=<?php echo $course['id']; ?>"
                                               class="text-purple-600 hover:text-purple-900 font-medium">
                                                Beheren
                                            </a>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" onclick="return confirm('Zeker weten dat je dit vak wilt verwijderen?')"
                                                        class="text-red-600 hover:text-red-900 font-medium">
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
            </div>
        </main>
    </div>

    <script>
        // Close modal when clicking outside
        document.getElementById('newCourseModal').addEventListener('click', function(e) {
            if(e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
