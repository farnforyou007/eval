//const API_URL = 'https://api-gateway.psu.ac.th:8443/Central/GetTitleAll';

//student detail
const API_URL = 'https://api-gateway.psu.ac.th:8443/regist/SIS/StudentDetailCampus/01/Adv?Filter=admitYear=2568,stillStudent=Y,studyLevelId=06&facID=53&Page=1&PageSize=200&OrderBy=studentId';

// student
//const API_URL = 'https://api-gateway.psu.ac.th:8443/regist/SIS/v3/registData/all/01/1/2568?facId=53&studentId=6611410005&offset=0&limit=100';

//subject
//const API_URL = 'https://api-gateway.psu.ac.th:8443/regist/v3/subject/offer/01/1/2568?facID=53&offset=0&limit=20';

//subject all
//const API_URL = 'https://api-gateway.psu.ac.th:8443/regist/v3/subject/detail/01?facId=53&offset=0&limit=500';


(function () {
    'use strict';

   fetchData();
})();

function fetchData() {
    // let sheetParam = 'Users';
    // let idParam = searchParams.get('id');
    axios.get(`${API_URL}`, {
        headers: {
            "Content-Type": "application/json; charset=UTF-8",
            "credential": "api_key=hrTNaZ1BH6NQs8ZDzVpCKeYkgYx0gw2+&app_secret=tEJ9CSK10V66IHZBITBafnYLnZLc1tpGAA==",
             "scopes": "01.53.*"
           },
        })
        .then(response => {
            console.log('GET Response:', response.data);
           // ดึงข้อมูลจาก API response
           const objs = response.data;

       
           console.log(objs["data"]);
           displayData(objs["data"]);
        })
        .catch(error => {
            console.error('GET Error:', error);
           
        });

}

// ฟังก์ชันในการแสดงข้อมูลใน HTML
function displayData(posts) {
    const container = document.getElementById('data-container');

    let sql = 'INSERT INTO subject(`subject_id`, `code`, `thainame`, `englishname`, `is_active`) VALUES'
    posts.forEach(post => {
        // สร้าง element ใหม่สำหรับแต่ละ post
        const postElement = document.createElement('pre');
        //postElement.innerHTML = `'${post.subjectId}', ${post.subjectCode}, ${post.subjectNameThai} (${post.shortNameEng}), ${post.isClosed}`;
        postElement.innerHTML = `'${post.studentId}, '${post.citizenId}, ${post.titleNameThai}, ${post.studNameThai}, ${post.studSnameThai}, ${post.admitDate}, ${post.gradDate}`;

        // เพิ่ม post element เข้าไปใน container
        container.appendChild(postElement);
    });
}
