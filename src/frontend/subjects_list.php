<style>
    .subject-card .card-body {
        display: flex;
        flex-direction: column;
        height: 100%;
        /* บังคับให้ body สูงเต็มพื้นที่การ์ด */
    }

    /* ปรับแต่งความนุ่มนวลและโทนสี */
    .subject-card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-radius: 20px;
        border: 1px solid #FFE0B2;
        background-color: #FFF5E9;
        height: 100%;
        /* บังคับให้การ์ดทุกใบสูงเท่ากันในแถวเดียวกัน */
    }

    /* เอฟเฟกต์ตอน Hover */
    .subject-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(255, 167, 38, 0.15);
        background-color: #FFFFFF !important;
        border-color: #FFB74D;
    }

    /* ปุ่มแก้ไขไอคอนดินสอสีน้ำตาลเข้ม (ไม่แสบตา) */
    .btn-edit-soft {
        background-color: #4E342E;
        color: #FFF5E9;
        border: none;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .btn-edit-soft:hover {
        background-color: #E65100;
        color: white;
        transform: rotate(15deg);
    }

    .search-input-group {
        border-radius: 15px;
        background: #F5F5F5;
        border: 1px solid #EEEEEE;
    }
</style>

<div class="container mt-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold" style="color: #4E342E;">จัดการแบบประเมินรายวิชา</h2>
            <p style="color: #8D6E63;">เลือกรายวิชาที่ต้องการตรวจสอบหรือแก้ไขข้อมูล</p>
        </div>
        <div class="col-md-6">
            <div class="input-group search-input-group p-1 shadow-sm">
                <span class="input-group-text bg-transparent border-0">
                    <i class="bi bi-search" style="color: #A1887F;"></i>
                </span>
                <input type="text" id="subjectSearch" class="form-control bg-transparent border-0" placeholder="ค้นหาตามรหัสหรือชื่อวิชา...">
            </div>
        </div>
    </div>

    <div class="row g-4" id="subjectGrid">
        <?php if (!empty($subjects)): ?>
            <?php foreach ($subjects as $row): ?>
                <div class="col-md-6 col-lg-4 subject-item">
                    <div class="card h-100 shadow-sm subject-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge px-3 py-2" style="background-color: #FFE0B2; color: #E65100; border-radius: 10px;">
                                    <?= htmlspecialchars($row['code']) ?>
                                </span>
                                <span class="text-muted small">SubjectID: <?= htmlspecialchars($row['subject_id']) ?></span>
                            </div>

                            <h5 class="fw-bold mb-1" style="color: #3E2723;">
                                <?= htmlspecialchars($row['thainame']) ?>
                            </h5>
                            <p class="small mb-4" style="color: #795548;">
                                <?= htmlspecialchars($row['englishname']) ?>
                            </p>

                            <!-- <div class="d-flex justify-content-end align-items-center mt-auto">
                                <button class="btn btn-edit-soft rounded-circle shadow-sm" title="แก้ไขวิชานี้">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div> -->
                            <div class="d-flex justify-content-end align-items-center mt-auto">
                                <a href="edit_questions?subid=<?= htmlspecialchars($row['subject_id']) ?>"
                                    class="btn btn-edit-soft rounded-circle shadow-sm" title="แก้ไขวิชานี้">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="noResultContainer" class="d-none">
        <div id="noResult" class="d-flex flex-column align-items-center justify-content-center" style="min-height: 40vh;">
            <i class="bi bi-search-heart display-1" style="color: #D7CCC8;"></i>
            <h4 class="mt-4" style="color: #8D6E63;">ไม่พบรหัสหรือชื่อวิชาที่คุณค้นหา</h4>
            <p class="text-muted">ตรวจสอบตัวสะกด หรือใช้คำค้นหาอื่นแทน</p>
        </div>
    </div>
</div>

<!-- <script>
    // ระบบค้นหาอัตโนมัติ (Auto-filter) พร้อมแสดงข้อความเมื่อไม่พบข้อมูล
    document.getElementById('subjectSearch').addEventListener('input', function() {
        let filter = this.value.toLowerCase().trim();
        let items = document.querySelectorAll('.subject-item');
        let hasVisible = false;

        items.forEach(item => {
            let text = item.innerText.toLowerCase();
            if (text.includes(filter)) {
                item.style.display = "";
                hasVisible = true;
            } else {
                item.style.display = "none";
            }
        });

        // ตรวจสอบเพื่อแสดง/ซ่อนข้อความ "ไม่พบข้อมูล"
        const noResult = document.getElementById('noResult');
        if (!hasVisible && filter !== "") {
            noResult.classList.remove('d-none');
        } else {
            noResult.classList.add('d-none');
        }
    });
</script> -->

<script>
    document.getElementById('subjectSearch').addEventListener('input', function() {
        let filter = this.value.toLowerCase().trim();
        let items = document.querySelectorAll('.subject-item');
        let hasVisible = false;

        items.forEach(item => {
            let text = item.innerText.toLowerCase();
            if (text.includes(filter)) {
                item.style.display = "";
                hasVisible = true;
            } else {
                item.style.display = "none";
            }
        });

        // จัดการการแสดงผลข้อความ "ไม่พบข้อมูล"
        const noResultCont = document.getElementById('noResultContainer');
        if (!hasVisible && filter !== "") {
            noResultCont.classList.remove('d-none');
        } else {
            noResultCont.classList.add('d-none');
        }
    });
</script>