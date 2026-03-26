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
                    <a href="<?php echo BASE_PATH; ?>/dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg mb-2 transition">
                        <span>📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/grades.php" class="flex items-center space-x-3 px-4 py-3 bg-purple-50 text-purple-700 rounded-lg mb-2 font-medium border-l-4 border-purple-600">
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
                    <h1 class="text-3xl font-bold text-gray-900">📈 Mijn Cijfers</h1>
                    <p class="text-gray-600 mt-2">Overzicht van alle behaalde resultaten</p>
                </div>

                <!-- Overall Average Card -->
                <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-lg shadow-lg p-8 mb-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-200 text-sm font-semibold uppercase tracking-wide mb-2">Algemeen Gemiddelde</p>
                            <p class="text-5xl font-bold"><?php echo round($overall_average, 2); ?></p>
                        </div>
                        <div class="text-6xl opacity-20">📊</div>
                    </div>
                </div>

                <!-- Grades by Course -->
                <?php if(empty($grades_by_course)): ?>
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Geen cijfers</h3>
                        <p class="mt-1 text-gray-600">Je hebt nog geen cijfers ingevoerd.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach($grades_by_course as $course_id => $course_data): ?>
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                <!-- Course Header -->
                                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4 text-white">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-xl font-bold"><?php echo htmlspecialchars($course_data['course_name']); ?></h2>
                                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                                            <span class="text-2xl font-bold"><?php echo round($course_averages[$course_id], 2); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grades Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Datum</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Weging</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Cijfer</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <?php foreach($course_data['grades'] as $grade): ?>
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-6 py-4 text-sm text-gray-600">
                                                        <?php echo date('d-m-Y', strtotime($grade['created_at'])); ?>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($grade['weight']); ?>x
                                                    </td>
                                                    <td class="px-6 py-4 text-sm">
                                                        <?php 
                                                            $g = $grade['grade_value'];
                                                            if($g < 5.5) {
                                                                echo '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">' . round($g, 2) . '</span>';
                                                            } else {
                                                                echo '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">' . round($g, 2) . '</span>';
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
