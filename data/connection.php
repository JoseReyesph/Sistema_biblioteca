<?php

if (file_exists(__DIR__ . '/../config/env.php')) {
    require_once __DIR__ . '/../config/env.php';
}

class Connection {
    private $server;
    private $user;
    private $password;
    private $database;
    private $charset;
    private $pdo;

    public function __construct() {
        $this->server   = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $this->user     = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->database = $_ENV['DB_NAME'] ?? '';
        $this->charset  = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
    }

    public function new_connection() {
        try {
            $dsn = "mysql:host={$this->server};dbname={$this->database};charset={$this->charset}";

            $this->pdo = new PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->pdo;
        } catch (PDOException $e) {
            echo "Error de conexion: " . $e->getMessage();
            return null;
        }
    }

    public function close_conection() {
        $this->pdo = null;
    }
}
