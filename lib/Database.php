<?php
namespace Lib;

use PDO;
use PDOException;

class Database {
    private $pdo;
    private $dbName;

    public function __construct() {
        $this->loadEnv();
        $this->connectServer();
        $this->createDatabaseIfNotExists();
        $this->connectDatabase();
    }

    /** Load .env file */
    private function loadEnv() {
        $envFile = __DIR__ . '/../.env';
        if (!file_exists($envFile)) {
            throw new \Exception(".env file not found!");
        }

        $env = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($env as $line) {
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }

        $this->dbName = $_ENV['DB_DATABASE'] ?? 'test';
    }

    /** Connect to MySQL server (without specifying DB yet) */
    private function connectServer() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']}",
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("DB Connection Error: " . $e->getMessage());
        }
    }

    /** Create database if not exists */
    private function createDatabaseIfNotExists() {
        $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    }

    /** Connect to the actual database */
    private function connectDatabase() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$this->dbName}",
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("DB Connection Error: " . $e->getMessage());
        }
    }

    /** Get PDO connection */
    public function getConnection() {
        return $this->pdo;
    }

    /** Sync all models: create/alter tables only */
    public function syncModels($modalPath = null) {
        $modalPath = $modalPath ?: realpath(MODAL_PATH);

        foreach (glob($modalPath . '/*.php') as $file) {
            $class = 'Backend\\Modal\\' . basename($file, '.php');
            if (class_exists($class) || require_once $file) {
                $model = new $class();
                if (method_exists($model, 'dbTable')) {
                    $def = $model->dbTable();
                    $this->syncTable($def);
                }
            }
        }
    }

    /** Sync a single table (create/alter) */
    private function syncTable($def) {
        $table = $def['table'];
        $columns = $def['columns'] ?? [];

        // Create table if not exists
        $cols = [];
        foreach ($columns as $col => $type) {
            $cols[] = "`$col` $type";
        }
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(',', $cols) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->pdo->exec($sql);

        // Add missing columns
        $existingCols = $this->getColumns($table);
        foreach ($columns as $col => $type) {
            if (!in_array($col, $existingCols)) {
                $this->pdo->exec("ALTER TABLE `$table` ADD COLUMN `$col` $type;");
            }
        }
    }

    /** Seed all models */
    public function syncSeeds($modalPath = null) {
        $modalPath = $modalPath ?: realpath(MODAL_PATH);

        foreach (glob($modalPath . '/*.php') as $file) {
            $class = 'Backend\\Modal\\' . basename($file, '.php');
            if (class_exists($class) || require_once $file) {
                $model = new $class();
                if (method_exists($model, 'dbTable')) {
                    $def = $model->dbTable();
                    $this->seedTable($def);
                }
            }
        }
    }

    /** Seed a single table: insert new rows or update only changed values */
    private function seedTable($def) {
        $table = $def['table'];
        $columns = $def['columns'] ?? [];
        $seeds = $def['seeds'] ?? [];

        foreach ($seeds as $row) {
            $where = [];

            // Use id if exists, else first UNIQUE column as identifier
            if (isset($row['id'])) {
                $where['id'] = $row['id'];
            } else {
                foreach ($columns as $col => $type) {
                    if (stripos($type, 'UNIQUE') !== false && isset($row[$col])) {
                        $where[$col] = $row[$col];
                        break;
                    }
                }
            }

            if (empty($where)) {
                // No unique key, insert new row
                $this->insertRow($table, $row);
                continue;
            }

            // Check existing row
            $conditions = [];
            foreach ($where as $col => $val) {
                $conditions[] = "`$col` = :$col";
            }

            $stmt = $this->pdo->prepare("SELECT * FROM `$table` WHERE " . implode(' AND ', $conditions));
            $stmt->execute($where);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Compare and update only changed columns
                $update = [];
                $params = [];
                foreach ($row as $col => $val) {
                    if (!array_key_exists($col, $existing) || $existing[$col] != $val) {
                        $update[] = "`$col` = :$col";
                        $params[$col] = $val;
                    }
                }
                if (!empty($update)) {
                    $stmtUpdate = $this->pdo->prepare(
                        "UPDATE `$table` SET " . implode(',', $update) . " WHERE " . implode(' AND ', $conditions)
                    );
                    $stmtUpdate->execute(array_merge($params, $where));
                }
            } else {
                // Insert new row
                $this->insertRow($table, $row);
            }
        }
    }

    /** Helper: insert row with prepared statement */
    private function insertRow($table, $row) {
        $cols = array_keys($row);
        $placeholders = array_map(fn($c) => ":$c", $cols);
        $sql = "INSERT INTO `$table` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($row);
    }

    /** Get existing columns */
    private function getColumns($table) {
        $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `$table`");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
