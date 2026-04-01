<?php
require_once __DIR__ . '/../config/Config.php';
session_start();

// Als al ingelogd, redirect naar dashboard
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ' . BASE_PATH . '/dashboard.php');
    exit;
}

$error = '';
$success = '';

// Verwerk login form
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    require_once __DIR__ . '/../src/controllers/AuthController.php';
    
    $result = AuthController::handleLogin($_POST['student_number'] ?? '', $_POST['password'] ?? '');
    
    if($result['success']) {
        header('Location: ' . BASE_PATH . '/dashboard.php');
        exit;
    } else {
        $error = $result['error'];
    }
}

// Verwerk registratie form
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    require_once __DIR__ . '/../src/controllers/AuthController.php';
    
    $result = AuthController::handleRegister($_POST);
    
    if($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyBuddy - Inloggen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo / Header -->
            <div class="text-center mb-8">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-white text-3xl font-bold">StudyBuddy</h1>
                <p class="text-purple-100 text-sm mt-2">Je persoonlijke studie assistent</p>
            </div>

            <!-- Auth Card -->
            <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
                <!-- Tabs -->
                <div class="flex bg-gray-100">
                    <button type="button" 
                            onclick="switchTab('login')" 
                            id="login-tab"
                            class="flex-1 py-4 px-6 text-center font-semibold text-gray-700 hover:text-purple-600 border-b-2 border-purple-600 bg-white transition">
                        Inloggen
                    </button>
                    <button type="button" 
                            onclick="switchTab('register')" 
                            id="register-tab"
                            class="flex-1 py-4 px-6 text-center font-semibold text-gray-500 hover:text-purple-600 border-b-2 border-transparent transition">
                        Registreren
                    </button>
                </div>

                <div class="p-8">
                    <!-- Login Form -->
                    <div id="login-content" class="block">
                        <?php if($error && (!isset($_POST['action']) || $_POST['action'] === 'login')): ?>
                            <div class="mb-4 p-4 bg-red-50 rounded-lg border border-red-200">
                                <p class="text-red-700 text-sm font-medium">⚠️ <?php echo htmlspecialchars($error); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="login">
                            
                            <div>
                                <label for="student_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Studentnummer of e-mail
                                </label>
                                <input type="text" id="student_number" name="student_number" required 
                                       placeholder="bijv. S12345 of admin@localhost" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Wachtwoord
                                </label>
                                <input type="password" id="password" name="password" required 
                                       placeholder="Minimaal 6 karakters"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            
                            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-2 px-4 rounded-lg hover:from-purple-700 hover:to-purple-800 transition transform hover:-translate-y-0.5">
                                Inloggen
                            </button>
                        </form>
                    </div>

                    <!-- Register Form -->
                    <div id="register-content" class="hidden">
                        <?php if($error && isset($_POST['action']) && $_POST['action'] === 'register'): ?>
                            <div class="mb-4 p-4 bg-red-50 rounded-lg border border-red-200">
                                <p class="text-red-700 text-sm font-medium">⚠️ <?php echo htmlspecialchars($error); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($success): ?>
                            <div class="mb-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                <p class="text-green-700 text-sm font-medium">✅ <?php echo htmlspecialchars($success); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="register">
                            
                            <div>
                                <label for="reg_student_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Studentnummer
                                </label>
                                <input type="text" id="reg_student_number" name="student_number" required 
                                       placeholder="bijv. S12345" 
                                       pattern="[A-Za-z0-9]{5,10}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label for="reg_full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Volledige Naam
                                </label>
                                <input type="text" id="reg_full_name" name="full_name" required 
                                       placeholder="Voornaam Achternaam"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label for="reg_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" id="reg_email" name="email" required 
                                       placeholder="jouw@email.com"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label for="reg_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Wachtwoord
                                </label>
                                <input type="password" id="reg_password" name="password" required 
                                       placeholder="Minimaal 6 karakters"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            </div>
                            
                            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-2 px-4 rounded-lg hover:from-purple-700 hover:to-purple-800 transition transform hover:-translate-y-0.5">
                                Account Aanmaken
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                    <p class="text-gray-600 text-xs text-center">
                        Beveiligd door enterprise-grade encryptie
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all content
            document.getElementById('login-content').classList.add('hidden');
            document.getElementById('register-content').classList.add('hidden');
            document.getElementById('login-tab').classList.remove('border-purple-600', 'bg-white');
            document.getElementById('login-tab').classList.add('border-transparent');
            document.getElementById('register-tab').classList.remove('border-purple-600', 'bg-white');
            document.getElementById('register-tab').classList.add('border-transparent');

            // Show selected content
            if(tab === 'login') {
                document.getElementById('login-content').classList.remove('hidden');
                document.getElementById('login-tab').classList.add('border-purple-600', 'bg-white');
                document.getElementById('login-tab').classList.remove('border-transparent');
            } else {
                document.getElementById('register-content').classList.remove('hidden');
                document.getElementById('register-tab').classList.add('border-purple-600', 'bg-white');
                document.getElementById('register-tab').classList.remove('border-transparent');
            }
        }
    </script>
</body>
</html>
