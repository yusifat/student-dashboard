<?php
/**
 * TasksController
 * Beheer taken en deadlines
 */

require_once __DIR__ . '/../models/Task.php';

class TasksController {
    
    /**
     * Get alle taken voor student
     * 
     * @param int $student_id
     * @return array
     */
    public static function getStudentTasks($student_id) {
        $task = new Task();
        return $task->getTasksByStudent($student_id);
    }
    
    /**
     * Get kritieke taken
     * 
     * @param int $student_id
     * @return array
     */
    public static function getCriticalTasks($student_id) {
        $task = new Task();
        return $task->getCriticalTasks($student_id);
    }
    
    /**
     * Update task status
     * 
     * @param int $task_id
     * @param string $status
     * @return array
     */
    public static function updateTaskStatus($task_id, $status) {
        if(!in_array($status, array('pending', 'completed'))) {
            return array('success' => false, 'error' => 'Ongeldig status');
        }
        
        $task = new Task();
        if($task->updateStatus($task_id, $status)) {
            return array('success' => true, 'message' => 'Status bijgewerkt');
        }
        return array('success' => false, 'error' => 'Fout bij bijwerken');
    }
    
    /**
     * Get urgency klasse
     * 
     * @param string $deadline
     * @param string $status
     * @return string
     */
    public static function getUrgencyClass($deadline, $status) {
        $task = new Task();
        return $task->getUrgency($deadline, $status);
    }
    
    /**
     * Format deadline date
     * 
     * @param string $deadline
     * @return string
     */
    public static function formatDeadline($deadline) {
        return date('d-m-Y H:i', strtotime($deadline));
    }
}
?>
