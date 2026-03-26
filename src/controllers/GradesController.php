<?php
/**
 * GradesController
 * Beheer cijfers en gemiddelde berekeningen
 */

require_once __DIR__ . '/../models/Grade.php';
require_once __DIR__ . '/../models/Course.php';

class GradesController {
    
    /**
     * Get alle cijfers van student
     * 
     * @param int $student_id
     * @return array
     */
    public static function getStudentGrades($student_id) {
        $grade = new Grade();
        return $grade->getGradesByStudent($student_id);
    }
    
    /**
     * Get gemiddelde per vak
     * 
     * @param int $student_id
     * @param int $course_id
     * @return float|null
     */
    public static function getCourseAverage($student_id, $course_id) {
        $grade = new Grade();
        return $grade->calculateAverage($student_id, $course_id);
    }
    
    /**
     * Get algemeen gemiddelde
     * 
     * @param int $student_id
     * @return float
     */
    public static function getOverallAverage($student_id) {
        $grade = new Grade();
        return $grade->calculateOverallAverage($student_id);
    }
    
    /**
     * Voeg nieuw cijfer toe
     * 
     * @param int $student_id
     * @param array $data
     * @return array
     */
    public static function addGrade($student_id, $data) {
        // Validatie
        if(empty($data['course_id']) || empty($data['grade_value'])) {
            return array('success' => false, 'error' => 'Vak en cijfer zijn verplicht');
        }
        
        $grade_value = (float)$data['grade_value'];
        
        if($grade_value < 1 || $grade_value > 10) {
            return array('success' => false, 'error' => 'Cijfer moet tussen 1 en 10 liggen');
        }
        
        $grade = new Grade();
        $grade->student_id = $student_id;
        $grade->course_id = (int)$data['course_id'];
        $grade->grade_value = $grade_value;
        $grade->weight = (int)($data['weight'] ?? 1);
        
        if($grade->addGrade()) {
            return array('success' => true, 'message' => 'Cijfer succesvol opgeslagen');
        }
        return array('success' => false, 'error' => 'Fout bij opslaan cijfer');
    }
    
    /**
     * Bepaal achtergrondkleur voor cijfer
     * 
     * @param float $grade
     * @return string
     */
    public static function getGradeClass($grade) {
        if($grade < 5.5) {
            return 'critical';
        }
        return 'good';
    }
}
?>
