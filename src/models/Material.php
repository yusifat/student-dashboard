<?php
/**
 * Material Model
 * Beheer studiemateriaal en links
 */

require_once __DIR__ . '/../../config/Database.php';

class Material {
    private $db;
    private $table = 'materials';
    
    // Properties
    public $id;
    public $course_id;
    public $title;
    public $url;
    public $file_type;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    /**
     * Get materiaal per vak
     * 
     * @param int $course_id
     * @return array
     */
    public function getMaterialByCourse($course_id) {
        $stmt = $this->db->prepare('
            SELECT * FROM ' . $this->table . ' 
            WHERE course_id = ?
            ORDER BY created_at DESC
        ');
        $stmt->execute([$course_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Voeg materiaal toe (admin)
     * 
     * @return bool
     */
    public function addMaterial() {
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->file_type = htmlspecialchars(strip_tags($this->file_type));
        
        // Valideer URL
        if(!filter_var($this->url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        $stmt = $this->db->prepare('
            INSERT INTO ' . $this->table . ' 
            (course_id, title, url, file_type) 
            VALUES (?, ?, ?, ?)
        ');
        
        return $stmt->execute([
            $this->course_id,
            $this->title,
            $this->url,
            $this->file_type
        ]);
    }
    
    /**
     * Delete materiaal
     * 
     * @param int $id
     * @return bool
     */
    public function deleteMaterial($id) {
        $stmt = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
?>
