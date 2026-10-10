<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-brands fa-telegram text-primary me-2"></i>ตั้งค่าการแจ้งเตือน Telegram</h4>
            <p class="text-muted small mb-0">รับข้อความแจ้งเตือนผ่านกลุ่ม Telegram ทันทีที่มีผู้ใช้ทักเข้ามาหรือต้องการติดต่อเจ้าหน้าที่</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="testTelegramBtn" onclick="testTelegramAlert()">
                <i class="fa-solid fa-paper-plane me-1"></i> ส่งข้อความทดสอบ
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <form id="telegramSettingsForm" onsubmit="saveTelegramConfig(event)">
                    <!-- Toggle Switch -->
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-4">
                        <div>
                            <div class="fw-semibold text-dark">เปิดการแจ้งเตือน Telegram</div>
                            <div class="text-muted small">ส่งข้อความแจ้งเตือนเมื่อมีผู้ใช้ใหม่เริ่มแชท</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" name="telegram_status" value="on" <?= (($tgConfig->telegram_status ?? 'on') === 'on') ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <!-- Bot Token -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Telegram Bot Token <span class="text-danger">*</span></label>
                        <input type="password" id="tgBotTokenInput" name="telegram_bot_token" class="form-control font-monospace" value="<?= esc($tgConfig->telegram_bot_token ?? '') ?>" placeholder="123456789:ABCDef..." required>
                        <div class="form-text small">สร้าง Bot และรับ Token จาก <a href="https://t.me/BotFather" target="_blank" class="text-decoration-none">@BotFather</a> บน Telegram</div>
                    </div>

                    <!-- Chat ID -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Telegram Chat ID / Group ID <span class="text-danger">*</span></label>
                        <input type="text" name="telegram_chat_id" class="form-control font-monospace" value="<?= esc($tgConfig->telegram_chat_id ?? '') ?>" placeholder="เช่น -100123456789 หรือ 12345678" required>
                        <div class="form-text small">ไอดีของกลุ่มหรือผู้รับ (หากเป็นกลุ่มมักขึ้นต้นด้วยเครื่องหมายลบ เช่น <code>-5407364488</code>)</div>
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">ชื่อกลุ่ม / คำอธิบายการแจ้งเตือน</label>
                        <input type="text" name="telegram_chat_title" class="form-control" value="<?= esc($tgConfig->telegram_chat_title ?? 'SKJ Live Chat Notifications') ?>">
                    </div>

                    <div class="alert alert-info-subtle border-0 rounded-3 small p-3 mb-4 d-flex align-items-start gap-2" style="font-size: 0.82rem; background: #e0f2fe; color: #0369a1;">
                        <i class="fa-solid fa-bell mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>การทำงานของการแจ้งเตือน:</strong>
                            เมื่อเปิดใช้งาน ระบบจะส่งข้อความแจ้งเตือนพร้อมปุ่มและลิงก์เปิดห้องแชทในระบบ Admin ไปยังกลุ่ม Telegram ทุกครั้งที่มีผู้ใช้ใหม่เริ่มแชท หรือมีข้อความใหม่จากผู้ติดต่อเข้ามา
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
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-circle-info text-info me-2"></i>วิธีดึง Chat ID ของกลุ่ม Telegram</h6>
                <ol class="small text-secondary ps-3 mb-0" style="line-height: 1.8;">
                    <li>สร้างกลุ่ม Telegram สำหรับทีมงานหรือครูเวรตอบแชท</li>
                    <li>ดึงบอทที่คุณสร้างไว้เข้าไปในกลุ่มนั้น</li>
                    <li>ตั้งค่าให้บอทเป็นแอดมินกลุ่ม (Group Admin) เพื่อให้บอทสามารถอ่านและส่งข้อความได้</li>
                    <li>ส่งข้อความอะไรก็ได้ลงในกลุ่ม 1 ข้อความ</li>
                    <li>พิมพ์เช็กไอดีกลุ่มผ่าน <a href="https://t.me/RawDataBot" target="_blank" class="text-decoration-none">@RawDataBot</a> หรือเรียก API <code>https://api.telegram.org/bot[TOKEN]/getUpdates</code></li>
                    <li>นำค่า <code>id</code> ที่ขึ้นต้นด้วยลบ (เช่น -5407364488) มากรอกในช่อง Chat ID</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
    function saveTelegramConfig(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        if (window.setButtonLoading) window.setButtonLoading(submitBtn, true, 'กำลังบันทึก...');
        const formData = new FormData(form);

        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`${BASE_URL}/settings/telegram/save`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success');
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            })
            .catch(() => {
                if (window.setButtonLoading) window.setButtonLoading(submitBtn, false);
                Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            });
    }

    function testTelegramAlert() {
        const btn = document.getElementById('testTelegramBtn');
        if (window.setButtonLoading) window.setButtonLoading(btn, true, 'กำลังส่ง...');

        Swal.fire({ title: 'กำลังส่งข้อความทดสอบ...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`${BASE_URL}/settings/telegram/test`, { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (window.setButtonLoading) window.setButtonLoading(btn, false);
                if (data.status === 'success') {
                    Swal.fire('ส่งสำเร็จ!', data.message, 'success');
                } else {
                    Swal.fire('ส่งไม่สำเร็จ', data.message, 'error');
                }
            })
            .catch(err => {
                if (window.setButtonLoading) window.setButtonLoading(btn, false);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อได้', 'error');
            });
    }
</script>
<?= $this->endSection() ?>
