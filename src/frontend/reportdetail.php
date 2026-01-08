<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Subjects;
use App\Models\Answers;
use App\Models\Forms;

$subjectsModel = new Subjects();
$answersModel = new Answers();
$formsModel = new Forms();

$subjectId = $_GET['subid'] ?? null;
$section = $_GET['sec'] ?? null;
$term = $_GET['term'] ?? null;
$year = $_GET['year'] ?? null;

$subject = $subjectsModel->getBySubjectId($subjectId);
$form = $formsModel->getBySubjectId($subjectId);

if (!$subjectId || !$section || !$term || !$year || !$subject || !$form) {
    echo "<div class='alert alert-danger'>ไม่พบข้อมูลรายวิชาหรือแบบประเมิน กรุณาตรวจสอบลิงก์อีกครั้ง</div>";
    exit;
}

$answers = $answersModel->getReportDetail($subjectId, $section, $term, $year);
if (empty($answers)) {
    echo "<div class='alert alert-warning'>ยังไม่มีข้อมูลการประเมินสำหรับรายวิชานี้</div>";
    exit;
}

$student_array = [];
$comment_array = [];

$rewdata = '[';
foreach ($answers as $key => $answer) {
  $student_array[] = $answer['student_code'];

  if ($answer['comments'] !== '' && trim($answer['comments']) !== '-')
    $comment_array[] = $answer['comments'];

  $rewdata .= $answer['answers'];
  if ($key < count($answers) - 1) {
    $rewdata .= ',';
  }
}
$rewdata .= ']';

// echo "<pre>"; print_r($comment_array); echo "</pre>";
// exit;
?>

<div id="reportArea" class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 bg-white p-3 rounded shadow">
      <div class="card-title mt-3 mb-4 text-center">
        <h5><?php echo htmlspecialchars($subject['code']) . ' : ' . htmlspecialchars($subject['thainame']); ?></h5>
        <div><small><?php echo htmlspecialchars($subject['englishname']); ?></small></div>
        <div>ปีการศึกษา <?php echo $term . '/' . $year; ?></div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th style="width: 40%">หัวข้อประเมิน</th>
              <th>1</th>
              <th>2</th>
              <th>3</th>
              <th>4</th>
              <th>5</th>
              <th>ค่าเฉลี่ย</th>
              <th>SD</th>
            </tr>
          </thead>
          <tbody id="reportTableBody"></tbody>
        </table>
        <p style="padding-bottom: 10px;"><i>หมายเหตุ &nbsp;&nbsp; 1 = ไม่พึงพอใจมากที่สุด, 2 = ไม่พึงพอใจ, 3 = ปานกลาง, 4 = พึงพอใจมาก, 5 = พึงพอใจมากที่สุด</i></p>
      </div>

      <div>
        <h5 class="mt-4">รหัสนักศึกษาที่ทำประเมิน</h5>
        <div>
          <b>จำนวน <?php echo count($student_array); ?> คน : </b>
          <p style="text-align: justify;">
          <?php echo implode(', ', $student_array); ?>
          </p>
        </div>
      </div>

      <div>
        <h5 class="mt-4">ความคิดเห็นและข้อเสนอแนะเพิ่มเติม</h5>
        <div>
          <?php
          if (empty($comment_array)) {
            echo "<div class='alert'>ไม่มีความคิดเห็นเพิ่มเติม</div>";
          } else {
            echo "<ol class='mb-3'>";
            foreach ($comment_array as $c) {
              echo "<li style='word-wrap: break-word; text-align: justify;'>" . htmlspecialchars($c) . "</li>";
            }
            echo "</ol>";
          }
          ?>
        </div>
      </div>

      <div class="text-center mb-3">
        <a href="print.php?subid=<?php echo $subjectId.'&sec='.$section.'&term='.$term.'&year='.$year; ?>" target="_blank" class="btn btn-danger">🖨️ Prints / Download PDF</a>
      </div>
    </div>
  </div>
</div>

<script>
  // 🔹 Formatter ตัวเลขให้สวย (ทศนิยม 2 ตำแหน่ง)
  const numFormat = new Intl.NumberFormat("th-TH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  // 🔹 คำถาม + หมวดหมู่
  const questions = <?php echo $form['form_questions']; ?>;

  // คำตอบ
  const rawData = <?php echo $rewdata; ?>;

  // 🔹 ฟังก์ชันคำนวณสถิติ
  function calculateStats(values) {
    const frequencies = [0, 0, 0, 0, 0];
    values.forEach(v => {
      frequencies[v - 1]++;
    });

    const meanRaw = values.reduce((a, b) => a + b, 0) / values.length;
    const variance = values.reduce((a, b) => a + Math.pow(b - meanRaw, 2), 0) / values.length;
    const sdRaw = Math.sqrt(variance);

    console.log(values.length);
    return {
      frequencies,
      mean: numFormat.format(meanRaw),
      sd: numFormat.format(sdRaw)
    };
  }

  // 🔹 แปลง rawData ให้เป็น column (แต่ละคำถาม)
  const cols = Array(questions.length).fill().map((_, colIndex) =>
    rawData.map(row => row[colIndex])
  );

  // 🔹 Group คำถามตาม topic
  const grouped = {};
  questions.forEach((q, idx) => {
    if (!grouped[q.topic]) grouped[q.topic] = [];
    grouped[q.topic].push({
      ...q,
      values: cols[idx]
    });
  });

  // 🔹 สร้างตาราง
  const tbody = document.getElementById("reportTableBody");
  let allValues = [];
  let index = 1;
  Object.entries(grouped).forEach(([topic, items]) => {
    // แถวหัวข้อ topic
    const trTopic = document.createElement("tr");
    trTopic.classList.add("table-secondary");
    trTopic.innerHTML = `<td colspan="8" class="fw-bold text-start">${topic}</td>`;
    tbody.appendChild(trTopic);

    // เรียงตาม order
    items.sort((a, b) => a.order - b.order);

    // รายละเอียดคำถามใน topic นั้น
    let topicValues = [];
    items.forEach((q) => {
      const stats = calculateStats(q.values);
      topicValues = topicValues.concat(q.values);
      allValues = allValues.concat(q.values);

      const tr = document.createElement("tr");
      tr.innerHTML = `
          <td class="text-start">${index}. ${q.text}</td>
          ${stats.frequencies.map(f => `<td>${f}</td>`).join("")}
          <td><b>${stats.mean}</b></td>
          <td>${stats.sd}</td>
        `;
      tbody.appendChild(tr);
      index++;
    });

    // สรุปผลรวมของ topic
    const summaryStats = calculateStats(topicValues);
    const trSummary = document.createElement("tr");
    trSummary.classList.add("table-info");
    trSummary.innerHTML = `
        <td class="text-start fw-bold">สรุปผลรวม: ${topic}</td>
        ${summaryStats.frequencies.map(f => `<td>${f}</td>`).join("")}
        <td><b>${summaryStats.mean}</b></td>
        <td>${summaryStats.sd}</td>
      `;
    tbody.appendChild(trSummary);
  });

  // 🔹 สรุปผลรวมทุกหัวข้อ
  const overallStats = calculateStats(allValues);
  const trOverall = document.createElement("tr");
  trOverall.classList.add("table-success");
  trOverall.innerHTML = `
      <td class="text-start fw-bold">สรุปผลรวมทุกหัวข้อ</td>
      ${overallStats.frequencies.map(f => `<td>${f}</td>`).join("")}
      <td><b>${overallStats.mean}</b></td>
      <td>${overallStats.sd}</td>
    `;
  tbody.appendChild(trOverall);

</script>
