<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host;
    private $db;
    private $user;
    private $pass;
    public $conn;

    // public function __construct() {
    //     $this->host = 'mariadb';
    //     $this->db   = 'eval';
    //     $this->user = 'root';
    //     $this->pass = 'OQs>ta5pkEsn';
    //     $this->connect();
    // }

    public function __construct() {
        $this->host = 'db';
        $this->db   = 'eval';
        $this->user = 'root';
        $this->pass = 'rootpassword';
        $this->connect();
    }

    private function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db}",
                $this->user,
                $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
