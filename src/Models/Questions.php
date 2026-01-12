<?php

namespace App\Models;

use App\Config\Database;
use PDO;

// src/Models/Questions.php
class Questions
{
    private $conn;
    private $table = "questions";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM {$this->table} ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getforsave()
    {
        // $stmt = $this->conn->query("SELECT subject_id FROM {$this->table} where subject_id in('0030496','0030501','0030498','0030500','0028475') GROUP BY subject_id");
        $stmt = $this->conn->query("SELECT subject_id FROM {$this->table} where subject_id in('0022954' , '0022959' , '0022960' ) GROUP BY subject_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySubjectId($subjectId)
    {
        $stmt = $this->conn->prepare("SELECT id, question_text as 'text', topic, question_order as 'order' 
                                    FROM {$this->table} 
                                    WHERE subject_id = :subjectId 
                                    ORDER BY question_order");
        $stmt->execute(['subjectId' => $subjectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function syncQuestions($subject_id, $q_ids, $texts, $topics)
    {
        try {
            $this->conn->beginTransaction();

            // 1. หา ID ทั้งหมดที่ยังเหลืออยู่จากการส่งมาในหน้าจอ
            $active_ids = array_filter($q_ids, fn($id) => is_numeric($id));

            // 2. ลบข้อที่ไม่มีอยู่ในรายการที่ส่งมา (โดนกดลบกากบาทในหน้าจอ)
            if (!empty($active_ids)) {
                $placeholders = implode(',', array_fill(0, count($active_ids), '?'));
                $deleteStmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE subject_id = ? AND id NOT IN ($placeholders)");
                $deleteStmt->execute(array_merge([$subject_id], $active_ids));
            } else {
                // ถ้าไม่ส่ง ID มาเลย แสดงว่าลบหมดเกลี้ยง
                $this->conn->prepare("DELETE FROM {$this->table} WHERE subject_id = ?")->execute([$subject_id]);
            }

            // 3. วนลูปเพื่อ Update ของเก่า หรือ Insert ของใหม่
            foreach ($texts as $index => $text) {
                $current_id = $q_ids[$index];
                $order = $index + 1;
                $topic = $topics[$index];

                if (is_numeric($current_id)) {
                    // ข้อเดิมที่มีอยู่แล้ว -> UPDATE (รักษา ID เดิมไว้)
                    $stmt = $this->conn->prepare("UPDATE {$this->table} SET question_text = ?, topic = ?, question_order = ? WHERE id = ?");
                    $stmt->execute([$text, $topic, $order, $current_id]);
                } else {
                    // ข้อที่เพิ่มใหม่ (ID = 'new') -> INSERT
                    $stmt = $this->conn->prepare("INSERT INTO {$this->table} (subject_id, question_text, topic, question_order) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$subject_id, $text, $topic, $order]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
