<?php
/**
 * AuthController
 * Beheer inloggen, registratie en logout
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/SessionManager.php';

class AuthController {
    
    /**
     * Verwerk login
     * 
     * @param string $student_number
     * @param string $password
     * @return array
     */
    public static function handleLogin($student_number, $password) {
        $user = new User();
        
        // Input validation
        if(empty($student_number) || empty($password)) {
            return array(
                'success' => false,
                'error' => 'Studentnummer en wachtwoord zijn verplicht'
            );
        }
        
        // Attempt login
        if($user->login($student_number, $password)) {
            SessionManager::setUser(array(
                'id' => $user->id,
                'student_number' => $user->student_number,
                'full_name' => $user->full_name,
                'role' => $user->role
            ));
            
            return array(
                'success' => true,
                'user' => array(
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'role' => $user->role
                )
            );
        }
        
        return array(
            'success' => false,
            'error' => 'Ongeldig studentnummer of wachtwoord'
        );
    }
    
    /**
     * Verwerk registratie
     * 
     * @param array $data
     * @return array
     */
    public static function handleRegister($data) {
        $user = new User();
        
        // Validatie
        if(empty($data['student_number']) || empty($data['email']) || 
           empty($data['password']) || empty($data['full_name'])) {
            return array(
                'success' => false,
                'error' => 'Alle velden zijn verplicht'
            );
        }
        
        // Email validatie
        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return array(
                'success' => false,
                'error' => 'Ongeldig email adres'
            );
        }
        
        // Studentnummer lengte check
        if(strlen($data['student_number']) < 5 || strlen($data['student_number']) > 10) {
            return array(
                'success' => false,
                'error' => 'Studentnummer moet tussen 5 en 10 karakters zijn'
            );
        }
        
        // Password lengte check
        if(strlen($data['password']) < 6) {
            return array(
                'success' => false,
                'error' => 'Wachtwoord moet minimaal 6 karakters zijn'
            );
        }
        
        // Set user properties
        $user->student_number = $data['student_number'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->full_name = $data['full_name'];
        
        // Registreer
        if($user->register()) {
            return array(
                'success' => true,
                'message' => 'Registratie succesvol! Je kunt nu inloggen.'
            );
        }
        
        return array(
            'success' => false,
            'error' => 'Registratie mislukt. Mogelijk bestaat het studentnummer al.'
        );
    }
    
    /**
     * Verwerk logout
     */
    public static function handleLogout() {
        SessionManager::logout();
        return array('success' => true);
    }
    
    /**
     * Check of user ingelogd is
     * 
     * @return bool
     */
    public static function isAuthenticated() {
        return SessionManager::isLoggedIn();
    }
    
    /**
     * Redirect naar login als niet ingelogd
     */
    public static function requireLogin() {
        if(!self::isAuthenticated()) {
            $base = defined('BASE_PATH') ? BASE_PATH : '';
            header('Location: ' . $base . '/login.php');
            exit;
        }
    }
    
    /**
     * Redirect naar login als niet admin
     */
    public static function requireAdmin() {
        if(!SessionManager::isAdmin()) {
            $base = defined('BASE_PATH') ? BASE_PATH : '';
            header('Location: ' . $base . '/dashboard.php');
            exit;
        }
    }
}
?>
