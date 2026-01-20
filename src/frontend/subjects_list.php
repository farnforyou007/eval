<style>
    .modern-table {
        margin-bottom: 0;
    }

    /* Header Styling */
    .modern-table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    /* Body Styling */
    .modern-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Hover Effect */
    .modern-table tbody tr:hover td {
        background-color: #f1f5f9;
        color: #0f172a;
    }

    /* Subject Badge */
    .code-badge {
        background-color: #f1f5f9;
        color: #1e293b;
        font-weight: 700;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        letter-spacing: 0.5px;
    }

    .subject-badge {
        background-color: #f1f5f9;
        color: #1e293b;
        font-weight: 700;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        letter-spacing: 0.5px;
    }

    /* Action Button Customization */
    .btn-edit-modern {
        background-color: #ffffff;
        color: #64748b;
        border: 1px solid #e2e8f0;
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-edit-modern:hover {
        background-color: #334155;
        color: #ffffff;
        border-color: #334155;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(51, 65, 85, 0.2);
    }

    /* Search Input Styling */
    .search-wrapper {
        position: relative;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .search-wrapper .bi-search {
        position: absolute;
        left: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        z-index: 10;
        font-size: 1.1rem;
    }

    .search-input-modern {
        width: 100%;
        /* เพิ่มบรรทัดนี้เพื่อให้กรอกได้เต็มความยาวกล่อง */
        height: 48px;
        border: none !important;
        padding-left: 3rem !important;
        padding-right: 1.5rem !important;
        /* เพิ่ม padding ขวาเพื่อไม่ให้ตัวหนังสือติดขอบเกินไป */
        background: transparent !important;
        font-size: 1rem;
        color: #334155;
    }

    .search-input-modern:focus {
        outline: none;
        border-color: #94a3b8;
        box-shadow: 0 0 0 4px rgba(226, 232, 240, 0.5);
    }

    .search-wrapper:focus-within {
        border-color: #94a3b8;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(226, 232, 240, 0.4) !important;
        transform: translateY(-1px);
        /* เพิ่มลูกเล่นขยับขึ้นเล็กน้อย */
    }

    .container-wide {
        max-width: 100%;
        width: 100%;
        /* ขยายให้กว้างกว่า container ปกติ */
        margin: 0 auto;
        padding: 0 2rem;
    }

    .table-container {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        overflow: hidden !important;
        /* เปลี่ยนจาก visible เป็น hidden เพื่อไม่ให้ขอบตารางทะลุ */
        position: relative;
        max-width: 100%;
    }

    /* ปรับแต่ง Tooltip เพิ่มเติม */
    .custom-tooltip {
        position: relative;
        display: inline-flex;
        /* ช่วยให้การจัดตำแหน่งแม่นยำขึ้น */
    }

    .custom-tooltip::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        background-color: #334155;
        color: white;
        padding: 6px 12px;
        /* เพิ่ม padding ให้ดูโปร่งขึ้น */
        border-radius: 6px;
        font-size: 12px;
        /* จุดสำคัญ: ห้ามตัดข้อความ และให้แสดงเหนือทุกอย่าง */
        white-space: nowrap;
        pointer-events: none;
        /* ป้องกันไม่ให้ tooltip ขวางการคลิก */
        z-index: 9999;
        /* มั่นใจว่าอยู่เหนือแถวตาราง */

        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* เพิ่มเงาให้ลอยเด่นออกมา */
    }

    /* เพิ่มสามเหลี่ยมเล็กๆ ใต้ Tooltip (Optional) */
    .custom-tooltip::before {
        content: "";
        position: absolute;
        bottom: 110%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: #334155 transparent transparent transparent;
        opacity: 0;
        visibility: hidden;
        z-index: 9999;
    }

    .custom-tooltip:hover::after,
    .custom-tooltip:hover::before {
        opacity: 1;
        visibility: visible;
        bottom: 135%;
        /* ขยับขึ้นตอน hover ให้ดูนุ่มนวล */
    }

    .table-container {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        /* 1. ต้องเป็น visible เพื่อให้ Tooltip ลอยออกนอกตารางได้ */
        overflow: visible !important;
        position: relative;
    }

    .table-responsive {
        /* ตาราง Responsive มักจะมี overflow-x: auto ซึ่งจะตัด tooltip
    วิธีแก้คือให้ใส่ padding top/bottom ในตารางเพื่อให้มีพื้นที่แสดง */
        overflow: visible !important;
        border-radius: 20px;
        /* เพิ่มความโค้งให้ตัวครอบชั้นใน */
    }

    .modern-table {
        margin-bottom: 0;
        width: 100%;
        /* 3. ใช้คำสั่งนี้เพื่อให้มุมโค้งทำงานได้แม้ overflow เป็น visible */
        border-collapse: separate;
        border-spacing: 0;
    }

    /* 5. จัดการความโค้งที่มุมของ Body (แถวสุดท้าย) */
    .modern-table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 20px;
    }

    .modern-table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 20px;
    }

    /* สไตล์ปุ่ม Filter */
    .filter-btn {
        border: none !important;
        color: #64748b;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .filter-btn:hover {
        color: #1e293b;
        background-color: #f8fafc;
    }

    .filter-btn.active {
        background-color: #334155 !important;
        /* สี Slate 700 */
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(51, 65, 85, 0.15);
    }

    /* สไตล์ Badge สถานะ */
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-active {
        background-color: #22c55e;
    }

    /* สีเขียว */
    .status-inactive {
        background-color: #ef4444;
    }

    .pagination-modern {
        display: flex;
        gap: 6px;
    }

    .pagination-modern .page-item .page-link {
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        border-radius: 10px !important;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .pagination-modern .page-item .page-link:hover {
        background-color: #f8fafc;
        color: #1e293b;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    .pagination-modern .page-item.active .page-link {
        background-color: #334155;
        /* Slate 700 ตามธีมหลัก */
        border-color: #334155;
        color: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .pagination-modern .page-item.disabled .page-link {
        background-color: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    .pagination-modern .page-link i {
        font-size: 1.1rem;
    }

    .modern-table thead th,
    .modern-table tbody td {
        text-align: left;
        /* Default เป็นชิดซ้าย */
        padding: 1rem 1.25rem;
    }

    .pe-4 {
        padding-right: 1.5rem !important;
    }

    .custom-switch {
        width: 2.6rem !important;
        height: 1.35rem !important;
        cursor: pointer;
        border: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
        transition: background-color 0.25s ease-in-out, transform 0.1s !important;
    }

    /* สถานะ "ปิด" (สีแดง) */
    .custom-switch {
        background-color: #ef4444 !important;
        /* Red 500 */
        opacity: 0.8;
    }

    /* สถานะ "เปิด" (สีเขียว) */
    .custom-switch:checked {
        background-color: #22c55e !important;
        /* Green 500 */
        opacity: 1;
    }

    .custom-switch:active {
        transform: scale(0.95);
    }

    .custom-switch:focus {
        box-shadow: none !important;
        outline: none !important;
    }

    /* คุมการเลื่อนของตาราง */
    .table-responsive-custom {
        width: 100%;
        overflow-x: auto;
        /* ให้เลื่อนแนวนอนได้ถ้าข้อมูลล้น */
        -webkit-overflow-scrolling: touch;
        /* ให้การเลื่อนใน iPhone/iPad ลื่นไหล */
    }

    /* ในหน้าจอคอมพิวเตอร์ (ขนาดใหญ่กว่า 992px) */
    @media (min-width: 992px) {
        .table-responsive-custom {
            overflow-x: visible;
            /* ในคอมไม่ต้องมี Scrollbar ถ้าไม่จำเป็น */
        }

        .modern-table {
            table-layout: auto;
            /* ให้ตารางขยายเต็มพื้นที่ที่มี */
            width: 100%;
        }
    }

    /* ในหน้าจอมือถือ (ขนาดเล็กกว่า 768px) */
    @media (max-width: 767.98px) {
        .table-responsive-custom {
            margin: 0 -1rem;
            /* ขยายตารางให้เกือบชิดขอบจอเพื่อเพิ่มพื้นที่ */
            padding: 0 1rem;
        }

        /* บังคับให้รหัสวิชาอยู่บรรทัดเดียวและตัวเล็กลงตามที่คุยกันก่อนหน้า */
        .code-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            white-space: nowrap;
        }

        /* ซ่อนคอลัมน์ที่ไม่จำเป็นมากในมือถือเพื่อลดการเลื่อนข้างเยอะๆ */
        .d-mobile-none {
            display: none !important;
        }
    }
</style>

<!-- <div class="container mt-4 mb-5">
    <div class="row align-items-end mb-3">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-1" style="color: #1e293b; letter-spacing: -0.5px;">จัดการแบบประเมินรายวิชา</h2>
            <p class="text-secondary mb-0 fs-6">จัดการเนื้อหาคำถามและตรวจสอบสถานะรายวิชาทั้งหมดในระบบ</p>
        </div>
    </div> -->
<div class="container mt-4 mb-5">
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="fw-bold mb-1" style="color: #1e293b; letter-spacing: -0.5px;">จัดการแบบประเมินรายวิชา</h2>
            <p class="text-secondary mb-0 fs-6">จัดการเนื้อหาคำถามและตรวจสอบสถานะรายวิชาทั้งหมดในระบบ</p>
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
                            <th style="width: 22%;" class="text-start">Subject ID</th>
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
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
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

    let currentPage = 1;
    let itemsPerPage = parseInt(itemsPerPageSelect.value); // ดึงค่าจาก Dropdown
    let filteredItems = [];
    let currentFilter = 'all';

    function applyFilter() {
        const searchText = searchInput.value.toLowerCase().trim();

        filteredItems = subjectItems.filter(item => {
            const itemStatus = item.getAttribute('data-status');
            const itemText = item.innerText.toLowerCase();
            const matchesSearch = itemText.includes(searchText);
            const matchesFilter = (currentFilter === 'all' || itemStatus === currentFilter);
            return matchesSearch && matchesFilter;
        });

        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        subjectItems.forEach(item => item.style.display = 'none');

        const pageItems = filteredItems.slice(start, end);
        pageItems.forEach((item, index) => {
            item.style.display = '';
            // คำนวณลำดับที่ถูกต้อง: (หน้าปัจจุบัน-1) * จำนวนต่อหน้า + (ลำดับในหน้านั้น+1)
            const globalIndex = start + index + 1;
            item.querySelector('.row-index').innerText = globalIndex;
        });

        document.getElementById('totalItems').innerText = filteredItems.length;
        renderPaginationControls();

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

    // Event Listeners อื่นๆ คงเดิม
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
</script>