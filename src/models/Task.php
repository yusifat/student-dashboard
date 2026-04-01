<?php
/**
 * Task Model
 * Beheer taken en deadlines
 */

require_once __DIR__ . '/../../config/Database.php';

class Task {
    private $db;
    private $table = 'tasks';
    
    // Properties
    public $id;
    public $course_id;
    public $title;
    public $description;
    public $deadline;
    public $status;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    /**
     * Get alle taken gesorteerd op deadline
     * 
     * @param int $student_id
     * @return array
     */
    public function getTasksByStudent($student_id) {
        $stmt = $this->db->prepare('
            SELECT t.*, c.name as course_name, c.code
            FROM ' . $this->table . ' t
            INNER JOIN courses c ON t.course_id = c.id
            INNER JOIN student_courses sc ON c.id = sc.course_id
            WHERE sc.student_id = ?
            ORDER BY t.deadline ASC
        ');
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get kritieke taken (deadline < 2 dagen)
     * 
     * @param int $student_id
     * @return array
     */
    public function getCriticalTasks($student_id) {
        $stmt = $this->db->prepare('
            SELECT t.*, c.name as course_name
            FROM ' . $this->table . ' t
            INNER JOIN courses c ON t.course_id = c.id
            INNER JOIN student_courses sc ON c.id = sc.course_id
            WHERE sc.student_id = ? 
            AND t.status = "pending"
            AND t.deadline BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 DAY)
            ORDER BY t.deadline ASC
        ');
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get taken voor specifiek vak
     * 
     * @param int $course_id
     * @return array
     */
    public function getTasksByCourse($course_id) {
        $stmt = $this->db->prepare('
            SELECT * FROM ' . $this->table . ' 
            WHERE course_id = ?
            ORDER BY deadline ASC
        ');
        $stmt->execute([$course_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Bereken dagen tot deadline
     * 
     * @param string $deadline (YYYY-MM-DD HH:MM:SS)
     * @return int
     */
    public function daysUntilDeadline($deadline) {
        $deadline_date = new DateTime($deadline);
        $now = new DateTime();
        $interval = $deadline_date->diff($now);
        
        return $interval->days;
    }
    
    /**
     * Bepaal urgentie van taak
     * 
     * @param string $deadline
     * @return string (critical, planned, completed)
     */
    public function getUrgency($deadline, $status) {
        if($status == 'completed') {
            return 'completed';
        }
        
        $days = $this->daysUntilDeadline($deadline);
        
        if($days <= 2) {
            return 'critical';
        }
        return 'planned';
    }
    
    /**
     * Maak nieuwe taak (admin/docent)
     * 
     * @return bool
     */
    public function createTask() {
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt = $this->db->prepare('
            INSERT INTO ' . $this->table . ' 
            (course_id, title, description, deadline, status) 
            VALUES (?, ?, ?, ?, ?)
        ');
        
        return $stmt->execute([
            $this->course_id,
            $this->title,
            $this->description,
            $this->deadline,
            'pending'
        ]);
    }
    
    /**
     * Update taak status
     * 
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare('
            UPDATE ' . $this->table . ' 
            SET status = ? 
            WHERE id = ?
        ');
        
        return $stmt->execute([$status, $id]);
    }
    
    /**
     * Delete taak
     * 
     * @param int $id
     * @return bool
     */
    public function deleteTask($id) {
        $stmt = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    /**
     * Submit taak door student (met optionele bijlage)
     * 
     * @param int $task_id
     * @param int $student_id
     * @param string|null $file_path
     * @return bool
     */
    public function submitTask($task_id, $student_id, $file_path = null) {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Update task status to completed
            $stmt1 = $this->db->prepare('UPDATE ' . $this->table . ' SET status = ? WHERE id = ?');
            $stmt1->execute(['completed', $task_id]);
            
            // Insert or update submission record
            $stmt2 = $this->db->prepare('
                INSERT INTO task_submissions (task_id, student_id, file_path) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE file_path = VALUES(file_path), submitted_at = CURRENT_TIMESTAMP
            ');
            $stmt2->execute([$task_id, $student_id, $file_path]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Get submission voor taak en student
     * 
     * @param int $task_id
     * @param int $student_id
     * @return array|false
     */
    public function getSubmission($task_id, $student_id) {
        $stmt = $this->db->prepare('
            SELECT * FROM task_submissions 
            WHERE task_id = ? AND student_id = ?
        ');
        $stmt->execute([$task_id, $student_id]);
        return $stmt->fetch();
    }
}
?>
