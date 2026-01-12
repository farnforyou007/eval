<!-- 

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
    body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
    
    /* Header Section - เรียบหรู */
    .edit-header-section {
        background: #ffffff;
        padding: 2rem 0;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    /* กล่องการ์ดคำถาม - Modern White */
    .topic-group {
        background-color: #ffffff;
        border-radius: 16px;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .topic-header {
        background-color: #1e293b; /* สีกรมท่าเข้ม */
        color: #f8fafc;
        padding: 1rem 1.5rem;
        font-weight: 500;
        font-size: 1rem;
        display: flex;
        align-items: center;
    }

    .question-item {
        background: #ffffff;
        margin: 0.75rem 1.5rem;
        padding: 1.25rem;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .question-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    /* Floating Save Bar - Glassmorphism */
    .floating-save-bar {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 0.75rem 2rem;
        border-radius: 100px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        z-index: 1000;
        width: auto;
        min-width: 400px;
    }

    /* Input & Icons */
    .drag-handle { cursor: grab; margin-right: 1rem; color: #94a3b8; font-size: 1.25rem; }
    .q-input { border: none; background: transparent; font-size: 0.95rem; color: #334155; width: 100%; font-weight: 400; }
    .q-input:focus { outline: none; border-bottom: 1px solid #64748b; }
    .btn-remove-q { color: #94a3b8; transition: color 0.2s; border: none; background: none; }
    .btn-remove-q:hover { color: #ef4444; }
    .ghost-class { opacity: 0.3; background: #f1f5f9; border: 2px dashed #cbd5e1; }
    
    /* Order Badge */
    .q-order-label {
        color: #64748b;
        font-size: 0.85rem;
        background: #f1f5f9;
        padding: 4px 12px;
        border-radius: 6px;
        margin-right: 1rem;
        min-width: 70px;
        text-align: center;
    }
</style>

<div class="edit-header-section shadow-sm">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1" style="color: #1e293b;">
                    <?= htmlspecialchars($subject['thainame']) ?>
                </h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 px-2 py-1" style="font-size: 0.75rem;">
                        <?= htmlspecialchars($subject['code']) ?>
                    </span>
                    <small class="text-muted border-start ps-2">ID: <?= htmlspecialchars($subject['subject_id']) ?></small>
                </div>
            </div>
            <a href="subjects" class="btn btn-link text-decoration-none text-muted p-0">
                <i class="bi bi-x-lg me-1"></i> ย้อนกลับ
            </a>
        </div>
    </div>
</div>

<div class="container" style="margin-bottom: 120px;">
    <form id="sortableForm">
        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['subject_id']) ?>">

        <?php
        $groupedQuestions = [];
        foreach ($questions as $q) {
            $groupedQuestions[$q['topic']][] = $q;
        }
        ?>

        <?php foreach ($groupedQuestions as $topic => $items): ?>
            <div class="topic-group">
                <div class="topic-header">
                    <i class="bi bi-list-check me-2"></i> <?= htmlspecialchars($topic) ?>
                    <span class="ms-auto opacity-75 fw-light" style="font-size: 0.75rem;">
                        ทั้งหมด <?= count($items) ?> ข้อ
                    </span>
                </div>

                <div class="question-list py-2">
                    <?php foreach ($items as $q): ?>
                        <div class="question-item shadow-none">
                            <div class="drag-handle"><i class="bi bi-grip-vertical"></i></div>

                            <div class="flex-grow-1 d-flex align-items-center">
                                <input type="hidden" name="question_id[]" value="<?= $q['id'] ?>">
                                <input type="hidden" name="topic[]" value="<?= htmlspecialchars($topic) ?>">

                                <span class="q-order-label fw-medium">
                                    ลำดับ <?= $q['order'] ?>
                                </span>

                                <input type="text" name="question_text[]" class="q-input"
                                    value="<?= htmlspecialchars($q['text']) ?>" placeholder="ระบุเนื้อหาคำถาม...">
                            </div>

                            <div class="ms-2">
                                <button type="button" class="btn-remove-q" title="ลบออก">
                                    <i class="bi bi-dash-circle"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="floating-save-bar d-flex justify-content-between align-items-center">
            <div class="text-muted small d-none d-md-block">
                <i class="bi bi-info-circle me-1"></i> ลากเพื่อจัดเรียงลำดับใหม่
            </div>
            <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 fw-medium shadow-sm">
                บันทึกการเปลี่ยนแปลง
            </button>
        </div>
    </form>
</div>

<script>
    // ตั้งค่า Drag & Drop
    document.querySelectorAll('.question-list').forEach(el => {
        new Sortable(el, {
            handle: '.drag-handle',
            animation: 200,
            ghostClass: 'ghost-class',
            onEnd: function() {
                updateOrders();
            }
        });
    });

    function updateOrders() {
        const labels = document.querySelectorAll('.q-order-label');
        labels.forEach((label, index) => {
            label.innerText = `ข้อที่ ${index + 1}`;
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-q')) {
            if (confirm('คุณต้องการลบคำถามนี้ใช่หรือไม่?')) {
                e.target.closest('.question-item').remove();
                updateOrders();
            }
        }
    });
</script> -->

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
        margin-bottom: 2.5rem;
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

    .ghost-class {
        opacity: 0.3;
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
    }
</style>

<div class="edit-header-section shadow-sm">
    <div class="container">
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
    <form id="sortableForm">
        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['subject_id']) ?>">

        <?php
        $groupedQuestions = [];
        foreach ($questions as $q) {
            $groupedQuestions[$q['topic']][] = $q;
        }
        ?>

        <?php foreach ($groupedQuestions as $topic => $items): ?>
            <div class="topic-group">
                <div class="topic-header">
                    <i class="bi bi-list-task me-2"></i> หมวดหมู่: <?= htmlspecialchars($topic) ?>
                    <span class="ms-auto opacity-75 fw-light" style="font-size: 0.8rem;">
                        จำนวน <?= count($items) ?> ข้อ
                    </span>
                </div>

                <div class="question-list py-2" data-topic="<?= htmlspecialchars($topic) ?>">
                    <?php foreach ($items as $q): ?>
                        <div class="question-item shadow-none">
                            <div class="drag-handle"><i class="bi bi-grip-vertical"></i></div>

                            <div class="flex-grow-1 d-flex align-items-center">
                                <input type="hidden" name="question_id[]" value="<?= $q['id'] ?>">
                                <input type="hidden" name="topic[]" value="<?= htmlspecialchars($topic) ?>">

                                <span class="q-order-label fw-medium">ข้อที่ <?= $q['order'] ?></span>

                                <input type="text" name="question_text[]" class="q-input"
                                    value="<?= htmlspecialchars($q['text']) ?>" placeholder="ระบุเนื้อหาคำถาม...">
                            </div>

                            <div class="ms-2">
                                <button type="button" class="btn-remove-q" title="ลบออก">
                                    <i class="bi bi-dash-circle"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

<script>
    // ตั้งค่า SortableJS
    document.querySelectorAll('.question-list').forEach(el => {
        new Sortable(el, {
            handle: '.drag-handle',
            animation: 250,
            ghostClass: 'ghost-class',
            onEnd: updateOrders
        });
    });

    function updateOrders() {
        const labels = document.querySelectorAll('.q-order-label');
        labels.forEach((label, index) => {
            label.innerText = `ข้อที่ ${index + 1}`;
        });
    }

    document.addEventListener('click', e => {
        if (e.target.closest('.btn-remove-q')) {
            if (confirm('คุณต้องการลบคำถามนี้ใช่หรือไม่?')) {
                e.target.closest('.question-item').remove();
                updateOrders();
            }
        }
    });
</script>