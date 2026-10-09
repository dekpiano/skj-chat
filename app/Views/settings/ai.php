<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-robot text-info me-2"></i>ตั้งค่า Google Gemini AI Assistant</h4>
            <p class="text-muted small mb-0">ปรับแต่งการทำงานของบอท "น้องกุหลาบ" ผู้ช่วยอัจฉริยะในการตอบแชทนักเรียนและผู้ปกครอง</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#testAiModal">
                <i class="fa-solid fa-flask me-1"></i> ทดสอบการตอบของ AI
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <form id="aiSettingsForm" onsubmit="saveAiConfig(event)">
                    <!-- Status Toggle -->
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-4">
                        <div>
                            <div class="fw-semibold text-dark">เปิดใช้งาน AI Chatbot</div>
                            <div class="text-muted small">ให้ AI ตอบข้อความอัตโนมัติเมื่อผู้ใช้ทักเข้ามา และไม่มีเจ้าหน้าที่กดรับช่วงต่อ</div>
                        </div>
                        <div class="form-check form-switch fs-4 mb-0">
                            <input class="form-check-input" type="checkbox" name="ai_status" value="on" <?= (($aiConfig->ai_status ?? 'off') === 'on') ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <!-- API Key -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Google Gemini API Key <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="geminiApiKeyInput" name="ai_api_key" class="form-control font-monospace" value="<?= esc($aiConfig->ai_api_key ?? '') ?>" placeholder="AIzaSy..." required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('geminiApiKeyInput')">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text small">สามารถรับ API Key ได้ฟรีจาก <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-decoration-none">Google AI Studio</a></div>
                    </div>

                    <!-- Model Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">รุ่นโมเดล (Gemini Model)</label>
                        <select name="ai_model" class="form-select">
                            <option value="gemini-3.5-flash" <?= (($aiConfig->ai_model ?? 'gemini-3.5-flash') === 'gemini-3.5-flash') ? 'selected' : '' ?>>Gemini 3.5 Flash (เสถียร รวดเร็ว แนะนำสำหรับการใช้งานจริง)</option>
                            <option value="gemini-3.5-flash-lite" <?= (($aiConfig->ai_model ?? '') === 'gemini-3.5-flash-lite') ? 'selected' : '' ?>>Gemini 3.5 Flash Lite (ความเร็วสูง ประหยัดทรัพยากร)</option>
                            <option value="gemini-flash-latest" <?= (($aiConfig->ai_model ?? '') === 'gemini-flash-latest') ? 'selected' : '' ?>>Gemini Flash Latest (อัปเดตโมเดลล่าสุดอัตโนมัติจาก Google)</option>
                            <option value="gemini-3.1-flash-lite" <?= (($aiConfig->ai_model ?? '') === 'gemini-3.1-flash-lite') ? 'selected' : '' ?>>Gemini 3.1 Flash Lite (รุ่นสำรองเบาพิเศษ)</option>
                        </select>
                    </div>

                    <!-- System Prompt -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">คำสั่งหลักและการกำหนดบทบาท (System Prompt / Persona)</label>
                        <textarea name="ai_system_prompt" class="form-control small font-monospace" rows="12"><?= esc($aiConfig->ai_system_prompt ?? '') ?></textarea>
                        <div class="form-text small">กำหนดบุคลิก กฎการตอบ และแนวทางการแนะนำเบอร์ติดต่อของสถานศึกษา</div>
                    </div>

                    <!-- Advanced Parameters -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Temperature (ความคิดสร้างสรรค์: 0.0 - 1.0)</label>
                            <input type="number" step="0.1" min="0" max="1" name="ai_temperature" class="form-control" value="<?= esc($aiConfig->ai_temperature ?? 0.7) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Max Output Tokens (ความยาวสูงสุดคำตอบ)</label>
                            <input type="number" step="50" min="100" max="2000" name="ai_max_tokens" class="form-control" value="<?= esc($aiConfig->ai_max_tokens ?? 500) ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: var(--skj-pink); border-color: var(--skj-pink);">
                        <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการตั้งค่า
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-lightbulb text-warning me-2"></i>เคล็ดลับการตั้งค่าบอท</h6>
                <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.8;">
                    <li>ระบบจะนำข้อมูลจาก **คลังความรู้ (Knowledge Base)** แนบส่งให้ Gemini ประมวลผลพร้อมกันในทุกคำถาม</li>
                    <li>หากผู้ดูแลระบบตอบแชทด้วยตนเอง บอทจะ **พักการทำงานอัตโนมัติ** ในเซสชันนั้นทันที เพื่อไม่ให้ส่งคำตอบแทรกแอดมิน</li>
                    <li>แนะนำให้ตั้งเบอร์โทรศัพท์ของโรงเรียนและเวลาทำการไว้ใน System Prompt เพื่อให้บอทส่งต่อเรื่องสำคัญได้อย่างถูกต้อง</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Test AI Response -->
<div class="modal fade" id="testAiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-flask text-primary me-2"></i>ทดสอบการตอบของ AI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">คำถามทดสอบ</label>
                    <div class="input-group">
                        <input type="text" id="testAiPromptInput" class="form-control" value="โรงเรียนเปิดรับสมัคร ม.1 ช่วงไหน และต้องเตรียมเอกสารอะไรบ้างครับ">
                        <button class="btn btn-primary" type="button" onclick="runAiTest()" style="background: var(--skj-pink); border-color: var(--skj-pink);">
                            <i class="fa-solid fa-paper-plane me-1"></i> ทดสอบส่ง
                        </button>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border" style="min-height: 140px;">
                    <div class="fw-semibold text-secondary small mb-2"><i class="fa-solid fa-robot me-1"></i> คำตอบจาก Gemini:</div>
                    <div id="testAiResultArea" class="small text-dark" style="white-space: pre-wrap;">คลิกปุ่ม "ทดสอบส่ง" เพื่อดูผลการตอบ...</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function saveAiConfig(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`${BASE_URL}/settings/ai/save`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('บันทึกสำเร็จ', data.message, 'success');
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            });
    }

    function runAiTest() {
        const prompt = document.getElementById('testAiPromptInput').value.trim();
        if (!prompt) return;

        const resultArea = document.getElementById('testAiResultArea');
        resultArea.innerHTML = '<span class="text-muted"><i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังสอบถาม Gemini...</span>';

        const formData = new FormData();
        formData.append('prompt', prompt);

        fetch(`${BASE_URL}/settings/ai/test`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    resultArea.innerText = data.reply;
                } else {
                    resultArea.innerHTML = `<span class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> ผิดพลาด: ${data.message}</span>`;
                }
            })
            .catch(err => {
                resultArea.innerHTML = `<span class="text-danger">ไม่สามารถเชื่อมต่อได้</span>`;
            });
    }
</script>
<?= $this->endSection() ?>
