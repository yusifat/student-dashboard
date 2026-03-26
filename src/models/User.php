<?php
/**
 * User Model
 * Beheer gebruiker data en authenticatie
 */

require_once __DIR__ . '/../../config/Database.php';

class User {
    private $db;
    private $table = 'users';
    
    // Properties
    public $id;
    public $student_number;
    public $email;
    public $password;
    public $full_name;
    public $role;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    /**
     * Login gebruiker
     * 
     * @param string $student_number
     * @param string $password
     * @return bool
     */
    public function login($student_number, $password) {
        $stmt = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE student_number = ?');
        $stmt->execute([$student_number]);
        
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password_hash'])) {
            $this->id = $user['id'];
            $this->student_number = $user['student_number'];
            $this->email = $user['email'];
            $this->full_name = $user['full_name'];
            $this->role = $user['role'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Register nieuwe gebruiker
     * 
     * @return bool
     */
    public function register() {
        // Sanitize
        $this->student_number = htmlspecialchars(strip_tags($this->student_number));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        
        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_ARGON2ID);
        
        $stmt = $this->db->prepare('
            INSERT INTO ' . $this->table . ' 
            (student_number, email, password_hash, full_name, role) 
            VALUES (?, ?, ?, ?, ?)
        ');
        
        return $stmt->execute([
            $this->student_number,
            $this->email,
            $hashed_password,
            $this->full_name,
            'student' // Nieuwe registraties zijn altijd student
        ]);
    }
    
    /**
     * Get gebruiker by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare('SELECT id, student_number, email, full_name, role FROM ' . $this->table . ' WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get alle admins
     * 
     * @return array
     */
    public function getAdmins() {
        $stmt = $this->db->prepare('SELECT id, student_number, full_name FROM ' . $this->table . ' WHERE role = "admin"');
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Update gebruiker gegevens
     * 
     * @return bool
     */
    public function update() {
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        
        $stmt = $this->db->prepare('
            UPDATE ' . $this->table . ' 
            SET full_name = ? 
            WHERE id = ?
        ');
        
        return $stmt->execute([
            $this->full_name,
            $this->id
        ]);
    }
}
?>
