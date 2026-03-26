<?php
/**
 * CoursesController
 * Beheer vak gerelateerde operaties
 */

require_once __DIR__ . '/../models/Course.php';

class CoursesController {
    
    /**
     * Get alle vakken voor student
     * 
     * @param int $student_id
     * @return array
     */
    public static function getStudentCourses($student_id) {
        $course = new Course();
        return $course->getCoursesByStudent($student_id);
    }
    
    /**
     * Get alle vakken voor admin
     * 
     * @param int $admin_id
     * @return array
     */
    public static function getAdminCourses($admin_id) {
        $course = new Course();
        return $course->getCoursesByAdmin($admin_id);
    }
    
    /**
     * Create nieuw vak (admin only)
     * 
     * @param int $admin_id
     * @param array $data
     * @return array
     */
    public static function createCourse($admin_id, $data) {
        // Validatie
        if(empty($data['code']) || empty($data['name'])) {
            return array('success' => false, 'error' => 'Code en naam zijn verplicht');
        }
        
        $course = new Course();
        $course->code = $data['code'];
        $course->name = $data['name'];
        $course->description = $data['description'] ?? '';
        $course->admin_id = $admin_id;
        
        if($course->create()) {
            return array('success' => true, 'message' => 'Vak succesvol aangemaakt');
        }
        return array('success' => false, 'error' => 'Fout bij aanmaken vak');
    }
    
    /**
     * Get vak details
     * 
     * @param int $course_id
     * @return array|false
     */
    public static function getCourseDetails($course_id) {
        $course = new Course();
        return $course->getCourseById($course_id);
    }
}
?>
