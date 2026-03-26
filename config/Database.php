<?php
/**
 * Database Configuration
 * Connectie met MySQL database
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'study_buddy';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private $connection;
    
    /**
     * Maak database connectie
     * 
     * @return PDO
     */
    public function connect() {
        $this->connection = null;
        
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=' . $this->charset;
        
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        
        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            return $this->connection;
        } catch(PDOException $e) {
            die('Databasefout: ' . $e->getMessage());
        }
    }
    
    /**
     * Get actieve connectie
     * 
     * @return PDO
     */
    public function getConnection() {
        if(!$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }
}
?>
