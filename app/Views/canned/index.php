<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-bolt text-warning me-2"></i>ข้อความตอบกลับด่วน (Canned Replies)</h4>
            <p class="text-muted small mb-0">สร้างคีย์ลัด เช่น <code>/hello</code>, <code>/reg</code>, <code>/contact</code> เพื่อให้เจ้าหน้าที่กดตอบได้ใน 1 วินาที</p>
        </div>
        <div>
            <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#cannedModal" onclick="prepareAddCanned()" style="background: var(--skj-pink); border-color: var(--skj-pink);">
                <i class="fa-solid fa-plus me-1"></i> เพิ่มข้อความด่วน
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="120">คีย์ลัด (Shortcut)</th>
                        <th width="200">หัวข้อข้อความ</th>
                        <th>ข้อความตอบกลับ</th>
                        <th class="text-end" width="100">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($replies)): ?>
                        <?php foreach ($replies as $r): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-pink-subtle text-danger font-monospace px-2 py-1" style="background:#fce4ec;"><?= esc($r->shortcut) ?></span>
                                </td>
                                <td class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="<?= esc($r->title) ?>"><?= esc($r->title) ?></td>
                                <td class="small text-secondary word-break-all" style="white-space: pre-wrap; max-width: 400px;"><?= esc($r->message) ?></td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-icon-sm btn-outline-secondary me-1" onclick='editCanned(<?= json_encode($r) ?>)' title="แก้ไข">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon-sm btn-outline-danger" onclick="deleteCanned(<?= $r->reply_id ?>)" title="ลบ">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                ยังไม่มีข้อความด่วน คลิกปุ่ม "เพิ่มข้อความด่วน" เพื่อสร้างเทมเพลตคำตอบ
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add/Edit Canned -->
<div class="modal fade" id="cannedModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content border-0 shadow rounded-4" onsubmit="submitCannedForm(event)">
            <input type="hidden" name="reply_id" id="replyIdInput" value="">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="cannedModalTitle">เพิ่มข้อความด่วน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">คีย์ลัด (Shortcut) <span class="text-danger">*</span></label>
                    <input type="text" name="shortcut" id="replyShortcutInput" class="form-control font-monospace" placeholder="/hello หรือ /fee" required>
                    <div class="form-text small">ขึ้นต้นด้วยเครื่องหมาย / เพื่อให้พิมพ์เรียกใช้ง่ายในช่องแชท</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">หัวข้อคำตอบ <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="replyTitleInput" class="form-control" placeholder="เช่น ทักทายต้อนรับ หรือ แจ้งเวลาทำการ" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">ข้อความตอบกลับ <span class="text-danger">*</span></label>
                    <textarea name="message" id="replyMessageInput" class="form-control" rows="5" placeholder="กรอกข้อความที่ต้องการส่งให้ผู้ใช้..." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" style="background: var(--skj-pink); border-color: var(--skj-pink);">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<script>
    function prepareAddCanned() {
        document.getElementById('cannedModalTitle').innerText = 'เพิ่มข้อความด่วน';
        document.getElementById('replyIdInput').value = '';
        document.getElementById('replyShortcutInput').value = '';
        document.getElementById('replyTitleInput').value = '';
        document.getElementById('replyMessageInput').value = '';
    }

    function editCanned(r) {
        document.getElementById('cannedModalTitle').innerText = 'แก้ไขข้อความด่วน';
        document.getElementById('replyIdInput').value = r.reply_id;
        document.getElementById('replyShortcutInput').value = r.shortcut;
        document.getElementById('replyTitleInput').value = r.title;
        document.getElementById('replyMessageInput').value = r.message;

        new bootstrap.Modal(document.getElementById('cannedModal')).show();
    }

    function submitCannedForm(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        fetch(`${BASE_URL}/canned/save`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            });
    }

    function deleteCanned(id) {
        Swal.fire({
            title: 'ยืนยันการลบข้อความด่วน?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e91e63',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${BASE_URL}/canned/delete/${id}`, { method: 'POST' })
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
