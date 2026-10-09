<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-sliders text-primary me-2"></i>ตั้งค่า Google OAuth 2.0 & ระบบความปลอดภัย</h4>
        <p class="text-muted small mb-0">กำหนดการเข้าสู่ระบบด้วย Google Account ของสถานศึกษา และจำกัดการเข้าถึงเฉพาะผู้ที่ได้รับอนุญาต</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <form id="oauthSettingsForm" onsubmit="saveOauthConfig(event)">
                    <!-- Client ID -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Google Client ID <span class="text-danger">*</span></label>
                        <input type="text" name="google_client_id" class="form-control font-monospace" value="<?= esc($oauthConfig->google_client_id ?? '') ?>" placeholder="1234567890-abcdef.apps.googleusercontent.com" required>
                        <div class="form-text small">ได้จาก Google Cloud Console &gt; APIs & Services &gt; Credentials</div>
                    </div>

                    <!-- Client Secret -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Google Client Secret (ถ้ามี)</label>
                        <input type="password" name="google_client_secret" class="form-control font-monospace" value="<?= esc($oauthConfig->google_client_secret ?? '') ?>" placeholder="GOCSPX-...">
                    </div>

                    <!-- Allowed Domain -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">โดเมนอีเมลที่ได้รับอนุญาต (Allowed Domains)</label>
                        <input type="text" name="allowed_domains" class="form-control font-monospace" value="<?= esc($oauthConfig->allowed_domains ?? 'skj.ac.th') ?>" placeholder="เช่น skj.ac.th (คั่นด้วยจุลภาคหากมีหลายโดเมน)">
                        <div class="form-text small">เช่น <code>skj.ac.th</code> บัญชี Google ที่ลงท้ายด้วย @skj.ac.th เท่านั้นจึงจะสามารถเข้าสู่ระบบได้</div>
                    </div>

                    <!-- Auto Register -->
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-4">
                        <div>
                            <div class="fw-semibold text-dark">ลงทะเบียนเจ้าหน้าที่ใหม่อัตโนมัติ (Auto Register)</div>
                            <div class="text-muted small">เมื่อครูหรือบุคลากรที่มีอีเมล @skj.ac.th กดเข้าสู่ระบบครั้งแรก จะสร้างบัญชี Agent ให้อัตโนมัติ</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" name="auto_register_domain" value="1" <?= (($oauthConfig->auto_register_domain ?? 1) == 1) ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: var(--skj-pink); border-color: var(--skj-pink);">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการตั้งค่า
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h6 class="fw-bold mb-3"><i class="fa-brands fa-google text-danger me-2"></i>วิธีตั้งค่า Google Cloud Console</h6>
                <ol class="small text-secondary ps-3 mb-0" style="line-height: 1.8;">
                    <li>ไปที่ <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-decoration-none">Google Cloud Console</a></li>
                    <li>สร้าง <strong>OAuth 2.0 Client ID</strong> ประเภท <strong>Web application</strong></li>
                    <li>ในหัวข้อ <strong>Authorized JavaScript origins</strong> ให้เพิ่ม:
                        <div class="bg-light p-2 rounded my-1 font-monospace text-dark">
                            <?= base_url() ?><br>
                            https://localhost:8071
                        </div>
                    </li>
                    <li>ในหัวข้อ <strong>Authorized redirect URIs</strong> ให้เพิ่ม:
                        <div class="bg-light p-2 rounded my-1 font-monospace text-dark">
                            <?= base_url('auth/login') ?>
                        </div>
                    </li>
                    <li>คัดลอก <strong>Client ID</strong> นำมาวางในช่องด้านซ้าย</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
    function saveOauthConfig(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`${BASE_URL}/settings/oauth/save`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success');
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            });
    }
</script>
<?= $this->endSection() ?>
