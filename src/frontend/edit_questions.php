<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    body {
        background-color: #f8fafc;
    }

    /* Header Style: Clean & Professional */
    .edit-header-section {
        background: #ffffff;
        padding: 2rem 0;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }

    .subject-title {
        color: #1e293b;
        font-weight: 700;
    }

    .subject-subtitle {
        color: #64748b;
        font-size: 0.95rem;
    }

    /* Question Cards: Modern White */
    .topic-group {
        background-color: #ffffff;
        border-radius: 16px;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .topic-header {
        background-color: #334155;
        /* Slate 700 */
        color: #f8fafc;
        padding: 1rem 1.5rem;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    .question-item {
        background: #ffffff;
        margin: 0.75rem 1.5rem;
        padding: 1.1rem;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .question-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }

    /* Floating Save Bar: Glassmorphism */
    .floating-save-bar {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 0.8rem 2.5rem;
        border-radius: 100px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        min-width: 350px;
    }

    /* Form Elements */
    .drag-handle {
        cursor: grab;
        margin-right: 1rem;
        color: #94a3b8;
        font-size: 1.2rem;
    }

    .q-order-label {
        color: #64748b;
        font-size: 0.85rem;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 6px;
        margin-right: 1rem;
        /* ปรับจาก 65px เป็น 80px หรือใส่ nowrap */
        min-width: 80px;
        text-align: center;
        white-space: nowrap;
        /* เพิ่มบรรทัดนี้เพื่อป้องกันการขึ้นบรรทัดใหม่ */
    }

    .q-input {
        border: none;
        background: transparent;
        font-size: 1rem;
        color: #334155;
        width: 100%;
    }

    .q-input:focus {
        outline: none;
        border-bottom: 1px solid #94a3b8;
    }

    .btn-remove-q {
        color: #94a3b8;
        border: none;
        background: none;
        transition: color 0.2s;
    }

    .btn-remove-q:hover {
        color: #ef4444;
    }

    /* ทำให้เป้าหมายที่กำลังลากดูโปร่งแสงขึ้น */
    .ghost-class {
        opacity: 0.4;
        background: #e2e8f0 !important;
        border: 2px dashed #cbd5e1 !important;
    }

    /* เพิ่มในส่วน <style> ของ edit_questions.php */
    .topic-group {
        background: #ffffff;
        border-radius: 16px;
        /* ขอบมนมากขึ้นให้ดูทันสมัย */
        border: 1px solid #f1f5f9;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .topic-header {
        background: #334155;
        /* Slate 700 */
        padding: 1.25rem 1.5rem;
        color: white;
    }

    .question-item {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        margin-bottom: 10px;
        padding: 12px 16px;
        transition: all 0.2s ease;
    }

    .question-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* ป้องกันการคลุมข้อความขณะลาก */
    /* ป้องกันการไฮไลต์ข้อความขณะลาก ซึ่งเป็นสาเหตุที่ทำให้ลากติดขัด */
    .question-item {
        user-select: none;
        -webkit-user-select: none;
        cursor: default;
    }

    /* ทำให้ Handle ดูเด่นขึ้นเมื่อเอาเมาส์ไปชี้ */
    .drag-handle:hover {
        color: #334155;
        cursor: grab;
    }

    .drag-handle:active {
        cursor: grabbing;
    }
</style>

<div class="edit-header-section shadow-sm">
    <div class="container ">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="subject-title mb-1">
                    <span class="text-primary me-2"><?= htmlspecialchars($subject['code']) ?></span>
                    <?= htmlspecialchars($subject['thainame']) ?>
                </h3>
                <div class="subject-subtitle d-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border fw-normal">ID: <?= htmlspecialchars($subject['subject_id']) ?></span>
                    <span class="text-muted">|</span>
                    <span class="fst-italic"><?= htmlspecialchars($subject['englishname']) ?></span>
                </div>
            </div>
            <a href="subjects" class="btn btn-link text-decoration-none text-muted">
                <i class="bi bi-x-lg me-1"></i> ปิดหน้าต่าง
            </a>

        </div>
    </div>
</div>



<div class="container" style="margin-bottom: 120px;">

    <form id="sortableForm" method="POST" action="Api/QuestionsApi.php">
        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['subject_id']) ?>">

        <?php
        $groupedQuestions = [];
        // foreach ($questions as $q) {
        //     $groupedQuestions[$q['topic']][] = $q;
        // }
        // 1. นำข้อมูลจาก DB มาจัดกลุ่มตามปกติ
        if (!empty($questions)) {
            foreach ($questions as $q) {
                $groupedQuestions[$q['topic']][] = $q;
            }
        }

        // 2. ถ้าไม่มีข้อมูลเลย ให้สร้างหัวข้อมาตรฐาน 2 อันรอไว้ (วิชาใหม่)
        if (empty($groupedQuestions)) {
            $defaultTopics = ['ประเมินอาจารย์ผู้ประสานงานรายวิชา', 'การทวนสอบผลการเรียนรู้'];
            foreach ($defaultTopics as $topic) {
                $groupedQuestions[$topic] = []; // สร้าง Key รอไว้แต่ไม่มีข้อมูลข้างใน
            }
        }
        ?>

        <?php foreach ($groupedQuestions as $topic => $items): ?>

            <div class="topic-group shadow-sm mb-4">
                <div class="topic-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-layers-half me-2"></i>
                        <span>หมวดหมู่: <?= htmlspecialchars($topic) ?></span>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-white bg-opacity-10 fw-normal rounded-pill px-3 q-count-badge" style="font-size: 0.85rem; border: 1px solid rgba(255,255,255,0.2);">
                            <?= count($items) ?> ข้อ
                        </span>

                        <button type="button" class="btn btn-sm btn-light rounded-pill btn-add-q" data-topic="<?= htmlspecialchars($topic) ?>">
                            <i class="bi bi-plus-lg me-1"></i> เพิ่มคำถาม
                        </button>
                    </div>
                </div>

                <div class="question-list py-2" data-topic="<?= htmlspecialchars($topic) ?>" style="min-height: 50px;">
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $q): ?>
                            <div class="question-item shadow-none">
                                <div class="drag-handle"><i class="bi bi-grip-vertical"></i></div>
                                <div class="flex-grow-1 d-flex align-items-center">
                                    <input type="hidden" name="question_id[]" value="<?= $q['id'] ?>">
                                    <input type="hidden" name="topic[]" value="<?= htmlspecialchars($topic) ?>">
                                    <span class="q-order-label fw-medium">ข้อที่ <?= $q['order'] ?></span>
                                    <input type="text" name="question_text[]" class="q-input" value="<?= htmlspecialchars($q['text']) ?>">
                                </div>
                                <div class="ms-2">
                                    <button type="button" class="btn-remove-q"><i class="bi bi-dash-circle"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted border-top border-bottom bg-light bg-opacity-10 small">
                            <i class="bi bi-info-circle me-1"></i> ยังไม่มีคำถามในหมวดหมู่นี้ กดปุ่ม "เพิ่มคำถาม" ด้านบนเพื่อเริ่มต้น
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>



        <div class="floating-save-bar d-flex justify-content-between align-items-center">
            <div class="text-muted small d-none d-md-block me-4">
                <i class="bi bi-info-circle me-1"></i> ลากเพื่อจัดลำดับใหม่
            </div>
            <button type="submit" class="btn btn-dark rounded-pill px-5 py-2 fw-medium shadow-sm">
                บันทึกการเปลี่ยนแปลงทั้งหมด
            </button>

        </div>
    </form>


    <div class="topic-group shadow-sm">
        <div class="topic-header" style="background-color: #64748b;">
            <i class="bi bi-clock-history me-2"></i> ประวัติการบันทึก
        </div>

        <div class="p-4">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>เวอร์ชั่น (ID)</th>
                        <th>วันที่บันทึก</th>
                        <th>หมายเหตุ</th>
                        <th>สถานะ</th>
                        <th class="text-end">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($versions) && is_array($versions) && count($versions) > 0):
                        // หาจำนวนเวอร์ชันทั้งหมด
                        $total_versions = count($versions);

                        foreach ($versions as $index => $v):
                            // คำนวณเลข่เวอร์ชัน: เอาจำนวนทั้งหมด ลบด้วยลำดับปัจจุบัน (index)
                            // เช่น มี 5 อัน: อันแรกสุด (index 0) จะได้ Ver. 5, อันสุดท้ายจะได้ Ver. 1
                            $display_version = $total_versions - $index;
                    ?>
                            <tr>
                                <td>Ver. <?= $display_version ?> (<?= $v['id'] ?>)</td>

                                <td><?= date('d/m/Y H:i', strtotime($v['created_at'])) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($v['note'] ?? '-') ?></td>
                                <td>
                                    <?= $v['in_used'] == 1 ? '<span class="badge bg-success">ใช้งานอยู่</span>' : '<span class="badge bg-light text-dark border">เวอร์ชั่นเก่า</span>' ?>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-preview-version" data-id="<?= $v['id'] ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <?php if ($v['in_used'] != 1): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-rollback" data-id="<?= $v['id'] ?>">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 btn-delete-version" data-id="<?= $v['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">ยังไม่มีประวัติการบันทึก</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>

    function initQuestionSortable(el) {
        new Sortable(el, {
            handle: '.drag-handle',
            animation: 200, // เพิ่มความเร็วให้ดูสมูทขึ้น
            ghostClass: 'ghost-class',
            forceFallback: false, // ปิดไว้เพื่อให้เบราว์เซอร์ช่วยจัดการจะลื่นกว่า
            onEnd: updateOrders
        });
    }

    // เรียกใช้ครั้งเดียวเมื่อโหลดหน้า
    document.querySelectorAll('.question-list').forEach(el => initQuestionSortable(el));

    function updateOrders() {
        const groups = document.querySelectorAll('#sortableForm .topic-group');

        groups.forEach(group => {
            const list = group.querySelector('.question-list');

            if (list) {
                const items = list.querySelectorAll('.question-item');
                const badge = group.querySelector('.q-count-badge');

                if (badge) {
                    badge.innerText = `${items.length} ข้อ`;
                }

                items.forEach((item, index) => {
                    const label = item.querySelector('.q-order-label');
                    if (label) {
                        label.innerText = `ข้อที่ ${index + 1}`;
                    }
                });
            }
        });
    }

    // ส่วนลบคำถาม
    document.addEventListener('click', e => {
        if (e.target.closest('.btn-remove-q')) {
            const item = e.target.closest('.question-item');

            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "ข้อมูลข้อนี้จะหายไปจากรายการปัจจุบัน",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // สีแดง
                confirmButtonText: 'ลบออก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    item.remove();
                    updateOrders();
                }
            });
        }
    });

    // 1. ฟังก์ชันเพิ่มคำถามใหม่
    document.addEventListener('click', async e => {
        if (e.target.closest('.btn-add-q')) {
            const btn = e.target.closest('.btn-add-q');
            const topic = btn.getAttribute('data-topic');
            const list = btn.closest('.topic-group').querySelector('.question-list');

            const {
                value: text
            } = await Swal.fire({
                title: 'เพิ่มคำถามใหม่',
                input: 'textarea',
                inputLabel: `หมวดหมู่: ${topic}`,
                inputPlaceholder: 'ระบุเนื้อหาคำถามที่นี่...',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#334155',
                inputAttributes: {
                    'rows': 4,
                    'style': 'font-size: 1rem; line-height: 1.5;'
                },
                inputValidator: (value) => {
                    if (!value) return 'กรุณาระบุข้อความคำถาม'
                }
            });

            if (text) {
                const btn = e.target.closest('.btn-add-q');
                const topic = btn.getAttribute('data-topic');
                const list = btn.closest('.topic-group').querySelector('.question-list');
                const emptyAlert = list.querySelector('.text-center.py-4');
                if (emptyAlert) {
                    emptyAlert.remove(); // ถ้าเจอให้ลบทิ้งทันที ก่อนจะแทรกคำถามใหม่
                }
                // --------------------------

                // จากนั้นค่อยแทรก HTML คำถามใหม่ตามปกติ
                const newItem = `
                <div class="question-item shadow-none">
                    <div class="drag-handle"><i class="bi bi-grip-vertical"></i></div>
                    <div class="flex-grow-1 d-flex align-items-center">
                        <input type="hidden" name="question_id[]" value="new">
                        <input type="hidden" name="topic[]" value="${topic}">
                        <span class="q-order-label fw-medium">ข้อที่ ใหม่</span>
                        <input type="text" name="question_text[]" class="q-input" value="${text}">
                    </div>
                    <div class="ms-2">
                        <button type="button" class="btn-remove-q"><i class="bi bi-dash-circle"></i></button>
                    </div>
                </div>`;

                list.insertAdjacentHTML('beforeend', newItem);
                updateOrders();


                Swal.fire({
                    icon: 'success',
                    title: 'เพิ่มคำถามใหม่แล้ว',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }

        if (e.target.closest('.btn-rollback')) {
            const id = e.target.closest('.btn-rollback').dataset.id;

            Swal.fire({
                title: 'ยืนยันการเปลี่ยนเวอร์ชั่น?',
                text: "ระบบจะนำคำถามในเวอร์ชั่นนี้กลับมาใช้งาน",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#334155',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`Api/QuestionsApi.php?action=rollback&fid=${id}&sid=<?= $subject['subject_id'] ?>`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('สำเร็จ', 'เปลี่ยนเวอร์ชั่นแล้ว', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('ผิดพลาด', data.message, 'error');
                            }
                        });
                }
            });
        }
    });
    // ปรับปรุงส่วนบันทึกคำถามทั้งหมด
    document.getElementById('sortableForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'ยืนยันการบันทึกเวอร์ชั่นใหม่',
            html: `
            <div class="text-center mb-2 ">ระบุหมายเหตุการบันทึก :</div>
            <input type="text" id="save_note" class="swal2-input mt-0" placeholder="ตัวอย่าง: เทอม 2/2568">
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#334155',
            confirmButtonText: 'บันทึกข้อมูล',
            cancelButtonText: 'ยกเลิก',
            preConfirm: () => {
                const note = document.getElementById('save_note').value;
                // ไม่บังคับกรอกก็ได้ แต่ส่งค่าไป
                return note;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const note = result.value; // ดึงค่าหมายเหตุจาก SweetAlert

                Swal.fire({
                    title: 'กำลังบันทึกข้อมูล...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false
                });

                const formData = new FormData(this);
                formData.append('save_note', note); // เพิ่มหมายเหตุลงใน FormData ก่อนส่ง

                fetch('Api/QuestionsApi.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('สำเร็จ!', data.message, 'success')
                                .then(() => {
                                    location.reload();
                                });
                        }
                    });
            }
        });
    });

    // เพิ่มฟังก์ชันนี้ในส่วน <script> ของ edit_questions.php
    document.addEventListener('click', e => {
        if (e.target.closest('.btn-preview-version')) {
            const fid = e.target.closest('.btn-preview-version').dataset.id;
            const verLabel = e.target.closest('tr').querySelector('td:first-child').innerText;

            Swal.fire({
                title: 'กำลังโหลดข้อมูล...',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`Api/QuestionsApi.php?action=get_version_detail&fid=${fid}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        let html = `<div class="text-start mt-2" style="max-height: 70vh; overflow-y: auto; padding: 10px 20px;">`;

                        for (const [topic, items] of Object.entries(res.data)) {
                            html += `
                                <div class="mb-5 shadow-sm border rounded-3 overflow-hidden">
                                    <div class="px-4 py-3 border-bottom" style="background-color: #334155;">
                                        <h6 class="fw-medium mb-0 d-flex align-items-center" style="color: #ffffff; font-size: 1.1rem;">
                                            <i class="bi bi-layers-half text-white me-2"></i> ${topic} 
                                            
                                            <span class="badge rounded-pill bg-white fw-normal ms-auto" 
                                                style="font-size: 0.8rem; color: #1e3a8a; border: 1px solid #e2e8f0;">
                                                ${items.length} ข้อ
                                            </span>
                                            
                                        </h6>
                                    </div>
                                    <div class="p-4 bg-white">`;


                            items.forEach((q, index) => {
                                html += `
                                <div class="d-flex align-items-start ${index !== items.length - 1 ? 'mb-4' : ''}">
                                    <div class="me-3 mt-1">
                                        <span class="badge rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center" 
                                        style="width: 28px; height: 28px; font-size: 0.75rem; border: 1px solid #e2e8f0;">
                                            ${q.order}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 pt-1" style="font-size: 1.05rem; line-height: 1.6; color: #1e293b;">
                                        ${q.text}
                                    </div>
                                </div>`;
                            });

                            html += `</div></div>`;
                        }
                        html += '</div>';

                        Swal.fire({
                            title: `<div class="pt-2" style="font-size: 1.4rem;">ตรวจสอบชุดคำถาม (${verLabel})</div>`,
                            html: html,
                            width: '1250', // ขยายกว้างขึ้นเพื่อลดความแออัด
                            confirmButtonText: 'ปิดหน้าต่าง',
                            confirmButtonColor: '#1e293b',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0',
                                container: 'p-0'
                            }
                        });
                    }
                });
        }
    });

    document.addEventListener('click', e => {
        if (e.target.closest('.btn-delete-version')) {
            const fid = e.target.closest('.btn-delete-version').dataset.id;

            Swal.fire({
                title: 'ยืนยันการลบเวอร์ชั่น?',
                text: "เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลเวอร์ชั่นนี้ได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'ใช่, ลบทิ้ง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`Api/QuestionsApi.php?action=delete_version&fid=${fid}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('ลบสำเร็จ', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('ผิดพลาด', data.message, 'error');
                            }
                        });
                }
            });
        }
    });
</script>