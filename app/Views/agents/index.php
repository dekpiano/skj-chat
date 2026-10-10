<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-users-gear text-primary me-2"></i>จัดการเจ้าหน้าที่ตอบแชท (Agent Roster)</h4>
            <p class="text-muted small mb-0">เพิ่ม ลบ หรือแก้ไขสิทธิ์การเข้าถึงระบบแชทของครูและเจ้าหน้าที่โรงเรียน</p>
        </div>
        <div>
            <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#agentModal" onclick="prepareAddAgent()" style="background: var(--skj-pink); border-color: var(--skj-pink);">
                <i class="fa-solid fa-user-plus me-1"></i> เพิ่มเจ้าหน้าที่ใหม่
            </button>
        </div>
    </div>

    <!-- Agent Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>เจ้าหน้าที่</th>
                        <th>อีเมลสถานศึกษา</th>
                        <th>บทบาท (Role)</th>
                        <th>สถานะการออนไลน์</th>
                        <th>สถานะบัญชี</th>
                        <th>เข้าสู่ระบบล่าสุด</th>
                        <th class="text-end" width="120">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($agents)): ?>
                        <?php foreach ($agents as $idx => $ag): ?>
                            <tr>
                                <td class="text-muted small"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                        <img src="<?= esc($ag->avatar ?: ('https://ui-avatars.com/api/?name=' . urlencode($ag->fullname) . '&background=fce4ec&color=c2185b')) ?>" class="rounded-circle avatar-img flex-shrink-0" width="36" height="36" style="object-fit: cover;" alt="Avatar" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode(esc($ag->fullname)) ?>&background=fce4ec&color=c2185b';">
                                        <div style="min-width: 0;">
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;" title="<?= esc($ag->fullname) ?>"><?= esc($ag->fullname) ?></div>
                                            <span class="text-muted small">ID: #<?= $ag->agent_id ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-monospace small text-truncate" style="max-width: 200px;"><?= esc($ag->email) ?></td>
                                <td>
                                    <?php if ($ag->role === 'superadmin'): ?>
                                        <span class="badge bg-danger-subtle text-danger">Super Admin</span>
                                    <?php elseif ($ag->role === 'admin'): ?>
                                        <span class="badge bg-primary-subtle text-primary">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Agent</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="agent-status-badge <?= esc($ag->online_status) ?> py-1" style="font-size: 0.75rem;">
                                        <span class="status-dot"></span>
                                        <?= ($ag->online_status === 'online') ? 'ออนไลน์' : (($ag->online_status === 'busy') ? 'ไม่สะดวก' : 'ออฟไลน์') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($ag->status === 'active'): ?>
                                        <span class="badge bg-success-subtle text-success">ปกติ</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">ระงับใช้งาน</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?= $ag->last_login_at ? date('d/m/Y H:i', strtotime($ag->last_login_at)) : '-' ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-icon-sm btn-outline-secondary me-1" onclick='editAgent(<?= json_encode($ag) ?>)' title="แก้ไข">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <?php if ($ag->role !== 'superadmin' && $ag->agent_id != ($currentAgent->agent_id ?? 0)): ?>
                                        <button class="btn btn-sm btn-icon-sm btn-outline-danger" onclick="deleteAgent(<?= $ag->agent_id ?>)" title="ลบ">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add/Edit Agent -->
<div class="modal fade" id="agentModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content border-0 shadow rounded-4" onsubmit="submitAgentForm(event)">
            <input type="hidden" name="agent_id" id="agentIdInput" value="">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="agentModalTitle">เพิ่มเจ้าหน้าที่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                    <input type="text" name="fullname" id="agentFullnameInput" class="form-control" placeholder="เช่น ครูสมชาย ใจดี" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">อีเมลสถานศึกษา (@skj.ac.th) <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="agentEmailInput" class="form-control font-monospace" placeholder="example@skj.ac.th" required>
                    <div class="form-text small">เจ้าหน้าที่จะใช้อีเมลนี้ในการกดปุ่ม Login ด้วย Google</div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">บทบาท (Role)</label>
                        <select name="role" id="agentRoleSelect" class="form-select">
                            <option value="agent">Agent (ตอบแชท)</option>
                            <option value="admin">Admin (จัดการระบบ)</option>
                            <option value="superadmin">Super Admin (สูงสุด)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">สถานะบัญชี</label>
                        <select name="status" id="agentStatusSelect" class="form-select">
                            <option value="active">ปกติ (Active)</option>
                            <option value="inactive">ระงับ (Inactive)</option>
                        </select>
                    </div>
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
    function prepareAddAgent() {
        document.getElementById('agentModalTitle').innerText = 'เพิ่มเจ้าหน้าที่ใหม่';
        document.getElementById('agentIdInput').value = '';
        document.getElementById('agentFullnameInput').value = '';
        document.getElementById('agentEmailInput').value = '';
        document.getElementById('agentRoleSelect').value = 'agent';
        document.getElementById('agentStatusSelect').value = 'active';
    }

    function editAgent(ag) {
        document.getElementById('agentModalTitle').innerText = 'แก้ไขข้อมูลเจ้าหน้าที่';
        document.getElementById('agentIdInput').value = ag.agent_id;
        document.getElementById('agentFullnameInput').value = ag.fullname;
        document.getElementById('agentEmailInput').value = ag.email;
        document.getElementById('agentRoleSelect').value = ag.role;
        document.getElementById('agentStatusSelect').value = ag.status;

        new bootstrap.Modal(document.getElementById('agentModal')).show();
    }

    function submitAgentForm(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        if (window.setButtonLoading) window.setButtonLoading(submitBtn, true, 'กำลังบันทึก...');
        const formData = new FormData(form);

        fetch(`${BASE_URL}/agents/save`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            })
            .catch(() => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            });
    }

    function deleteAgent(id) {
        Swal.fire({
            title: 'ยืนยันการลบเจ้าหน้าที่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e91e63',
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${BASE_URL}/agents/delete/${id}`, { method: 'POST' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('ลบสำเร็จ', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด', data.message, 'error');
                        }
                    });
            }
        });
    }
</script>
<?= $this->endSection() ?>
