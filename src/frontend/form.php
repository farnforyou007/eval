<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Subjects;
use App\Models\Forms;

$subjectsModel = new Subjects();
$formsModel = new Forms();

$sectionOfferId = $_GET['sec'] ?? null;

$eduYear = $sectionOfferId ? substr($sectionOfferId, 0, 4) : null;
$eduTerm = $sectionOfferId ? substr($sectionOfferId, 4, 1) : null;
$subjectId = $sectionOfferId ? substr($sectionOfferId, 5, 7) : null;
$section = $sectionOfferId ? substr($sectionOfferId, 16, 2) : null;


$subject = $subjectsModel->getBySubjectId($subjectId);
$form = $formsModel->getBySubjectId($subjectId);

// Debug: บันทึกแบบฟอร์มใหม่สำหรับวิชาที่ต้องการ
// $questionsModel = new \App\Models\Questions();
// $temp = $questionsModel->getforsave();

// foreach($temp as $q) {
//   // echo $q['subject_id'];
//   $questions = $questionsModel->getBySubjectId($q['subject_id']);

//   $inputs = [
//   'subject_id' => $q['subject_id'],
//   'form_questions' => $questions,
//   'in_used' => 1
// ];
// $formsModel->save($inputs);
// }
// echo "<pre>";
// print_r($inputs);
// exit;

?>


<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 bg-white p-3 rounded shadow">
      <?php
      if (!$sectionOfferId || !$subject || !$form) :
        echo "<div class='alert alert-danger'>ไม่พบข้อมูลแบบประเมินสำหรับวิชานี้ หรือ กรุณาเลือกวิชาที่ต้องการทำแบบประเมิน</div>";
        exit;
      else:
      ?>
      <div class="card-title mt-3 mb-4">
        <h5><?php echo htmlspecialchars($subject['code']) . ' : ' . htmlspecialchars($subject['thainame']); ?></h5>
        <div><small><?php echo htmlspecialchars($subject['englishname']); ?></small></div>
      </div>

      <form id="surveyForm">
        <div id="questionsContainer"></div>

        <div class="mb-4">
          <label for="comment" class="form-label fw-bold">ความคิดเห็นเพิ่มเติม</label>
          <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="กรอกความคิดเห็นของคุณ..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-4">ส่งแบบประเมิน</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  const questions = <?php echo $form['form_questions']; ?>;

  const grouped = questions.reduce((acc, q) => {
    if (!acc[q.topic]) acc[q.topic] = [];
    acc[q.topic].push(q);
    return acc;
  }, {});

  // 🔹 Render ฟอร์ม
  const container = document.getElementById("questionsContainer");
  let questionIndex = 1; // ตัวนับ index

  Object.entries(grouped).forEach(([topic, qs]) => {
    const topicDiv = document.createElement("div");
    topicDiv.className = "mb-4";
    topicDiv.innerHTML = `<h5 class="text-primary mb-3">${topic}</h5>`;

    qs.forEach(q => {
      const qDiv = document.createElement("div");
      qDiv.className = "mb-3 p-3 border rounded bg-white";
      qDiv.innerHTML = `
      <label class="form-label fw-bold">${questionIndex}. ${q.text}</label>
      <div class="d-flex gap-3">
        ${[1,2,3,4,5].map(val => `
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" 
                   name="q${q.id}" id="q${q.id}_${val}" value="${val}" required>
            <label class="form-check-label" for="q${q.id}_${val}">
              ${val} = ${val === 1 ? "ไม่พึงพอใจมากที่สุด" :
                          val === 2 ? "ไม่พึงพอใจ" :
                          val === 3 ? "ปานกลาง" :
                          val === 4 ? "พึงพอใจมาก" :
                          "พึงพอใจมากที่สุด"}
            </label>
          </div>
        `).join("")}
      </div>
    `;
      topicDiv.appendChild(qDiv);
      questionIndex++; // เพิ่ม index
    });

    container.appendChild(topicDiv);
  });

  document.getElementById("surveyForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const answers = {};
    for (const [key, value] of formData.entries()) {
      answers[key] = value;
    }

    answers.subject_id = '<?php echo $subjectId; ?>';
    answers.section = '<?php echo $section; ?>';
    answers.year = <?php echo $eduYear; ?>;
    answers.term = '<?php echo $eduTerm; ?>';
    answers.form_id = '<?php echo $form['id']; ?>';
    answers.student_code = '<?php echo $userdata['psu_id']; ?>';


    //console.log(answers);

    fetch("/Api/answers.php", {
        method: "POST",
        body: JSON.stringify(answers),
        headers: {
          "Content-Type": "application/json"
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: "ส่งแบบประเมินเรียบร้อย ✅",
            text: "ขอบคุณสำหรับความร่วมมือของคุณ",
            timer: 3000,
            showConfirmButton: false
          }).then(() => {
            window.location.href = '/main';
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "เกิดข้อผิดพลาด ❌",
            text: data.message || "ไม่สามารถบันทึกข้อมูลได้"
          });
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire({
          icon: "error",
          title: "เชื่อมต่อเซิร์ฟเวอร์ไม่ได้ ❌",
          text: "กรุณาลองใหม่อีกครั้ง"
        });
      });
  });
</script>