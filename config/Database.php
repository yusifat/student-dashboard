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
        
        // Eerst verbinden met de server en kijken of de database bestaat
        $dsn = 'mysql:host=' . $this->host . ';charset=' . $this->charset;
        
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        
        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);

            // Maak database aan als deze nog niet bestaat
            $this->connection->exec('CREATE DATABASE IF NOT EXISTS `' . $this->db_name . '` CHARACTER SET ' . $this->charset . ' COLLATE utf8mb4_unicode_ci');

            // Selecteer de juiste database
            $this->connection->exec('USE `' . $this->db_name . '`');

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
