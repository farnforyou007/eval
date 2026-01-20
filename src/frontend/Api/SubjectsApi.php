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
$action = $_GET['action'] ?? null;

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
        // 1. ตรวจสอบว่ามีการส่งข้อมูลมาหรือไม่
        if (!$input) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No data provided']);
            exit;
        }

        // 2. ตรวจสอบ Action จาก URL (?action=update_status)
        if ($action === 'update_status') {
            $subject_id = $input['id'] ?? null;
            $status = $input['status'] ?? null;

            if (!$subject_id || !$status) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน (id/status)']);
                exit;
            }

            // เรียกใช้ Model เพื่ออัปเดตสถานะ (ส่งค่า 'Y' หรือ 'N')
            $result = $subjectsModel->updateStatus($subject_id, $status);

            echo json_encode(['success' => $result, 'message' => $result ? 'อัปเดตสำเร็จ' : 'อัปเดตล้มเหลว']);
            exit;
        }

        // 3. กรณีเป็น POST อื่นๆ ที่ยังไม่ได้ทำระบบรองรับ
        http_response_code(501); // Not Implemented
        echo json_encode(['success' => false, 'message' => 'Action not implemented']);
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
