<?php
/**
 * Course Model
 * Beheer vakken en inschrijvingen
 */

require_once __DIR__ . '/../../config/Database.php';

class Course {
    private $db;
    private $table = 'courses';
    private $student_courses_table = 'student_courses';
    
    // Properties
    public $id;
    public $code;
    public $name;
    public $description;
    public $admin_id;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    /**
     * Get alle vakken voor een student
     * 
     * @param int $student_id
     * @return array
     */
    public function getCoursesByStudent($student_id) {
        $stmt = $this->db->prepare('
            SELECT c.* FROM ' . $this->table . ' c
            INNER JOIN ' . $this->student_courses_table . ' sc 
            ON c.id = sc.course_id
            WHERE sc.student_id = ?
            ORDER BY c.name ASC
        ');
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get alle vakken (for admin)
     * 
     * @param int $admin_id
     * @return array
     */
    public function getCoursesByAdmin($admin_id) {
        $stmt = $this->db->prepare('
            SELECT * FROM ' . $this->table . ' 
            WHERE admin_id = ?
            ORDER BY name ASC
        ');
        $stmt->execute([$admin_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get vak by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getCourseById($id) {
        $stmt = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Student aantal per vak
     * 
     * @param int $course_id
     * @return int
     */
    public function getStudentCount($course_id) {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) as count FROM ' . $this->student_courses_table . ' 
            WHERE course_id = ?
        ');
        $stmt->execute([$course_id]);
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Get ingeschreven studenten voor een vak
     * 
     * @param int $course_id
     * @return array
     */
    public function getStudentsByCourse($course_id) {
        $stmt = $this->db->prepare('
            SELECT u.id, u.student_number, u.full_name
            FROM users u
            INNER JOIN ' . $this->student_courses_table . ' sc ON sc.student_id = u.id
            WHERE sc.course_id = ?
            ORDER BY u.full_name ASC
        ');
        $stmt->execute([$course_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Maak nieuw vak (admin only)
     * 
     * @return bool
     */
    public function create() {
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt = $this->db->prepare('
            INSERT INTO ' . $this->table . ' 
            (code, name, description, admin_id) 
            VALUES (?, ?, ?, ?)
        ');
        
        return $stmt->execute([
            $this->code,
            $this->name,
            $this->description,
            $this->admin_id
        ]);
    }
    
    /**
     * Update vak
     * 
     * @return bool
     */
    public function update() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt = $this->db->prepare('
            UPDATE ' . $this->table . ' 
            SET name = ?, description = ? 
            WHERE id = ?
        ');
        
        return $stmt->execute([
            $this->name,
            $this->description,
            $this->id
        ]);
    }
    
    /**
     * Delete vak
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    /**
     * Voeg student toe aan vak
     * 
     * @param int $student_id
     * @param int $course_id
     * @return bool
     */
    public function enrollStudent($student_id, $course_id) {
        $stmt = $this->db->prepare('
            INSERT INTO ' . $this->student_courses_table . ' 
            (student_id, course_id) 
            VALUES (?, ?)
        ');
        
        return $stmt->execute([$student_id, $course_id]);
    }
}
?>
