<?php
session_start();

// Als al ingelogd, redirect naar dashboard
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';
$success = '';

// Verwerk login form
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    require_once __DIR__ . '/src/controllers/AuthController.php';
    
    $result = AuthController::handleLogin($_POST['student_number'] ?? '', $_POST['password'] ?? '');
    
    if($result['success']) {
        header('Location: /dashboard.php');
        exit;
    } else {
        $error = $result['error'];
    }
}

// Verwerk registratie form
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    require_once __DIR__ . '/src/controllers/AuthController.php';
    
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
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Tabs -->
            <div class="auth-tabs">
                <button type="button" class="auth-tab active" data-tab="login">Inloggen</button>
                <button type="button" class="auth-tab" data-tab="register">Registreren</button>
            </div>
            
            <!-- Login Form -->
            <div id="login" class="auth-tab-content active">
                <h1>Inloggen bij StudyBuddy</h1>
                
                <?php if($error && !isset($_POST['action']) || ($_POST['action'] === 'login')): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label for="student_number">Studentnummer</label>
                        <input type="text" id="student_number" name="student_number" required 
                               placeholder="bijv. S12345" pattern="[A-Za-z0-9]{5,10}">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Wachtwoord</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Minimaal 6 karakters">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Inloggen</button>
                </form>
            </div>
            
            <!-- Register Form -->
            <div id="register" class="auth-tab-content">
                <h1>Account Aanmaken</h1>
                
                <?php if($error && isset($_POST['action']) && $_POST['action'] === 'register'): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-group">
                        <label for="reg_student_number">Studentnummer</label>
                        <input type="text" id="reg_student_number" name="student_number" required 
                               placeholder="bijv. S12345" pattern="[A-Za-z0-9]{5,10}">
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_full_name">Volledige Naam</label>
                        <input type="text" id="reg_full_name" name="full_name" required 
                               placeholder="Voornaam Achternaam">
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_email">Email</label>
                        <input type="email" id="reg_email" name="email" required 
                               placeholder="jouw@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_password">Wachtwoord</label>
                        <input type="password" id="reg_password" name="password" required 
                               placeholder="Minimaal 6 karakters">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Account Aanmaken</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Tab switching
        document.querySelectorAll('.auth-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Hide all tabs
                document.querySelectorAll('.auth-tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Remove active from all tab buttons
                document.querySelectorAll('.auth-tab').forEach(tabBtn => {
                    tabBtn.classList.remove('active');
                });
                
                // Show selected tab
                document.getElementById(tabName).classList.add('active');
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
