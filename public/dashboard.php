<?php
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../src/utils/SessionManager.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/CoursesController.php';

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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg">
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <div class="flex items-center space-x-2 p-6 border-b border-gray-200">
                    <div class="bg-purple-600 rounded-lg p-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">StudyBuddy</span>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6">
                    <a href="<?php echo BASE_PATH; ?>/dashboard.php" class="flex items-center space-x-3 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg mb-2 font-medium border-l-4 border-purple-600">
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

                <!-- User Profile & Logout -->
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
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Welkom, <?php echo htmlspecialchars($user['full_name']); ?> 👋</h1>
                    <p class="text-gray-600 mt-2">Hier is jouw persoonlijke studie-overzicht</p>
                </div>

                <!-- Courses Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Mijn Vakken</h2>
                    
                    <?php if(empty($courses)): ?>
                        <div class="bg-white rounded-lg shadow p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Geen vakken</h3>
                            <p class="mt-1 text-gray-600">Je bent nog ingeschreven voor geen vakken.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach($courses as $course): ?>
                                <div class="bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer overflow-hidden border-l-4 border-purple-500">
                                    <div class="p-6">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider"><?php echo htmlspecialchars($course['code']); ?></p>
                                                <h3 class="text-lg font-semibold text-gray-900 mt-1"><?php echo htmlspecialchars($course['name']); ?></h3>
                                            </div>
                                            <div class="text-2xl">📚</div>
                                        </div>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($course['description'] ?? 'Geen omschrijving'); ?></p>
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <a href="<?php echo BASE_PATH; ?>/tasks.php" class="text-sm font-medium text-purple-600 hover:text-purple-700">
                                                Bekijk details →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="<?php echo BASE_PATH; ?>/grades.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <span class="text-2xl">📊</span>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Bekijk je Cijfers</p>
                                <p class="text-xl font-bold text-gray-900">Mijn Resultaten</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="<?php echo BASE_PATH; ?>/tasks.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="flex items-center">
                            <div class="bg-orange-100 rounded-full p-3 mr-4">
                                <span class="text-2xl">⏰</span>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Komende Deadlines</p>
                                <p class="text-xl font-bold text-gray-900">Mijn Planning</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="<?php echo BASE_PATH; ?>/materials.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-3 mr-4">
                                <span class="text-2xl">📚</span>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Alle Materialen</p>
                                <p class="text-xl font-bold text-gray-900">Theorie & Links</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
