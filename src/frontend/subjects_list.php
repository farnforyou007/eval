<link rel="stylesheet" href="subjects_list.css">

<?php
$subjectsModel = new \App\Models\Subjects();

$allSubjects = $subjectsModel->getAll();
// ใช้ array_column เพื่อดึงเฉพาะฟิลด์ study_level และ array_unique เพื่อเอาค่าที่ไม่ซ้ำกัน
$study_levels = array_unique(array_column($allSubjects, 'study_level'));

// กรองค่าว่าง (null หรือ string ว่าง) ออกจากรายการ
$study_levels = array_filter($study_levels, function ($value) {
    return !empty($value);
});

?>
<?php
// ฟังก์ชันช่วยเลือกสีตามระดับการศึกษา
function getStudyLevelBadge($level)
{
    $level = $level ?? '';
    $badgeClass = 'bg-secondary'; // สีเทาเริ่มต้น

    if (mb_strpos($level, 'ปริญญาตรี') !== false) {
        $badgeClass = 'bg-success'; // สีเขียว
    } elseif (mb_strpos($level, 'ปริญญาโท') !== false) {
        $badgeClass = 'bg-primary'; // สีน้ำเงิน
    } elseif (mb_strpos($level, 'ปริญญาเอก') !== false) {
        $badgeClass = 'bg-info';    // สีฟ้าอ่อน
    }

    $colorName = str_replace('bg-', '', $badgeClass);
    return sprintf(
        '<span class="badge rounded-pill %s bg-opacity-10 text-%s border border-%s border-opacity-25 px-3">%s</span>',
        $badgeClass,
        $colorName,
        $colorName,
        htmlspecialchars($level ?: '-')
    );
}
?>

