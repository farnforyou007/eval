<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Models\Subjects;


// instance API
$subjectsModel = new Subjects();

// ตรวจสอบ method
$method = $_SERVER['REQUEST_METHOD'];

// ดึง id จาก URL (ถ้ามี) เช่น /subjects.php?id=5
$id = isset($_GET['id']) ? $_GET['id'] : null;


// อ่าน input data (POST/PUT)
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        if ($id) {
            $result = $subjectsModel->getBySubjectId($id);
        } else {
            $result = $subjectsModel->getAll();
        }
        echo json_encode($result);
        break;

    case 'POST':
        if (!$input) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'No data provided']);
            exit;
        }

        echo json_encode(['status' => false, 'message' => 'Not implemented']);
        // $result = $api->saveAnswer($input);
        // echo json_encode($result);
        break;

    case 'PUT':
        if (!$id || !$input) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Missing id or data']);
            exit;
        }
        $result = $api->updateAnswer($id, $input);
        echo json_encode($result);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Missing id']);
            exit;
        }
        $result = $api->deleteAnswer($id);
        echo json_encode($result);
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => false, 'message' => 'Method not allowed']);
        break;
}
