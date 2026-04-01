<?php
/**
 * Grade Model
 * Beheer cijfers en gemiddelde berekening
 */

require_once __DIR__ . '/../../config/Database.php';

class Grade {
    private $db;
    private $table = 'grades';
    
    // Properties
    public $id;
    public $student_id;
    public $course_id;
    public $grade_value;
    public $weight;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    /**
     * Get alle cijfers van student
     * 
     * @param int $student_id
     * @return array
     */
    public function getGradesByStudent($student_id) {
        $stmt = $this->db->prepare('
            SELECT g.*, c.name as course_name, c.code 
            FROM ' . $this->table . ' g
            INNER JOIN courses c ON g.course_id = c.id
            WHERE g.student_id = ?
            ORDER BY c.name ASC
        ');
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get cijfers voor specifiek vak (per student)
     * 
     * @param int $student_id
     * @param int $course_id
     * @return array
     */
    public function getGradesByCourse($student_id, $course_id) {
        $stmt = $this->db->prepare('
            SELECT * FROM ' . $this->table . ' 
            WHERE student_id = ? AND course_id = ?
            ORDER BY created_at DESC
        ');
        $stmt->execute([$student_id, $course_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get alle cijfers voor een vak (admin zicht)
     * 
     * @param int $course_id
     * @return array
     */
    public function getGradesByCourseAll($course_id) {
        $stmt = $this->db->prepare('
            SELECT g.* , u.full_name, u.student_number
            FROM ' . $this->table . ' g
            INNER JOIN users u ON g.student_id = u.id
            WHERE g.course_id = ?
            ORDER BY u.full_name ASC, g.created_at DESC
        ');
        $stmt->execute([$course_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Bereken gemiddelde voor student in vak
     * 
     * @param int $student_id
     * @param int $course_id
     * @return float|null
     */
    public function calculateAverage($student_id, $course_id) {
        $stmt = $this->db->prepare('
            SELECT 
                SUM(grade_value * weight) / SUM(weight) as average
            FROM ' . $this->table . ' 
            WHERE student_id = ? AND course_id = ?
        ');
        $stmt->execute([$student_id, $course_id]);
        $result = $stmt->fetch();
        
        if($result['average']) {
            return round($result['average'], 2);
        }
        return null;
    }
    
    /**
     * Bereken algemeen gemiddelde student
     * 
     * @param int $student_id
     * @return float
     */
    public function calculateOverallAverage($student_id) {
        $stmt = $this->db->prepare('
            SELECT AVG(grade_value) as average 
            FROM ' . $this->table . ' 
            WHERE student_id = ?
        ');
        $stmt->execute([$student_id]);
        $result = $stmt->fetch();
        
        if($result['average']) {
            return round($result['average'], 2);
        }
        return 0;
    }
    
    /**
     * Voeg nieuw cijfer toe
     * 
     * @return bool
     */
    public function addGrade() {
        // Validatie
        if($this->grade_value < 1 || $this->grade_value > 10) {
            return false;
        }
        
        $stmt = $this->db->prepare('
            INSERT INTO ' . $this->table . ' 
            (student_id, course_id, grade_value, weight) 
            VALUES (?, ?, ?, ?)
        ');
        
        return $stmt->execute([
            $this->student_id,
            $this->course_id,
            $this->grade_value,
            $this->weight
        ]);
    }
    
    /**
     * Update cijfer
     * 
     * @return bool
     */
    public function updateGrade() {
        // Validatie
        if($this->grade_value < 1 || $this->grade_value > 10) {
            return false;
        }
        
        $stmt = $this->db->prepare('
            UPDATE ' . $this->table . ' 
            SET grade_value = ?, weight = ? 
            WHERE id = ?
        ');
        
        return $stmt->execute([
            $this->grade_value,
            $this->weight,
            $this->id
        ]);
    }
    
    /**
     * Delete cijfer
     * 
     * @param int $id
     * @return bool
     */
    public function deleteGrade($id) {
        $stmt = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
?>
