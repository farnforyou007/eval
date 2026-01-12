<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Subjects;

$subjectsModel = new Subjects();

session_start();

// if (isset($_SESSION['user_psusso'])) {
//     $userdata = $_SESSION['user_psusso'];
// }else{
//     header('Location: https://www.ttmed.psu.ac.th/th/evaluate');
//     exit();
// }

//debug
$userdata = array('email' => 'niti.c@example.com', 'display_name_th' => 'ผู้ใช้ ตัวอย่าง', 'username' => 'niti.c', 'psu_id' => '6710210474', 'usertype' => 'staff');


function render($view, $params = [])
{
    extract($params);
    include $view;
}

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($path === '') {
    $page = $userdata['usertype'] == 'staff' ? 'report' : 'main';
} else {
    $page = $path;
}

// กำหนด params เฉพาะแต่ละหน้า
switch ($page) {
    case 'main':
        $params = [
            'siteName' => 'นักศึกษาทำประเมิน',
            'page'     => 'main.php',
        ];
        break;
    case 'form':
        $params = [
            'siteName' => 'แบบประเมิน',
            'page'     => 'form.php',
            'info'     => 'แบบฟอร์มการประเมิน',
        ];
        break;
    case 'report':
        $params = [
            'siteName' => 'Report',
            'page'     => 'report.php',
            'email'    => 'contact@company.com',
        ];
        break;
    case 'reportdetail':
        $params = [
            'siteName' => 'Report Detail',
            'page'     => 'reportdetail.php'
        ];
        break;
    case 'subjects':
        $params = [
            'siteName' => 'รายชื่อรายวิชา',
            'page'     => 'subjects_list.php', // เราจะสร้างไฟล์นี้ในขั้นตอนถัดไป
            'subjects' => $subjectsModel->getAll() // ส่งข้อมูลที่ดึงได้ไปที่หน้าเว็บ
        ];
        break;
    case 'edit_questions':
        $subjectId = $_GET['subid'] ?? null;
        $questionsModel = new \App\Models\Questions();
        $subjectsModel = new \App\Models\Subjects();

        $params = [
            'siteName' => 'จัดการคำถามแบบประเมิน',
            'page'     => 'edit_questions.php',
            'subject'  => $subjectsModel->getBySubjectId($subjectId), // ดึงชื่อวิชา
            'questions' => $questionsModel->getBySubjectId($subjectId) // ดึงคำถาม
        ];
        break;
    default:
        $params = [
            'siteName' => '404',
            'page'     => '404.php',
        ];
}


$templateFile = __DIR__ . "/template.php";

// Pass $userdata into render function
$params['userdata'] = $userdata;

render($templateFile, $params);
