<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../helpers/debug.php'; // ตรวจสอบ Path ให้ถูกต้อง
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
        // if (!$input) {
        //     http_response_code(400);
        //     echo json_encode(['success' => false, 'message' => 'No data provided']);
        //     exit;
        // }

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

        // if ($action === 'add_subject') {
        //     // คุณอาจเพิ่ม Logic ตรวจสอบว่า subject_id ซ้ำหรือไม่ก่อนบันทึก
        //     // $result = $subjectsModel->add($input);
        //     // echo json_encode(['success' => $result, 'message' => $result ? 'เพิ่มรายวิชาสำเร็จ' : 'เพิ่มรายวิชาล้มเหลว']);
        //     // exit;
        //     header('Content-Type: application/json'); // บังคับให้เป็น JSON
        //     $result = $subjectsModel->add($input);
        //     echo json_encode([
        //         'success' => $result,
        //         'message' => $result ? 'เพิ่มรายวิชาสำเร็จ' : 'เพิ่มรายวิชาล้มเหลว'
        //     ]);
        //     exit;
        // }

        if ($action === 'add_subject') {
            header('Content-Type: application/json');

            // 1. ตรวจสอบก่อนว่ามี Subject ID นี้ในฐานข้อมูลแล้วหรือยัง
            // เราสามารถใช้ฟังก์ชันที่มีอยู่แล้วใน Model (เช่น getBySubjectId) มาเช็คได้
            $existing = $subjectsModel->getBySubjectId($input['subject_id']);

            if ($existing) {
                // หากพบข้อมูลซ้ำ ให้ส่ง error กลับไปทันที ไม่ต้องทำขั้นตอนบันทึก
                echo json_encode([
                    'success' => false,
                    'message' => 'ไม่สามารถเพิ่มได้ เนื่องจากมีรหัส Subject ID นี้ในระบบแล้ว'
                ]);
                exit;
            }

            // 2. ถ้าไม่ซ้ำ ถึงจะทำการบันทึกข้อมูล
            try {
                $result = $subjectsModel->add($input);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'เพิ่มรายวิชาสำเร็จ' : 'เพิ่มรายวิชาล้มเหลว'
                ]);
            } catch (Exception $e) {
                // เผื่อกรณีเกิด Error อื่นๆ ใน Database
                echo json_encode([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ]);
            }
            exit;
        }
        // ส่วนของ sync_preview ใน SubjectsApi.php
        if ($action === 'sync_preview') {
            // ปิดการแสดง Error ชั่วคราวไม่ให้หลุดไปปนกับ JSON
            error_reporting(0);
            ini_set('display_errors', 0);

            try {
                $api_url = "https://api-gateway.psu.ac.th:8443/regist/v3/subject/detail/01?facId=53&offset=0&limit=500";
                $ch = curl_init($api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json; charset=UTF-8",

                    "credential: api_key=hrTNaZ1BH6NQs8ZDzVpCKeYkgYx0gw2+&app_secret=tEJ9CSK10V66IHZBITBafnYLnZLc1tpGAA==",
                    "scopes: 01.53.*"
                ]);
                $response = curl_exec($ch);
                $apiData = json_decode($response, true);

                // สำคัญ: ข้อมูลวิชาอยู่ที่ Key 'data'
                $subjectsFromApi = $apiData['data'] ?? [];

                $allInDB = $subjectsModel->getAll();
                $existingIds = array_column($allInDB, 'subject_id');

                $newItems = [];
                foreach ($subjectsFromApi as $item) {
                    // ดักกรอง: ข้ามข้อมูลที่มีรหัสเป็น 000-000 หรือชื่อเป็น 000 (ขยะจาก API)
                    if ($item['subjectCode'] === '000-000' || $item['subjectNameThai'] === '000') {
                        continue;
                    }

                    // ตรวจสอบว่ามีวิชานี้ใน DB หรือยัง
                    if (!in_array($item['subjectId'], $existingIds)) {
                        $newItems[] = [
                            'subject_id'  => (string)$item['subjectId'], // ใช้ subjectId (I ตัวใหญ่)
                            'code'        => $item['subjectCode'],     // ใช้ subjectCode
                            'thainame'    => $item['subjectNameThai'], // ใช้ subjectNameThai
                            'englishname' => $item['subjectNameEng'],  // ใช้ subjectNameEng
                            'study_level' => $item['studyLevelName']   // ใช้ studyLevelName
                        ];
                    }
                }

                // ล้าง Output Buffer และส่ง JSON
                header('Content-Type: application/json');
                echo json_encode([
                    'success'   => true,
                    'new_count' => count($newItems),
                    'items'     => array_values($newItems)
                ]);
                exit;
            } catch (Exception $e) {
                if (ob_get_length()) ob_clean();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        if ($action === 'sync_api') {
            try {
                $subjectsToSync = $input['subjects'] ?? [];
                $added = $subjectsModel->syncFromApi($subjectsToSync);

                echo json_encode(['success' => true, 'added' => $added]);
            } catch (Exception $e) {
                // หาก Database พัง ให้ส่งเป็น JSON บอก JavaScript แทนการพ่น HTML Error ออกมา
                echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
            }
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
