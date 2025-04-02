<?php
require_once __DIR__ . '/../config/config.php';

class DB {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->logError('Database Connection Error: ' . $e->getMessage());
            throw new Exception('Connection failed: Database error');
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError('Query Error: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw new Exception('Database query failed');
        }
    }

    public function insert($table, $data) {
        try {
            $fields = array_keys($data);
            $values = array_fill(0, count($fields), '?');
            
            $sql = "INSERT INTO " . $table . " (" . implode(", ", $fields) . ") 
                    VALUES (" . implode(", ", $values) . ")";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($data));
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->logError('Insert Error: ' . $e->getMessage());
            throw new Exception('Failed to insert data');
        }
    }

    public function update($table, $data, $where, $whereParams = []) {
        try {
            $fields = array_keys($data);
            $set = array_map(function($field) {
                return "$field = ?";
            }, $fields);
            
            $sql = "UPDATE " . $table . " SET " . implode(", ", $set) . " WHERE " . $where;
            
            $params = array_merge(array_values($data), $whereParams);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->logError('Update Error: ' . $e->getMessage());
            throw new Exception('Failed to update data');
        }
    }

    public function delete($table, $where, $params = []) {
        try {
            $sql = "DELETE FROM " . $table . " WHERE " . $where;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->logError('Delete Error: ' . $e->getMessage());
            throw new Exception('Failed to delete data');
        }
    }

    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError('FetchAll Error: ' . $e->getMessage());
            throw new Exception('Failed to fetch data');
        }
    }

    public function fetch($sql, $params = []) {
        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->logError('Fetch Error: ' . $e->getMessage());
            throw new Exception('Failed to fetch data');
        }
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

    private function logError($message) {
        $logFile = __DIR__ . '/../log.txt';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}