<div class="container mt-4 mb-5">

    <div class="row align-items-center mb-3">
        <div class="col">
            <h2 class="fw-bold mb-1" style="color: #1e293b; letter-spacing: -0.5px;">จัดการแบบประเมินรายวิชา</h2>
            <p class="text-secondary mb-0 fs-6">จัดการเนื้อหาคำถามและตรวจสอบสถานะรายวิชาทั้งหมดในระบบ</p>
        </div>
        <div class="col-auto d-flex gap-2"> <button id="btnSync" onclick="handleSyncWithPreview()"
                class="btn btn-outline-secondary px-4 py-2 rounded-4 shadow-sm fw-medium d-flex align-items-center gap-2"
                style="border-color: #e2e8f0; color: #475569; background-color: #ffffff;">
                <i class="bi bi-arrow-repeat fs-5"></i>
                ดึงข้อมูลจากมหาลัย
            </button>

            <button id="btnAddSubject"
                class="btn btn-dark px-4 py-2 rounded-4 shadow-sm fw-medium d-flex align-items-center gap-2"
                style="background-color: #334155; border: none;">
                <i class="bi bi-plus-circle-fill fs-5"></i>
                เพิ่มรายวิชาใหม่
            </button>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-auto">
            <div class="d-inline-flex gap-1 bg-white border p-1 rounded-4 shadow-sm" style="min-height: 48px;">
                <button class="btn btn-sm px-4 rounded-pill filter-btn active" data-filter="all">ทั้งหมด</button>
                <button class="btn btn-sm px-4 rounded-pill filter-btn" data-filter="active">
                    <span class="status-dot status-active me-1"></span> เปิดสอน
                </button>
                <button class="btn btn-sm px-4 rounded-pill filter-btn" data-filter="inactive">
                    <span class="status-dot status-inactive me-1"></span> ไม่เปิดสอน
                </button>
            </div>
        </div>

        <div class="col-md-auto">
            <select id="levelFilter" class="form-select shadow-sm h-100 rounded-4 border-0 px-3"
                style="min-width: 200px; border: 1px solid #e2e8f0 !important; color: #334155; font-weight: 500;">
                <option value="all">ทุกระดับการศึกษา</option>
                <?php if (!empty($study_levels)): ?>
                    <?php foreach ($study_levels as $level): ?>
                        <option value="<?= htmlspecialchars($level) ?>"><?= htmlspecialchars($level) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-md">
            <div class="search-wrapper shadow-sm h-100">
                <i class="bi bi-search fs-5"></i>
                <input type="text" id="subjectSearch" class="search-input-modern" placeholder="ค้นหารหัสวิชา, ชื่อภาษาไทย หรือชื่อภาษาอังกฤษ...">
            </div>
        </div>
    </div>

    <div class="table-container shadow-sm mt-2">
        <div class="d-flex justify-content-between align-items-center pt-4 pb-3 px-4">

            <div class="text-muted small">
                <i class="bi bi-list-ul me-1"></i>
                แสดงทั้งหมด <span id="totalItems" class="fw-bold text-dark">0</span> รายวิชา
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">แสดง</span>
                <select id="itemsPerPageSelect" class="form-select form-select-sm shadow-sm"
                    style="width: 70px; border-radius: 8px; border-color: #e2e8f0;">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-muted small">แถวต่อหน้า</span>
            </div>

        </div>

        <div class="table-container shadow-sm mt-2">
            <div class="table-responsive-custom">
                <table class="table modern-table" id="subjectTable">
                    <thead>
                        <tr>
                            <th style="width: 5%;" class="text-center">ลำดับ</th>
                            <th style="width: 15%;" class="text-start">รหัสวิชา</th>
                            <th style="width: 43%;" class="text-start">ชื่อรายวิชา / สถานะ</th>
                            <th style="width: 11%;" class="text-start">Subject ID</th>
                            <th style="width: 11%;" class="text-center">ระดับ</th>
                            <th style="width: 15%;" class="text-end pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="subjectGrid">
                        <?php if (!empty($subjects)): $index = 1; ?>
                            <?php foreach ($subjects as $row):
                                $isActive = $row['is_active'] ?? 'Y';
                                $statusText = ($isActive === 'Y') ? 'active' : 'inactive';
                            ?>
                                <tr class="subject-item" data-status="<?= $statusText ?>">
                                    <td class="text-center text-muted small row-index"></td>
                                    <td><span class="code-badge border-0"><?= htmlspecialchars($row['code']) ?></span></td>
                                    <td class="text-start">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['thainame']) ?></div>
                                        <div class="text-muted small">
                                            <span class="status-dot <?= $isActive === 'Y' ? 'status-active' : 'status-inactive' ?>"></span>
                                            <?= htmlspecialchars($row['englishname']) ?>
                                        </div>
                                    </td>
                                    <td class="text-start">
                                        <span class="text-muted fw-light subject-badge"><?= htmlspecialchars($row['subject_id']) ?></span>
                                    </td>
                                    <td class="text-center ">
                                        <div class="mb-1">
                                            <?= getStudyLevelBadge($row['study_level'] ?? '') ?>
                                        </div>

                                    </td>

                                    <!-- <td class="text-end pe-4">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <div class="form-check form-switch m-0 p-0">
                                                <input class="form-check-input status-toggle-input m-0 custom-switch"

                                                    type="checkbox"
                                                    role="switch"
                                                    id="status_<?= $row['subject_id'] ?>"
                                                    data-id="<?= $row['subject_id'] ?>"
                                                    <?= ($row['is_active'] === 'Y') ? 'checked' : '' ?>
                                                    title="<?= ($row['is_active'] === 'Y') ? 'ปิดใช้งาน' : 'เปิดใช้งาน' ?>">
                                            </div>

                                            <a href="edit_questions?subid=<?= htmlspecialchars($row['subject_id']) ?>"
                                                class="btn-edit-modern shadow-sm custom-tooltip"
                                                data-tooltip="แก้ไขแบบประเมิน">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <i class="bi bi-trash-fill"></i>
                                        </div>


                                    </td> -->

                                    <td class="text-end pe-4">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <div class="form-check form-switch m-0 p-0">
                                                <input class="form-check-input status-toggle-input m-0 custom-switch"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="status_<?= $row['subject_id'] ?>"
                                                    data-id="<?= $row['subject_id'] ?>"
                                                    <?= ($row['is_active'] === 'Y') ? 'checked' : '' ?>
                                                    title="<?= ($row['is_active'] === 'Y') ? 'เปิดใช้งาน' : 'ปิดใช้งาน' ?>">
                                            </div>


                                            <a href="edit_questions?subid=<?= htmlspecialchars($row['subject_id']) ?>"

                                                class="btn-edit-modern shadow-sm custom-tooltip"
                                                data-tooltip="แก้ไขแบบประเมิน">

                                                <i class="bi bi-journal-text"></i>
                                            </a>


                                            <button type="button"
                                                class="btn-edit-subject-modern shadow-sm custom-tooltip"
                                                data-id="<?= htmlspecialchars($row['subject_id']) ?>"
                                                data-code="<?= htmlspecialchars($row['code']) ?>"
                                                data-thainame="<?= htmlspecialchars($row['thainame']) ?>"
                                                data-englishname="<?= htmlspecialchars($row['englishname']) ?>"
                                                data-level="<?= htmlspecialchars($row['study_level'] ?? '') ?>"
                                                data-active="<?= $row['is_active'] ?>"
                                                data-tooltip="แก้ไขรายวิชานี้"
                                                onclick="handleEditSubject(this)"> <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <button type="button"
                                                class="btn-delete-modern shadow-sm custom-tooltip"
                                                data-id="<?= htmlspecialchars($row['subject_id']) ?>"
                                                data-tooltip="ลบรายวิชานี้"
                                                onclick="confirmDelete('<?= htmlspecialchars($row['subject_id']) ?>')">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center py-4 border-top bg-light bg-opacity-10">
            <ul class="pagination pagination-modern mb-0" id="paginationWrapper"></ul>
        </div>
    </div>

    <div id="noResultContainer" class="d-none border-top">
        <div class="d-flex flex-column align-items-center justify-content-center py-5">
            <i class="bi bi-search display-4 text-light-emphasis opacity-25"></i>
            <h5 class="mt-3 text-secondary">ไม่พบรายวิชาที่คุณค้นหา</h5>
            <p class="small text-muted">ลองตรวจสอบตัวสะกดหรือใช้คำค้นหาอื่น</p>
        </div>
    </div>
