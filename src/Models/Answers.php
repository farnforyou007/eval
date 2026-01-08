<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Answers
{
    private $conn;
    private $table = "answers";

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
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE subject_id = :id");
        $stmt->execute(['id' => $subject_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPointBySectionAndStudent($sectionofferId, $studentCode)
    {

        $eduYear = $sectionofferId ? substr($sectionofferId, 0, 4) : null;
        $eduTerm = $sectionofferId ? substr($sectionofferId, 4, 1) : null;
        $subjectId = $sectionofferId ? substr($sectionofferId, 5, 7) : null;
        $section = $sectionofferId ? substr($sectionofferId, 16, 2) : null;

        $stmt = $this->conn->prepare("SELECT point FROM {$this->table} WHERE student_code = :studentCode AND subject_id = :subjectId AND section = :section AND term = :eduTerm AND year = :eduYear");
        $stmt->execute([
            'studentCode' => $studentCode,
            'subjectId' => $subjectId,
            'section' => $section,
            'eduTerm' => $eduTerm,
            'eduYear' => $eduYear
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getReport()
    {
        $sql = "SELECT COUNT(DISTINCT a.student_code) AS amount, a.subject_id, a.section, a.term, a.year, AVG(a.point) AS avg,
                    b.code, b.thainame, b.englishname
            FROM answers a
            JOIN subjects b ON a.subject_id = b.subject_id
            GROUP BY a.subject_id, a.section, a.term, a.year, b.code, b.thainame, b.englishname
            ORDER BY b.code, a.section, a.term, a.year ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReportDetail($subject_id, $section, $term, $year)
    {
        $sql = "SELECT * FROM answers
            WHERE subject_id = :subject_id AND section = :section AND term = :term AND year = :year
            GROUP BY student_code
            ORDER BY student_code ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['subject_id' => $subject_id, 'section' => $section, 'term' => $term, 'year' => $year]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($data)
    {

        $comment = $data["comment"] ?? "";

        // เอาเฉพาะ key ที่เป็นคำถาม q1, q2, q3...
        $answers = array_filter($data, function ($key) {
            return preg_match('/^q[0-9]+$/', $key);
        }, ARRAY_FILTER_USE_KEY);

        // คำนวณค่าเฉลี่ย
        $total = array_sum($answers);
        $count = count($answers);
        $average = $count > 0 ? round($total / $count, 2) : 0;

        $score = '[' . implode(',', $answers) . ']';

        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (student_code, subject_id, section, term, year, form_id, answers, point, comments, created_at) VALUES (:student_code, :subject_id, :section, :term, :year, :form_id, :answers, :point, :comments, :created_at)");
        return $stmt->execute([
            'student_code' => $data['student_code'],
            'subject_id' => $data['subject_id'],
            'section' => $data['section'],
            'term' => $data['term'],
            'year' => $data['year'],
            'form_id' => $data['form_id'],
            'answers' => $score,
            'point' => $average,
            'comments' => $comment,
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
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
