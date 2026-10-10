<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-brain text-primary me-2"></i>คลังความรู้ AI (Knowledge Base)</h4>
            <p class="text-muted small mb-0">รวบรวมข้อมูล หลักสูตร ระเบียบการ และลิงก์เว็บไซต์ของโรงเรียนเพื่อให้ Google Gemini ตอบคำถามได้อย่างแม่นยำ</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#addUrlModal">
                <i class="fa-solid fa-globe me-1"></i> เพิ่มจาก URL เว็บไซต์
            </button>
            <button class="btn btn-outline-success btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                <i class="fa-solid fa-file-arrow-up me-1"></i> อัปโหลดเอกสาร
            </button>
            <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#addTextModal" style="background: var(--skj-pink); border-color: var(--skj-pink);">
                <i class="fa-solid fa-plus me-1"></i> เพิ่มข้อความ/Q&A
            </button>
        </div>
    </div>

    <!-- Stats row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">รายการทั้งหมด</div>
                        <h3 class="fw-bold mb-0 text-dark"><?= $stats['total'] ?></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-primary-subtle text-primary"><i class="fa-solid fa-database fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">กำลังใช้งาน (Active)</div>
                        <h3 class="fw-bold mb-0 text-success"><?= $stats['active'] ?></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-success-subtle text-success"><i class="fa-solid fa-circle-check fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">หน้าเว็บไซต์ (URLs)</div>
                        <h3 class="fw-bold mb-0 text-info"><?= $stats['urlCount'] ?></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-info-subtle text-info"><i class="fa-solid fa-globe fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">ไฟล์เอกสาร (Files)</div>
                        <h3 class="fw-bold mb-0 text-warning"><?= $stats['docCount'] ?></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-warning-subtle text-warning"><i class="fa-solid fa-file-lines fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th>หัวข้อข้อมูล</th>
                        <th>ประเภท</th>
                        <th>ขนาด/ความยาว</th>
                        <th>สถานะ</th>
                        <th>วันที่เพิ่ม</th>
                        <th class="text-end" width="100">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $idx => $item): ?>
                            <tr>
                                <td class="text-muted small"><?= $idx + 1 ?></td>
                                <td>
                                    <?php
                                        $catMatch = '';
                                        if (preg_match('/^【หมวดหมู่:\s*([^】]+)】/u', $item->content ?? '', $mCat)) {
                                            $catMatch = trim($mCat[1]);
                                        }
                                    ?>
                                    <div class="d-flex align-items-center gap-1 flex-wrap mb-1">
                                        <?php if ($catMatch): ?>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle small" style="font-size: 0.7rem;"><i class="fa-solid fa-folder me-1"></i><?= esc($catMatch) ?></span>
                                        <?php endif; ?>
                                        <span class="fw-semibold text-dark text-truncate" style="max-width: 300px;" title="<?= esc($item->title) ?>"><?= esc($item->title) ?></span>
                                    </div>
                                    <?php if (!empty($item->source_url)): ?>
                                        <a href="<?= esc($item->source_url) ?>" target="_blank" class="small text-decoration-none text-muted d-inline-block text-truncate" style="max-width: 320px;" title="<?= esc($item->source_url) ?>">
                                            <i class="fa-solid fa-link me-1"></i><?= esc($item->source_url) ?>
                                        </a>
                                    <?php elseif (!empty($item->file_name)): ?>
                                        <span class="small text-muted d-inline-block text-truncate" style="max-width: 320px;" title="<?= esc($item->file_name) ?>"><i class="fa-solid fa-paperclip me-1"></i><?= esc($item->file_name) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item->source_type === 'chat'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-comments me-1"></i>จากแชทสด</span>
                                    <?php elseif ($item->source_type === 'url'): ?>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="fa-solid fa-globe me-1"></i>URL เว็บ</span>
                                    <?php elseif ($item->source_type === 'file'): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="fa-solid fa-file-lines me-1"></i>เอกสาร</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="fa-solid fa-align-left me-1"></i>ข้อความ</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= number_format($item->char_count) ?> ตัวอักษร</td>
                                <td>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" <?= ($item->status === 'on') ? 'checked' : '' ?> onchange="toggleKnowledge(<?= $item->knowledge_id ?>)">
                                    </div>
                                </td>
                                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($item->created_at)) ?></td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-icon-sm btn-outline-danger" onclick="deleteKnowledge(<?= $item->knowledge_id ?>)" title="ลบข้อมูล">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-1 text-secondary mb-2 d-block"></i>
                                ยังไม่มีข้อมูลในคลังความรู้ AI คลิกปุ่มด้านบนเพื่อเพิ่มข้อมูล
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add URL -->
<div class="modal fade" id="addUrlModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content border-0 shadow rounded-4" onsubmit="submitUrlForm(event)">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-globe text-primary me-2"></i>ดึงข้อมูลจากเว็บไซต์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">URL เว็บไซต์ <span class="text-danger">*</span></label>
                    <input type="url" name="url" class="form-control" placeholder="https://skj.ac.th/about" required>
                    <div class="form-text small">ระบบจะเข้าไปดึงเนื้อหาข้อความบนหน้าเว็บมาให้อัตโนมัติ</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">ชื่อหัวข้อ (ไม่ระบุได้)</label>
                    <input type="text" name="title" class="form-control" placeholder="เช่น ข้อมูลเกี่ยวกับโรงเรียน">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" style="background: var(--skj-pink); border-color: var(--skj-pink);">เริ่มดึงข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Upload Doc -->
