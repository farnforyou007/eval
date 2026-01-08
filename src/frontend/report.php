<?php
require_once __DIR__ . '/../../vendor/autoload.php';
;
use App\Models\Answers;

$answersModel = new Answers();

$answers = $answersModel->getReport();
//  echo "<pre>"; print_r($answers); echo "</pre>";  
//  exit;

?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-10 bg-white p-3 rounded shadow">
      <div class="card-title mt-3 mb-4 text-center">
        <h5>ผลการประเมิน</h5>
        <div>ปีการศึกษา 1/2568</div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th style="width: 50%;">ชื่อรายวิชา</th>
              <th>section</th>
              <th>term</th>
              <th>year</th>
              <th>จำนวนคนประเมิน</th>
              <th>คะแนนประเมิน</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($answers as $row) : ?>
              <tr>
                <td style="text-align: left;"><?php echo htmlspecialchars($row['code']) . ' ' . htmlspecialchars($row['thainame']) .'<br><small>'
                 . htmlspecialchars($row['englishname']) . '</small>'; ?>
              </td>
                <td><?php echo htmlspecialchars($row['section']); ?></td>
                <td><?php echo htmlspecialchars($row['term']); ?></td>
                <td><?php echo htmlspecialchars($row['year']); ?></td>
                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                <td><a href="reportdetail?subid=<?php echo $row['subject_id'].'&sec='.$row['section'].'&term='.$row['term'].'&year='.$row['year']; ?>"><?php echo number_format($row['avg'], 2); ?></a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
     
    </div>
  </div>
</div>

