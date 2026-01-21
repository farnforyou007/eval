<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<head>
    <link rel="stylesheet" href="edit_questions.css">
</head>

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
                    <?php
                    $isActive = $subject['is_active'] ?? 'Y';
                    $statusClass = ($isActive === 'Y') ? 'status-active' : 'status-inactive';
                    ?>
                    <span class="status-dot <?= $statusClass ?>"></span>
                    <span class="fst-italic"><?= htmlspecialchars($subject['englishname']) ?></span>
                </div>
            </div>

            <a href="subjects" class="text-decoration-none text-muted d-inline-flex align-items-center gap-2 btn-back-hover">
               
                <i class="bi bi-arrow-left-circle-fill" style="font-size: 24px;"></i>
                <span style="line-height: 1;">ย้อนกลับ</span>
            </a> 
           
        </div>
    </div>

    <div class="fixed-bottom-right" style="position: fixed; right: 20px; bottom: 100px; z-index: 1050;">
        <button class="btn btn-dark shadow-lg rounded-circle d-flex align-items-center justify-content-center custom-tooltip"
            style="width: 60px; height: 60px;"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#historyDrawer"
            data-tooltip="เปิดดูประวัติเวอร์ชั่น">
            <i class="bi bi-clock-history fs-3"></i>
        </button>
    </div>
</div>



<div class="container-fluid px-md-5" style="margin-bottom: 120px;">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <form id="sortableForm" method="POST" action="Api/QuestionsApi.php">
                <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['subject_id']) ?>">

                <?php
                $groupedQuestions = [];

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
                                            <!-- <button type="button" class="btn-remove-q"><i class="bi bi-dash-circle"></i></button> -->
                                            <button type="button" class="btn-remove-q custom-tooltip" data-tooltip="ลบข้อนี้ออก ">
                                                <i class="bi bi-dash-circle"></i>
                                            </button>
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
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="historyDrawer" aria-labelledby="historyDrawerLabel" style="width: 600px;">
            <div class="offcanvas-header border-bottom bg-light">
                <h5 class="offcanvas-title d-flex align-items-center" id="historyDrawerLabel">
                    <i class="bi bi-clock-history me-2"></i> ประวัติการบันทึกเวอร์ชั่น
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top" style="top: 0; z-index: 1;">
                            <tr>
                                <th>เวอร์ชั่น (ID)</th>
                                <th>วันที่บันทึก</th>
                                <th>หมายเหตุ</th>
                                <th>สถานะ</th>
                                <th class="text-end pe-3">การจัดการ</th>
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
                                        <td class="ps-3 fw-bold">Ver. <?= $display_version ?> (<?= $v['id'] ?>)</td>

                                        <td>
                                            <div class="small"><?= date('d/m/Y', strtotime($v['created_at'])) ?></div>
                                            <div class="text-muted small"><?= date('H:i', strtotime($v['created_at'])) ?> น.</div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($v['note'] ?? '-') ?>">
                                                <?= htmlspecialchars($v['note'] ?? '-') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?= $v['in_used'] == 1 ? '<span class="badge bg-success">ใช้งานอยู่</span>' : '<span class="badge bg-light text-dark border">เวอร์ชั่นเก่า</span>' ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-preview-version custom-tooltip" data-tooltip="คลิกเพื่อดูข้อมูล " data-id="<?= $v['id'] ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <?php if ($v['in_used'] != 1): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-rollback custom-tooltip" data-tooltip="เปิดใช้งานเวอร์ชั่นนี้ " data-id="<?= $v['id'] ?>">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 btn-delete-version custom-tooltip" data-tooltip="ลบเวอร์ชั่นนี้ " data-id="<?= $v['id'] ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
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
            <div class="offcanvas-footer p-3 border-top bg-light">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i> คุณสามารถดูตัวอย่าง (Eye) หรือย้อนกลับไปยังเวอร์ชั่นที่ต้องการได้ทันที</small>
            </div>
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
    // ส่วนบันทึกข้อมูล
    document.getElementById('sortableForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // ดึงจำนวนคำถามทั้งหมดที่มีในฟอร์ม
        const totalQuestions = document.querySelectorAll('.question-item').length;

        // ถ้าไม่มีคำถามเลย ให้แสดงคำเตือนก่อน
        if (totalQuestions === 0) {
            Swal.fire({
                title: 'คำเตือน: ไม่มีคำถาม',
                text: "คุณกำลังจะบันทึกแบบประเมินที่ไม่มีคำถามเลย ต้องการดำเนินการต่อหรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#334155',
                confirmButtonText: 'ยืนยันบันทึกแบบว่าง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    showSaveNoteModal(this); // เรียกฟังก์ชันบันทึก
                }
            });
        } else {
            showSaveNoteModal(this); // เรียกฟังก์ชันบันทึกปกติ
        }
    });

    // แยกฟังก์ชันการกรอก Note ออกมาเพื่อให้เรียกใช้ซ้ำได้
    function showSaveNoteModal(formElement) {
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
                return document.getElementById('save_note').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const note = result.value;

                Swal.fire({
                    title: 'กำลังบันทึกข้อมูล...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false
                });

                const formData = new FormData(formElement);
                formData.append('save_note', note);

                fetch('Api/QuestionsApi.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('สำเร็จ!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('ผิดพลาด', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อกับ Server ได้', 'error');
                    });
            }
        });
    }

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

<script>
    // เพิ่มโค้ดนี้ในส่วน <script> ท้ายไฟล์ เพื่อเปิดใช้งาน Tooltip ทั้งหน้า
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>