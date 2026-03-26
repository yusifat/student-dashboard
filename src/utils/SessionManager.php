<?php
/**
 * Session Utility
 * Beheer gebruiker sessie en authenticatie
 */

class SessionManager {
    
    /**
     * Start sessie
     */
    public static function start() {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Set user in session
     * 
     * @param array $user
     */
    public static function setUser($user) {
        self::start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['student_number'] = $user['student_number'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
    }
    
    /**
     * Get huidge user
     * 
     * @return array|null
     */
    public static function getUser() {
        self::start();
        
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            return array(
                'id' => $_SESSION['user_id'],
                'student_number' => $_SESSION['student_number'],
                'full_name' => $_SESSION['full_name'],
                'role' => $_SESSION['role']
            );
        }
        return null;
    }
    
    /**
     * Check of user ingelogd is
     * 
     * @return bool
     */
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Check of user admin is
     * 
     * @return bool
     */
    public static function isAdmin() {
        self::start();
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Destroy sessie (logout)
     */
    public static function logout() {
        self::start();
        session_unset();
        session_destroy();
    }
}
?>
