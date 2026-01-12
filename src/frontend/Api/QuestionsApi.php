<?php

// namespace App\Api;

// header("Content-Type: application/json; charset=UTF-8");
// require_once __DIR__ . '/../../../vendor/autoload.php';



// use App\Models\Questions;
// use App\Models\Forms;

// $questionsModel = new Questions();
// $formsModel = new Forms();
// $method = $_SERVER['REQUEST_METHOD'];

// if ($method === 'POST') {

//     $subject_id = $_POST['subject_id'] ?? null;
//     $q_texts = $_POST['question_text'] ?? [];
//     $topics = $_POST['topic'] ?? [];

//     if (!$subject_id) {
//         echo json_encode(['success' => false, 'message' => 'Missing Subject ID']);
//         exit;
//     }


//     $formData = [];
//     foreach ($q_texts as $index => $text) {
//         $formData[] = [
//             'topic' => $topics[$index],
//             'text' => $text,
//             'order' => $index + 1
//         ];
//     }


//     $result = $formsModel->saveNewVersion($subject_id, $formData);

//     if ($result) {
//         echo json_encode(['success' => true, 'message' => 'บันทึกเวอร์ชันใหม่เรียบร้อยแล้ว']);
//     } else {
//         echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
//     }
//     exit;
// }


// if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'rollback') {
//     $fid = $_GET['fid'];
//     $sid = $_GET['sid'];

//     echo json_encode(['success' => true]);
//     exit;
// }
// class QuestionsApi
// {
//     private $qustionsModel;

//     public function __construct()
//     {
//         $this->qustionsModel = new Questions();
//     }

//     public function listQuestions()
//     {
//         return $this->qustionsModel->getAll();
//     }

//     public function questionBySubject($id)
//     {
//         return $this->qustionsModel->getBySubjectId($id);
//     }
// }



header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Models\Questions;
use App\Models\Forms;

$questionsModel = new Questions();
$formsModel = new Forms();

$method = $_SERVER['REQUEST_METHOD'];

// รองรับทั้งการส่งแบบ JSON และแบบ FormData
$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    $input = $_POST;
}

switch ($method) {
    case 'GET':
        $action = $_GET['action'] ?? null;
        $subjectId = $_GET['subid'] ?? $_GET['id'] ?? null;
        $action = $_GET['action'] ?? null;

        // ใหม่: สำหรับดึงข้อมูล Preview
        if ($action === 'get_version_detail') {
            $fid = $_GET['fid'] ?? null;  // Form ID
            $stmt = $formsModel->getById($fid);

            if ($stmt) {
                $questions = json_decode($stmt['form_questions'], true);

                // จัดกลุ่มคำถามตามหมวดหมู่ (Topic)
                $grouped = [];
                foreach ($questions as $q) {
                    $grouped[$q['topic']][] = $q;
                }

                echo json_encode(['success' => true, 'data' => $grouped]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลเวอร์ชันนี้']);
            }
            exit;
        }

        // เพิ่มในไฟล์ QuestionsApi.php ส่วนจัดการ action
        if ($action === 'delete_version') {
            $fid = $_GET['fid'] ?? null;
            // เรียกใช้ฟังก์ชันลบจาก Model
            if ($formsModel->delete($fid)) {
                echo json_encode(['success' => true, 'message' => 'ลบประวัติเวอร์ชันเรียบร้อย']);
            } else {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบได้']);
            }
            exit;
        }

        if ($action === 'rollback') {
            $fid = $_GET['fid'] ?? null;
            $sid = $_GET['sid'] ?? null;  // Subject ID
            if (!$fid || !$sid) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน (fid/sid)']);
                exit;
            }
            $result = $formsModel->rollback($sid, $fid);
            echo json_encode(['success' => $result, 'message' => $result ? 'เปลี่ยนเวอร์ชันเรียบร้อย' : 'ล้มเหลว']);
        } else if ($subjectId) {
            $result = $questionsModel->getBySubjectId($subjectId);
            echo json_encode($result);
        } else {
            $result = $questionsModel->getAll();
            echo json_encode($result);
        }
        break;

    case 'POST':
        $subject_id = $input['subject_id'] ?? null;
        $q_ids = $input['question_id'] ?? [];
        $q_texts = $input['question_text'] ?? [];
        $topics = $input['topic'] ?? [];

        // รับค่าหมายเหตุที่ส่งมาจาก SweetAlert/FormData
        $note = $input['save_note'] ?? '';

        if (!$subject_id || empty($q_texts)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }

        // 1. Sync ข้อมูลลงตารางหลัก (ตามที่เราคุยกันก่อนหน้านี้)
        $questionsModel->syncQuestions($subject_id, $q_ids, $q_texts, $topics);

        // 2. ดึงข้อมูลล่าสุดมาทำ JSON
        $latestQuestions = $questionsModel->getBySubjectId($subject_id);
        $formData = [];
        foreach ($latestQuestions as $q) {
            $formData[] = [
                'id'    => (int)$q['id'],
                'text'  => $q['text'],
                'topic' => $q['topic'],
                'order' => (int)$q['order']
            ];
        }

        // 3. บันทึกลงตาราง forms (ส่ง $note เข้าไปด้วย)
        // ปรับให้ส่งอาเรย์ที่มีทั้งข้อมูลคำถามและหมายเหตุ
        $saveData = [
            'subject_id' => $subject_id,
            'form_questions' => $formData,
            'note' => $note, // <--- ตรวจสอบว่ามีบรรทัดนี้
            'in_used' => 1
        ];

        if ($formsModel->saveNewVersion($subject_id, $saveData)) {
            echo json_encode(['success' => true, 'message' => 'บันทึกพร้อมหมายเหตุเรียบร้อย']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'บันทึกล้มเหลว']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
