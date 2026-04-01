<?php
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../src/utils/SessionManager.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/CoursesController.php';
require_once __DIR__ . '/../src/models/Material.php';

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
                    <a href="<?php echo BASE_PATH; ?>/materials.php" class="flex items-center space-x-3 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg mb-2 font-medium border-l-4 border-purple-600">
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
                    <h1 class="text-3xl font-bold text-gray-900">📚 Studiemateriaal</h1>
                    <p class="text-gray-600 mt-2">Alle links en documenten per vak</p>
                </div>

                <?php if(empty($materials_by_course)): ?>
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Geen materiaal</h3>
                        <p class="mt-1 text-gray-600">Voor je vakken is nog geen studiemateriaal toegevoegd.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-8">
                        <?php foreach($materials_by_course as $course_id => $course_data): ?>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center space-x-2">
                                    <span>📖</span>
                                    <span><?php echo htmlspecialchars($course_data['course_name']); ?></span>
                                </h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <?php foreach($course_data['materials'] as $material): ?>
                                        <?php
                                            // Vervang standaard voorbeeldlink door w3schools wanneer aanwezig
                                            $materialUrl = $material['url'];
                                            if($materialUrl === 'https://example.com/study-guide') {
                                                $materialUrl = 'https://www.w3schools.com';
                                            }
                                        ?>
                                        <a href="<?php echo htmlspecialchars($materialUrl); ?>" target="_blank" rel="noopener noreferrer"
                                           class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 border-l-4 border-purple-500">
                                            <div class="flex items-start space-x-3">
                                                <div class="text-2xl">
                                                    <?php 
                                                        $file_type = strtolower($material['file_type'] ?? 'url');
                                                        if(strpos($file_type, 'pdf') !== false) echo '📄';
                                                        elseif(strpos($file_type, 'video') !== false) echo '🎥';
                                                        elseif(strpos($file_type, 'audio') !== false) echo '🎵';
                                                        else echo '🔗';
                                                    ?>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="font-semibold text-gray-900 truncate">
                                                        <?php echo htmlspecialchars($material['title']); ?>
                                                    </h3>
                                                    <p class="text-xs text-gray-600 mt-1 truncate">
                                                        <?php echo htmlspecialchars(substr($material['url'], 0, 40)); ?>...
                                                    </p>
                                                </div>
                                                <span class="text-purple-600 text-lg flex-shrink-0">→</span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
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
