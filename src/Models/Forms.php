<?php

namespace App\Models;

use App\Config\Database;
use PDO;

// src/Models/Forms.php
class Forms
{
    private $conn;
    private $table = "forms";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySubjectId($subject_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE subject_id = :id and in_used = 1");
        $stmt->execute(['id' => $subject_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (subject_id, form_questions, note, in_used, created_at) VALUES (:subject_id, :form_questions, :note, :in_used, :created_at)");
        return $stmt->execute([
            'subject_id' => $data['subject_id'],
            'form_questions' => json_encode($data['form_questions'], JSON_UNESCAPED_UNICODE),
            'note' => $data['note'] ?? null, // เพิ่มฟิลด์หมายเหตุ
            'in_used' => $data['in_used'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET name = :name, email = :email WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }
    
    public function delete($id)
    {
        // เพิ่มการเช็คเพื่อความปลอดภัย: ห้ามลบตัวที่ in_used = 1
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id AND in_used = 0");
        return $stmt->execute(['id' => $id]);
    }
    // เพิ่มฟังก์ชันนี้ใน class Forms
    public function getVersionsBySubjectId($subjectId)
    {
        // ดึงประวัติการบันทึก เรียงจากใหม่ไปเก่า
        $query = "SELECT id, created_at, in_used , note
              FROM " . $this->table . " 
              WHERE subject_id = :sid 
              ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['sid' => $subjectId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function rollback($subject_id, $form_id)
    {
        try {
            $this->conn->beginTransaction();
            // ปิดทุกตัว
            $this->conn->prepare("UPDATE {$this->table} SET in_used = 0 WHERE subject_id = ?")->execute([$subject_id]);
            // เปิดตัวที่เลือก
            $this->conn->prepare("UPDATE {$this->table} SET in_used = 1 WHERE id = ?")->execute([$form_id]);
            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function saveNewVersion($subject_id, $saveData)
    {
        try {
            $this->conn->beginTransaction();

            // ปิดการใช้งานตัวเก่า
            $updateStmt = $this->conn->prepare("UPDATE {$this->table} SET in_used = 0 WHERE subject_id = :sid");
            $updateStmt->execute(['sid' => $subject_id]);

            // บันทึกตัวใหม่ (ตรวจสอบว่า SQL ของคุณมีคอลัมน์ note หรือยัง)
            $stmt = $this->conn->prepare("INSERT INTO {$this->table} (subject_id, form_questions, note, in_used, created_at) VALUES (:subject_id, :form_questions, :note, :in_used, :created_at)");

            $stmt->execute([
                'subject_id' => $saveData['subject_id'],
                'form_questions' => json_encode($saveData['form_questions'], JSON_UNESCAPED_UNICODE),
                'note' => $saveData['note'] ?? null, // <--- ต้องมีตรงนี้
                'in_used' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT form_questions FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