</div>
</div>



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

<script>
    const searchInput = document.getElementById('subjectSearch');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const subjectItems = Array.from(document.querySelectorAll('.subject-item'));
    const itemsPerPageSelect = document.getElementById('itemsPerPageSelect');
    const urlParams = new URLSearchParams(window.location.search);

    let currentPage = parseInt(urlParams.get('p')) || 1;
    let rowsPerPage = 10; // หรือตามที่คุณตั้งค่าไว้
    // let currentPage = 1;
    let itemsPerPage = parseInt(itemsPerPageSelect.value);
    // let filteredItems = [];
    let filteredItems = [...subjectItems];
    let currentFilter = 'all';

    const levelFilter = document.getElementById('levelFilter');

    function applyFilter() {
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedLevel = levelFilter.value;

        filteredItems = subjectItems.filter(item => {
            const itemStatus = item.getAttribute('data-status');
            const itemText = item.innerText.toLowerCase();

            // 1. เช็คคำค้นหา (Search)
            const matchesSearch = itemText.includes(searchText);

            // 2. เช็คสถานะ เปิด/ปิด (Filter เดิม)
            const matchesStatus = (currentFilter === 'all' || itemStatus === currentFilter);

            // 3. เช็คระดับการศึกษา (Dropdown ใหม่)
            const matchesLevel = (selectedLevel === 'all' || itemText.includes(selectedLevel.toLowerCase()));

            return matchesSearch && matchesStatus && matchesLevel;
        });

        // currentPage = 1;
        // renderTable();

        if (searchText !== "" || selectedLevel !== "all" || currentFilter !== "all") {
            currentPage = 1;
        } else {
            // ดึงจาก URL อีกครั้งเพื่อความชัวร์ตอนโหลดครั้งแรก
            const urlP = new URLSearchParams(window.location.search).get('p');
            currentPage = parseInt(urlP) || currentPage;
        }

        renderTable();
    }

    // ผูก Event ให้ทำงานเมื่อเปลี่ยนค่าใน Dropdown
    levelFilter.addEventListener('change', applyFilter);

    function updateUrlPage(page) {
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('p', page);
        window.history.pushState({
            page: page
        }, '', newUrl);
    }

    // function renderTable() {
    //     const start = (currentPage - 1) * itemsPerPage;
    //     const end = start + itemsPerPage;
    //     const editLink = `edit_questions?subid=${item.subject_id}&p=${currentPage}`;
    //     subjectItems.forEach(item => item.style.display = 'none');
    //     const pageItems = filteredItems.slice(start, end);
    //     pageItems.forEach((item, index) => {
    //         item.style.display = '';
    //         // คำนวณลำดับที่ถูกต้อง: (หน้าปัจจุบัน-1) * จำนวนต่อหน้า + (ลำดับในหน้านั้น+1)
    //         const globalIndex = start + index + 1;
    //         item.querySelector('.row-index').innerText = globalIndex;
    //     });

    //     document.getElementById('totalItems').innerText = filteredItems.length;

    //     renderPaginationControls();
    //     updateUrlPage(currentPage);

    // }

    function renderTable() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        subjectItems.forEach(item => item.style.display = 'none');
        const pageItems = filteredItems.slice(start, end);

        // ถ้ามีข้อมูลให้แสดงผล
        if (pageItems.length > 0) {
            document.getElementById('noResultContainer').classList.add('d-none');
            pageItems.forEach((item, index) => {
                item.style.display = '';

                // อัปเดต Link ปุ่มแก้ไขให้จำหน้า p
                const editBtn = item.querySelector('a[href*="edit_questions"]');
                if (editBtn) {
                    let currentHref = editBtn.getAttribute('href');
                    let baseUrl = currentHref.split('&p=')[0];
                    editBtn.setAttribute('href', `${baseUrl}&p=${currentPage}`);
                }

                const globalIndex = start + index + 1;
                const rowIndexEl = item.querySelector('.row-index');
                if (rowIndexEl) rowIndexEl.innerText = globalIndex;
            });
        } else {
            document.getElementById('noResultContainer').classList.remove('d-none');
        }

        // อัปเดตตัวเลขจำนวนวิชาทั้งหมดที่กรองได้
        document.getElementById('totalItems').innerText = filteredItems.length;

        renderPaginationControls();
        updateUrlPage(currentPage); // บรรทัดนี้จะทำให้ URL จำหน้า p
    }

    // ฟังก์ชันเปลี่ยนจำนวนแถว
    itemsPerPageSelect.addEventListener('change', function() {
        itemsPerPage = parseInt(this.value);
        currentPage = 1; // กลับไปหน้า 1 เมื่อเปลี่ยนจำนวนแถว
        renderTable();
    });

    function renderPaginationControls() {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        const wrapper = document.getElementById('paginationWrapper');
        wrapper.innerHTML = '';
        if (totalPages <= 1) return;

        // ปุ่ม Previous
        wrapper.innerHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link shadow-none" href="javascript:void(0)" onclick="changePage(${currentPage - 1})"><i class="bi bi-chevron-left"></i></a>
            </li>`;

        // ปุ่มเลขหน้า (Logic เดิมแต่ทำให้สวยขึ้น)
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                wrapper.innerHTML += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link shadow-none" href="javascript:void(0)" onclick="changePage(${i})">${i}</a>
                    </li>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                wrapper.innerHTML += `<li class="page-item disabled"><span class="page-link border-0 bg-transparent">...</span></li>`;
            }
        }

        // ปุ่ม Next
        wrapper.innerHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link shadow-none" href="javascript:void(0)" onclick="changePage(${currentPage + 1})"><i class="bi bi-chevron-right"></i></a>
            </li>`;
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
        document.querySelector('.table-container').scrollIntoView({
            behavior: 'smooth'
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            applyFilter();
        });
    });
    searchInput.addEventListener('input', applyFilter);
    applyFilter();
