<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Questions {
    private $conn;
    private $table = "questions";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM {$this->table} ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getforsave() {
        $stmt = $this->conn->query("SELECT subject_id FROM {$this->table} where subject_id in('0030496','0030501','0030498','0030500','0028475') GROUP BY subject_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySubjectId($subjectId) {
        $stmt = $this->conn->prepare("SELECT id, question_text as 'text', topic, question_order as 'order' FROM {$this->table} WHERE subject_id = :subjectId ORDER BY question_order");
        $stmt->execute(['subjectId' => $subjectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
