<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Forms {
    private $conn;
    private $table = "forms";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySubjectId($subject_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE subject_id = :id and in_used = 1");
        $stmt->execute(['id' => $subject_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data) {

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (subject_id, form_questions, in_used, created_at) VALUES (:subject_id, :form_questions, :in_used, :created_at)");
        return $stmt->execute([
            'subject_id' => $data['subject_id'],
            'form_questions' => json_encode($data['form_questions'], JSON_UNESCAPED_UNICODE),
            'in_used' => $data['in_used'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET name = :name, email = :email WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
