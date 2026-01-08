<?php

//คำนวณว่ากำลังอยู่ในเทอมอะไร
function term_calculate() {
	    $month = intval(date("m"));

	    $term_arr = array();
		if($month > 6 and $month <= 10){
			// First Term
			$term_arr["eduTerm"] = 1;
			$term_arr["eduYear"] = (date("Y")+543);
		}
		elseif($month >= 1 and $month <= 4 or $month > 10 and $month <= 12){
			// Second Term
			$term_arr["eduTerm"] = 2;
			$term_arr["eduYear"] = (date("Y")+542);

		} 
		else{
			// Summer Term
			$term_arr["eduTerm"] = 3;
			$term_arr["eduYear"] = (date("Y")+542);
		}
		return $term_arr;
   
}

$term_info = term_calculate();
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 bg-white p-3 rounded shadow">
      <div class="card-title mt-3 mb-4">
      <h3>รายวิชาที่ต้องทำประเมินในปีการศึกษา <?php echo $term_info["eduYear"]; ?></h3>
      <h6>กรุณาเลือกภาคการศึกษาที่ต้องการทำประเมิน</h6>
      </div>
      <ul class="nav nav-tabs mb-3" id="courseTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link <?php echo $term_info["eduTerm"] == 1 ? 'active' : ''; ?>" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">1/<?php echo $term_info["eduYear"]; ?></button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link <?php echo $term_info["eduTerm"] == 2 ? 'active' : ''; ?>" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">2/<?php echo $term_info["eduYear"]; ?></button>
      </li>
      </ul>

      <div class="tab-content" id="courseTabContent">
      <div class="tab-pane fade <?php echo $term_info["eduTerm"] == 1 ? 'show active' : ''; ?>" id="all" role="tabpanel">
        <div id="data-container1"></div>
      </div>
      <div class="tab-pane fade <?php echo $term_info["eduTerm"] == 2 ? 'show active' : ''; ?>" id="completed" role="tabpanel">
        <div id="data-container2"></div>
      </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  //const STUDENT_ID = '<?php echo $userdata['psu_id']; ?>';
  const eduYear = <?php echo $term_info["eduYear"]; ?>;
  //debug
  const STUDENT_ID = '6611410002'; // ทดสอบ

  //const API_URL = `https://api-gateway.psu.ac.th:8443/regist/SIS/v3/registData/all/01/1/2568?facId=53&studentId=${STUDENT_ID}&offset=0&limit=100`;



  (function() {
    'use strict';

    fetchData(1, eduYear, STUDENT_ID, 'data-container1'); // ภาคเรียนที่ 1
    fetchData(2, eduYear, STUDENT_ID, 'data-container2'); // ภาคเรียนที่ 2

  })();

  function fetchData(term, year, studentId, containerId) {

    let API_URL = `https://www.ttmed.psu.ac.th/psuapi/registdata.php?studentId=${studentId}&term=${term}&year=${year}`;

    axios.get(`${API_URL}`, {
        headers: {
          "Content-Type": "application/json; charset=UTF-8",
        },
      })
      .then(response => {
         //console.log('GET Response:', response.data);
        // ดึงข้อมูลจาก API response
        const objs = response.data;

        displayData(objs['data'], containerId);
      })
      .catch(error => { 
        //console.error('GET Error:', error);
        const container = document.getElementById(containerId);
        const alertDiv = document.createElement('div');
        alertDiv.classList.add('alert', 'alert-warning');
        alertDiv.textContent = 'ไม่พบรายวิชาที่ต้องทำประเมิน หรือไม่สามารถดึงข้อมูลรายวิชาได้กรุณาลองใหม่อีกครั้ง.';
        container.appendChild(alertDiv);

      });

  }


  // ฟังก์ชันในการแสดงข้อมูลใน HTML
  function displayData(posts, containerId) {
    const container = document.getElementById(containerId);

    posts.forEach(post => {
      // สร้าง element ใหม่สำหรับแต่ละ post
      const postElement = document.createElement('div');
      postElement.classList.add('card', 'mb-3'); // เพิ่มคลาส card และ mb-3 (margin bottom)

      // สร้าง card body
      const cardBody = document.createElement('div');
      cardBody.classList.add('card-body');

      // สร้าง title ของ card
      const cardTitle = document.createElement('h5');
      cardTitle.classList.add('card-title');
      cardTitle.textContent = post.subjectCode + ' ' + post.subjectNameThai;

      const cardSubtitle = document.createElement('h6');
      cardSubtitle.classList.add('small');
      cardSubtitle.textContent = post.subjectNameEng;

       const URL = `Api/answers.php?sec=${post.sectionOfferId}&student=${STUDENT_ID}`;
       
      axios.get(`${URL}`, {
          headers: {
            "Content-Type": "application/json; charset=UTF-8",
          },
        })
        .then(response => {
          console.log('GET Response:', response.data);
          if (response.data) {
            //มีผลการประเมินแล้ว
            const cardText = document.createElement('p');
            cardText.classList.add('card-text');
            cardText.classList.add('mt-2');
            cardText.classList.add('text-primary');
            cardText.textContent = `ผลการประเมิน: ${response.data["point"]}`;
            cardBody.appendChild(cardText);

          } else {
            //ไม่มีผลการประเมิน แสดงปุ่มให้ทำแบบประเมิน
            const linkButton = document.createElement('a');
            linkButton.href = 'form/?sec=' + post.sectionOfferId;
            linkButton.textContent = 'ทำแบบประเมิน';
            linkButton.classList.add('btn', 'btn-primary', 'btn-sm'); 
            cardBody.appendChild(linkButton);
          }

        })
        .catch(error => {
          console.error('GET Error:', error);

        });


      // เพิ่ม title และ text ลงใน card body
      cardBody.appendChild(cardTitle);
      cardBody.appendChild(cardSubtitle);

      if (post.sectionOfferId && post.sectionOfferId.includes('0161')) {
        // เพิ่ม card body ลงใน post element
        postElement.appendChild(cardBody);

        // เพิ่ม post element เข้าไปใน container
        container.appendChild(postElement);
      }


    });
  }
</script>