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


$answers = $answersModel->getReportDetail($subjectId, $section, $term, $year);
//  echo "<pre>"; print_r($answers); echo "</pre>";  
//  exit;

$student_array = [];
$comment_array = [];

$rewdata = '[';
foreach ($answers as $key => $answer) {
  $student_array[] = $answer['student_code'];

  if ($answer['comments'])
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
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ผลการประเมิน <?php echo htmlspecialchars($subject['code']) . '_' .$section. '_' . $term . $year; ?></title>

  <!-- ✅ ใช้ Printed.css -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/printed-css@1.1.0/dist/printed.min.css">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sarabun:300,400,500,700" />
  <style>
    /* กำหนดขนาดกระดาษ */
    @page {
      size: A4 portrait;
      margin: 10mm;
    }

    /* ========================
   ✅ สไตล์รายงาน
   ======================== */

    body {
      font-family: 'Sarabun', cursive;
      font-size: 10pt;
      color: #111;
      background: #fff;
    }

    .report-wrapper {
      max-width: 210mm;
      margin: 0 auto;
      padding: 5mm;
      box-sizing: border-box;
    }

    .header {
      text-align: center;
      border-bottom: 2px solid #444;
      padding-bottom: 5px;
      margin-bottom: 5px;
    }

    .header h1 {
      font-size: 12pt;
      margin: 0;
    }

    .header h2 {
      font-size: 11pt;
      color: #444;
      margin-top: 5px;
    }

    /* ✅ ตาราง */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5mm;
      font-size: 10pt;
    }

    th,
    td {
      border: 1px solid #333;
      padding: 6px 8px;
      text-align: center;
    }
    th.title, td.text-start {
      width: 51%;
      text-align: left;
    }

    th {
      background-color: #f4f4f4;
      font-weight: bold;
    }

    tr:nth-child(even) td {
      background-color: #fafafa;
    }

   ol {
      padding-left: 20px;
    }

    ol li {
      margin-bottom: 6px;
    }

    .text-topic, .table-info, .table-success{
      font-weight: bold;    
    }

  </style>
</head>

<body class="printed A4 portrait">

  <div class="report-wrapper">
    <div class="header">
      <h1><?php echo htmlspecialchars($subject['code']) . ' : ' . htmlspecialchars($subject['thainame']); ?></h1>
      <h2><?php echo htmlspecialchars($subject['englishname']); ?></h2>
      <h2>ปีการศึกษา <?php echo $term . '/' . $year; ?></h2>
    </div>

    <table>
      <thead>
        <tr>
          <th>หัวข้อประเมิน</th>
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

  <div>
    <h3>รหัสนักศึกษาที่ทำประเมิน</h3>
    <div>
      <p><b style="font-size: 11pt;">จำนวน <?php echo count($student_array); ?> คน : </b></p>
      <p><?php echo implode(', ', $student_array); ?></p>
    </div>
  </div>

  <div>
    <h3>ความคิดเห็นและข้อเสนอแนะเพิ่มเติม</h3>
    <div>
      <?php
      if (empty($comment_array)) {
        echo "<div>ไม่มีความคิดเห็นเพิ่มเติม</div>";
      } else {
        echo "<ol>";
        foreach ($comment_array as $c) {
          echo "<li>" . htmlspecialchars($c) . "</li>";
        }
        echo "</ol>";
      }
      ?>
    </div>
  </div>

  </div>
</body>

</html>


<script>

window.addEventListener('load', function() {
    // ตั้งเวลาเล็กน้อยเผื่อโหลดเนื้อหาเสร็จ
    setTimeout(() => {
        window.print();
    }, 500);
});

  //Formatter ตัวเลขให้สวย (ทศนิยม 2 ตำแหน่ง)
  const numFormat = new Intl.NumberFormat("th-TH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  //คำถาม + หมวดหมู่
  const questions = <?php echo $form['form_questions']; ?>;

  // คำตอบ
  const rawData = <?php echo $rewdata; ?>;

  //ฟังก์ชันคำนวณสถิติ
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
    trTopic.innerHTML = `<td colspan="8" class="text-topic">${topic}</td>`;
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
          <td>${stats.mean}</td>
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
        <td class="text-start">สรุปผลรวม: ${topic}</td>
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
      <td class="text-start"">สรุปผลรวมทุกหัวข้อ</td>
      ${overallStats.frequencies.map(f => `<td>${f}</td>`).join("")}
      <td><b>${overallStats.mean}</b></td>
      <td>${overallStats.sd}</td>
    `;
  tbody.appendChild(trOverall);
</script>