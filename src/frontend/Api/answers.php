<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Models\Answers;

// instance API
$answersModel = new Answers();

// ตรวจสอบ method
$method = $_SERVER['REQUEST_METHOD'];

// ดึง id จาก URL (ถ้ามี) เช่น /answers.php?id=5
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$sectionofferId = isset($_GET['sec']) ? intval($_GET['sec']) : null;
$studentCode = isset($_GET['student']) ? intval($_GET['student']) : null;


// อ่าน input data (POST/PUT)
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        if ($id) {
            $result = $answersModel->getBySubjectId($id);
        } else if ($sectionofferId && $studentCode) {
            $result = $answersModel->getPointBySectionAndStudent($sectionofferId, $studentCode);
        } else {
            $result = $answersModel->getAll();
        }
        echo json_encode($result);
        break;

    case 'POST':
        if (!$input) {
            http_response_code(400);
            echo json_encode(['status' => "error", 'message' => 'No data provided']);
            exit;
        }
       
        // print_r($input);
        // exit;
        $result = $answersModel->save($input);
        if ($result) {
            echo json_encode(['status' => "success", 'message' => 'Answer saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => "error", 'message' => 'Failed to save answer']);
        }
        break;

    case 'PUT': 
        if (!$id || !$input) {
            http_response_code(400);
            echo json_encode(['status' => "error", 'message' => 'Missing id or data']);
            exit;
        }
        $result = $api->updateAnswer($id, $input);
        echo json_encode($result);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => "error", 'message' => 'Missing id']);
            exit;
        }
        $result = $api->deleteAnswer($id);
        echo json_encode($result);
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => "error", 'message' => 'Method not allowed']);
        break;
}