</script>

<script>
    document.querySelectorAll('.status-toggle-input').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const subjectId = this.dataset.id;
            const isChecked = this.checked;
            const newStatus = isChecked ? 'Y' : 'N';

            // หาแถว (tr) ที่ปุ่มนี้สังกัดอยู่ เพื่อไปเปลี่ยนสีจุดในแถวนั้น
            const row = this.closest('tr');
            const statusDot = row.querySelector('.status-dot');

            // แสดงสถานะกำลังโหลด (Feedback)
            this.style.opacity = '0.5';

            fetch('Api/SubjectsApi.php?action=update_status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: subjectId,
                        status: newStatus
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const row = this.closest('tr');
                        const statusDot = row.querySelector('.status-dot');
                        if (statusDot) {
                            if (newStatus === 'Y') {
                                statusDot.classList.remove('status-inactive');
                                statusDot.classList.add('status-active');
                            } else {
                                statusDot.classList.remove('status-active');
                                statusDot.classList.add('status-inactive');
                            }
                        }
                        const statusText = (newStatus === 'Y') ? 'active' : 'inactive';
                        row.setAttribute('data-status', statusText);

                        // --- 3. รันฟังก์ชัน Filter ใหม่เพื่อให้แถวหายไป/ปรากฏตามหมวดหมู่ที่เลือก ---
                        // ให้เรียกชื่อฟังก์ชันที่คุณใช้กรองข้อมูล (เช่น filterSubjects หรือ applyFilters)
                        if (typeof applyFilters === 'function') {
                            applyFilters();
                        }
                        Swal.fire({
                            icon: 'success',
                            title: isChecked ? 'เปิดใช้งานแล้ว' : 'ปิดใช้งานแล้ว',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        this.checked = !isChecked; // คืนค่าปุ่มถ้า Error
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: data.message
                        });
                    }
                })
                .catch(err => {
                    this.checked = !isChecked;
                    console.error('Error:', err);
                })
                .finally(() => {
                    this.style.opacity = '1';
                });
        });
    });

    document.getElementById('btnAddSubject').addEventListener('click', async function() {
        const {
            value: formValues
        } = await Swal.fire({
            title: '<span class="fs-4 fw-bold">เพิ่มรายวิชาใหม่</span>',
            html: `
            <div class="container-fluid text-start mt-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">Subject ID</label>
                        <input id="swal-subjectid" class="form-control rounded-3" placeholder="เช่น 000XXXX">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">รหัสวิชา (Code)</label>
                        <input id="swal-code" class="form-control rounded-3" placeholder="เช่น 001-XXX">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="small text-muted mb-1">ชื่อรายวิชา (ภาษาไทย)</label>
                        <input id="swal-thainame" class="form-control rounded-3" placeholder="ชื่อภาษาไทย">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="small text-muted mb-1">ชื่อรายวิชา (English Name)</label>
                        <input id="swal-englishname" class="form-control rounded-3" placeholder="English Name">
                    </div>
                   <div class="col-12 mt-3">
                        <label class="small text-muted mb-1">ระดับการศึกษา (Study Level)</label>
                        <select id="swal-studylevel" class="form-select rounded-3">
                            <option value="">-- เลือกระดับการศึกษา --</option>
                            <option value="ปริญญาตรี">ปริญญาตรี</option>
                            <option value="ปริญญาโท">ปริญญาโท</option>
                            <option value="ปริญญาเอก">ปริญญาเอก</option>
                            <option value="ประกาศนียบัตรบัณฑิต">ประกาศนียบัตรบัณฑิต</option>
                        </select>
                    </div>
                    <div class="col-12 mt-3 d-flex align-items-center justify-content-between bg-light p-2 rounded-3">
                        <span class="small fw-medium text-dark">สถานะการเปิดใช้งาน (is_active)</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input custom-switch" type="checkbox" id="swal-isactive" checked style="width: 3em; height: 1.5em; cursor:pointer;">
                        </div>
                    </div>
                </div>
            </div>
        `,
            customClass: {
                actions: 'gap-3', // เพิ่มระยะห่างระหว่างปุ่มตรงนี้ (gap-3 คือประมาณ 1rem)
                confirmButton: 'btn btn-dark px-4 py-2 rounded-3',
                cancelButton: 'btn btn-light px-4 py-2 rounded-3 text-dark border' // เพิ่ม border ให้ปุ่มยกเลิกดูชัดขึ้น
            },
            buttonsStyling: false,
            showCancelButton: true,
            width: '900px',
            confirmButtonText: 'บันทึกรายวิชา',
            cancelButtonText: 'ยกเลิก',
            preConfirm: () => {
                const subject_id = document.getElementById('swal-subjectid').value;
                const code = document.getElementById('swal-code').value;
                const thainame = document.getElementById('swal-thainame').value;
                const englishname = document.getElementById('swal-englishname').value;
                const study_level = document.getElementById('swal-studylevel').value;
                const is_active = document.getElementById('swal-isactive').checked ? 'Y' : 'N';

                if (!subject_id || !code || !thainame || !englishname || !study_level) {
                    Swal.showValidationMessage('กรุณากรอก Subject ID, รหัสวิชา และชื่อวิชา');
                    return false;
                }
                return {
                    subject_id,
                    code,
                    thainame,
                    englishname,
                    study_level,
                    is_active
                };
            }
        });

        if (formValues) {
            fetch('Api/SubjectsApi.php?action=add_subject', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json; charset=UTF-8'
                    },
                    body: JSON.stringify(formValues)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'เพิ่มรายวิชาเรียบร้อยแล้ว',
                                timer: 1500,
                                showConfirmButton: false
                            })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('ผิดพลาด', data.message, 'error');
                    }
                });
        }
    });

    async function handleSyncWithPreview() {
        // 1. แสดงสถานะกำลังโหลด
        Swal.fire({
            title: 'กำลังตรวจสอบข้อมูล...',
            didOpen: () => {
                Swal.showLoading();
            }
        });
        const response = await fetch('Api/SubjectsApi.php?action=sync_preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }

        });
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            // const data = await response.json();

            if (data.success) {
                if (parseInt(data.new_count) === 0) {
                    Swal.fire('ข้อมูลเป็นปัจจุบัน', 'ไม่พบรายวิชาใหม่จากมหาลัยที่ยังไม่มีในระบบของคุณ', 'info');
                } else {
                    // --- ส่วนที่แก้ไข: สร้างตารางแสดงรายการวิชาใหม่ ---
                    let itemsHtml = `
   <div class="text-start mb-3 px-1">
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
            <i class="bi bi-info-circle me-1"></i> ตรวจพบรายวิชาใหม่ ${data.new_count} รายการ
        </span>
    </div>
    <div class="table-responsive rounded-3 border" style="max-height: 350px; overflow-y: auto;">
        <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem; border-collapse: separate;">
            <thead class="sticky-top shadow-sm" style="background-color: #f8fafc; z-index: 10;">
                <tr class="text-secondary">
                    <th class="py-2 text-center" style="width: 15%;">SubjectID</th>
                    <th class="py-2 text-center" style="width: 20%;">รหัสวิชา</th>
                    <th class="py-2 text-start" style="width: 45%;">ชื่อภาษาไทย / อังกฤษ</th>
                    <th class="py-2 text-center" style="width: 20%;">ระดับ</th>
                </tr>
            </thead>
            <tbody class="align-middle">
                ${data.items.map(item => `
                    <tr>
                        <td class="py-2 text-center text-muted font-monospace" style="font-size: 0.75rem;">
                            ${item.subject_id}
                        </td>
                        <td class="py-2 text-center">
                            <span class="badge bg-light text-dark border-0 fw-bold" style="letter-spacing: 0.5px;">
                                ${item.code}
                            </span>
                        </td>
                        <td class="py-2 text-start">
                            <div class="fw-bold text-dark" style="line-height: 1.2;">${item.thainame}</div>
                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">${item.englishname}</div>
                        </td>
                        <td class="text-center py-1">
                        ${(() => {
                            // กำหนดค่าเริ่มต้นเป็นสีเทา
                            let badgeStyle = 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25';
                            
                            // เช็คเงื่อนไขตามข้อความที่ได้รับจาก API
                            if (item.study_level && item.study_level.includes('ปริญญาตรี')) {
                                badgeStyle = 'bg-success bg-opacity-10 text-success border-success border-opacity-25';
                            } else if (item.study_level && item.study_level.includes('ปริญญาโท')) {
                                badgeStyle = 'bg-primary bg-opacity-10 text-primary border-primary border-opacity-25';
                            } else if (item.study_level && item.study_level.includes('ปริญญาเอก')) {
                                badgeStyle = 'bg-info bg-opacity-10 text-info border-info border-opacity-25';
                            }

                            return `<span class="badge rounded-pill border ${badgeStyle} px-3" style="font-weight: 500;">
                                        ${item.study_level || '-'}
                                    </span>`;
                })()
        } < /td>      < /
        tr >
            `).join('')}
                    </tbody>
                </table>
            </div>
            <div class="mt-3 p-2 bg-light rounded-3 text-center">
                <span class="text-secondary small">ต้องการนำเข้าข้อมูลรายวิชาทั้งหมดนี้หรือไม่?</span>
            </div>
        `;

        const result = await Swal.fire({
            title: '<span class="fs-4 fw-bold">พบรายวิชาใหม่!</span>',
            html: itemsHtml,
            icon: 'question',
            width: '800px', // ขยายความกว้าง Modal เพื่อให้เห็นตารางชัดเจน
            showCancelButton: true,
            confirmButtonText: 'ยืนยันนำเข้า',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#334155',

        });

        if (result.isConfirmed) {
            performSync(data.items);
        }
    }
    }
    }
    catch (e) {
        console.error(e);
        console.error("Raw Response:", text);
        Swal.fire('Error', 'ไม่สามารถอ่านข้อมูล JSON ได้ หรือโครงสร้างข้อมูลผิดพลาด', 'error');
    }
    }

    async function performSync(items) {
        Swal.fire({
            title: 'กำลังนำเข้าข้อมูล...',
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const res = await fetch('Api/SubjectsApi.php?action=sync_api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                subjects: items
            })
        });

        const result = await res.json();
        if (result.success) {
            Swal.fire('สำเร็จ', `เพิ่มรายวิชาใหม่เรียบร้อยแล้ว (${result.added} วิชา)`, 'success')
                .then(() => location.reload());
        }
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'ยืนยันการลบรายวิชา?',
            text: "ข้อมูลรายวิชาจะถูกลบถาวร!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // สีแดง
            cancelButtonColor: '#f1f5f9',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                cancelButton: 'text-dark border'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่งคำขอไปที่ API เพื่อลบ
                deleteSubject(id);
            }
        });
    }

    // async function deleteSubject(id) {
    //     try {
    //         const response = await fetch(`Api/SubjectsApi.php?action=delete&id=${id}`, {
    //             method: 'DELETE'
    //         });
    //         const res = await response.json();

    //         if (res.success) {
    //             Swal.fire('ลบสำเร็จ!', 'รายวิชาถูกลบออกจากระบบแล้ว', 'success')
    //                 .then(() => location.reload());
    //         } else {
    //             Swal.fire('ผิดพลาด', res.message || 'ไม่สามารถลบข้อมูลได้', 'error');
    //         }
    //     } catch (error) {
    //         Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
    //     }
    // }

    async function deleteSubject(id, isForce = false) {
        const url = `Api/SubjectsApi.php?action=delete&id=${id}${isForce ? '&force=true' : ''}`;

        try {
            const response = await fetch(url, {
                method: 'DELETE'
            });
            const res = await response.json();

            if (res.success) {
                Swal.fire('สำเร็จ!', res.message, 'success').then(() => location.reload());
            } else if (res.has_data) {
                // --- กรณีพบข้อมูลใน answers: เตือนเพื่อให้กดยืนยันอีกครั้ง ---
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    html: `
        <div class="mt-2">
            <p class="text-danger fw-medium mb-1">${res.message}</p>
            <p class="text-secondary">คุณต้องการลบ "รายวิชา" นี้ออกใช่หรือไม่?</p>
            <small class="text-muted">(ข้อมูลการประเมินที่เคยตอบไว้จะไม่ถูกลบ)</small>
        </div>
    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'ยืนยันการลบรายวิชา',
                    cancelButtonText: 'ยกเลิก',
                    customClass: {
                        cancelButton: 'text-white border'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteSubject(id, true); // ส่ง force=true เพื่อไปรันคำสั่งลบ subject ใน PHP
                    }
                });
            } else {
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        } catch (error) {
            Swal.fire('ผิดพลาด', 'การเชื่อมต่อล้มเหลว', 'error');
        }
    }

    async function handleEditSubject(btn) {
        // ดึงข้อมูลจาก Attribute ของปุ่มที่กด
        const data = btn.dataset;

        const {
            value: formValues
        } = await Swal.fire({
            title: '<span class="fs-4 fw-bold">แก้ไขข้อมูลรายวิชา</span>',
            html: `
            <div class="container-fluid text-start mt-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">Subject ID (แก้ไขไม่ได้)</label>
                        <input id="edit-subjectid" class="form-control rounded-3 bg-light" value="${data.id}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">รหัสวิชา (Code)</label>
                        <input id="edit-code" class="form-control rounded-3" value="${data.code}">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="small text-muted mb-1">ชื่อรายวิชา (ภาษาไทย)</label>
                        <input id="edit-thainame" class="form-control rounded-3" value="${data.thainame}">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="small text-muted mb-1">ชื่อรายวิชา (English Name)</label>
                        <input id="edit-englishname" class="form-control rounded-3" value="${data.englishname}">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="small text-muted mb-1">ระดับการศึกษา</label>
                        <select id="edit-studylevel" class="form-select rounded-3">
                            <option value="ปริญญาตรี" ${data.level === 'ปริญญาตรี' ? 'selected' : ''}>ปริญญาตรี</option>
                            <option value="ปริญญาโท" ${data.level === 'ปริญญาโท' ? 'selected' : ''}>ปริญญาโท</option>
                            <option value="ปริญญาเอก" ${data.level === 'ปริญญาเอก' ? 'selected' : ''}>ปริญญาเอก</option>
                            <option value="ประกาศนียบัตรบัณฑิต" ${data.level === 'ประกาศนียบัตรบัณฑิต' ? 'selected' : ''}>ประกาศนียบัตรบัณฑิต</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
            width: '700px',
            showCancelButton: true,
            confirmButtonText: 'บันทึกการแก้ไข',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                actions: 'gap-3',
                confirmButton: 'btn btn-dark px-4 py-2 rounded-3',
                cancelButton: 'btn btn-light px-4 py-2 rounded-3 border'
            },
            buttonsStyling: false,
            preConfirm: () => {
                return {
                    subject_id: document.getElementById('edit-subjectid').value,
                    code: document.getElementById('edit-code').value,
                    thainame: document.getElementById('edit-thainame').value,
                    englishname: document.getElementById('edit-englishname').value,
                    study_level: document.getElementById('edit-studylevel').value
                }
            }
        });

        if (formValues) {
            // ส่งข้อมูลไปที่ API เพื่อ Update
            updateSubject(formValues);
        }
    }

    async function updateSubject(formData) {
        try {
            const response = await fetch('Api/SubjectsApi.php?action=update_subject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            const res = await response.json();
            if (res.success) {
                Swal.fire('สำเร็จ', 'แก้ไขข้อมูลเรียบร้อยแล้ว', 'success').then(() => location.reload());
            } else {
                Swal.fire('ล้มเหลว', res.message, 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    }
</script>