<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Subjects
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }


    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM subjects ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getBySubjectId($subjectId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM subjects WHERE subject_id = :id");
        $stmt->execute(['id' => $subjectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        // ตรวจสอบความถูกต้องของสถานะก่อนบันทึก
        $status = ($status === 'Y') ? 'Y' : 'N';

        $sql = "UPDATE subjects SET is_active = :status WHERE subject_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }
}