<div class="modal fade" id="uploadDocModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content border-0 shadow rounded-4" onsubmit="submitDocForm(event)">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-arrow-up text-success me-2"></i>อัปโหลดไฟล์เอกสาร</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">เลือกไฟล์ <span class="text-danger">*</span></label>
                    <input type="file" name="doc_file" class="form-control" accept=".txt,.pdf,.docx,.csv" required>
                    <div class="form-text small">รองรับไฟล์ .txt, .pdf, .docx, .csv</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">ชื่อหัวข้อเอกสาร</label>
                    <input type="text" name="title" class="form-control" placeholder="เช่น ระเบียบการรับสมัครนักเรียนใหม่">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success">อัปโหลด</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Text -->
<div class="modal fade" id="addTextModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content border-0 shadow rounded-4" onsubmit="submitTextForm(event)">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-nib text-primary me-2"></i>เพิ่มข้อความความรู้ / Q&A</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">หัวข้อความรู้ <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="เช่น การชำระเงินค่าเทอมและเลขบัญชี" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">เนื้อหารายละเอียด <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control font-monospace small" rows="8" placeholder="กรอกรายละเอียด หรือรูปแบบถาม-ตอบ Q&A สำหรับสอน AI บอท..." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" style="background: var(--skj-pink); border-color: var(--skj-pink);">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<script>
    function submitUrlForm(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        if (window.setButtonLoading) window.setButtonLoading(submitBtn, true, 'กำลังดึงข้อมูล...');
        const formData = new FormData(form);

        Swal.fire({ title: 'กำลังดึงข้อมูลเว็บไซต์...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`${BASE_URL}/knowledge/save-url`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(() => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            });
    }

    function submitDocForm(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        if (window.setButtonLoading) window.setButtonLoading(submitBtn, true, 'กำลังอัปโหลด...');
        const formData = new FormData(form);

        Swal.fire({ title: 'กำลังอัปโหลดเอกสาร...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`${BASE_URL}/knowledge/upload-file`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(() => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            });
    }

    function submitTextForm(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        if (window.setButtonLoading) window.setButtonLoading(submitBtn, true, 'กำลังบันทึก...');
        const formData = new FormData(form);

        fetch(`${BASE_URL}/knowledge/save-text`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(() => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            });
    }

    function toggleKnowledge(id) {
        fetch(`${BASE_URL}/knowledge/toggle/${id}`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถเปลี่ยนสถานะได้', 'error');
                }
            });
    }

    function deleteKnowledge(id) {
        Swal.fire({
            title: 'ยืนยันการลบข้อมูล?',
            text: 'ข้อมูลนี้จะไม่ถูกนำไปใช้ในการตอบคำถามของ AI อีกต่อไป',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e91e63',
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${BASE_URL}/knowledge/delete/${id}`, { method: 'POST' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('ลบสำเร็จ', data.message, 'success').then(() => location.reload());
                        }
                    });
            }
        });
    }
</script>
<?= $this->endSection() ?>
