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
    public function add($data)
    {
        $sql = "INSERT INTO subjects (subject_id, code, thainame, englishname,study_level, is_active) 
            VALUES (:subject_id, :code, :thainame, :englishname,:study_level, :is_active)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':subject_id'  => $data['subject_id'],
            ':code'        => $data['code'],
            ':thainame'    => $data['thainame'],
            ':englishname' => $data['englishname'],
            ':study_level' => $data['study_level'],
            ':is_active'   => $data['is_active']
        ]);
    }

    // ซฺิงค์ข้อมูลรายวิชาจาก API มหาลัย
    public function syncFromApi($data)
    {
        // เตรียม SQL (อย่าลืมเพิ่มคอลัมน์ study_level ใน DB)
        $sql = "INSERT IGNORE INTO subjects (subject_id, code, thainame, englishname, study_level, is_active) 
            VALUES (:subject_id, :code, :thainame, :englishname, :study_level, 'Y')";
        $stmt = $this->conn->prepare($sql);
        $count = 0;

        foreach ($data as $item) {
            $result = $stmt->execute([
                ':subject_id'  => $item['subject_id'],  // ชื่อ Key ต้องตรงกับที่ Map ไว้ข้างบน
                ':code'        => $item['code'],
                ':thainame'    => $item['thainame'],
                ':englishname' => $item['englishname'],
                ':study_level' => $item['study_level']
            ]);
            if ($result && $stmt->rowCount() > 0) $count++;
        }
        return $count;
    }
}
