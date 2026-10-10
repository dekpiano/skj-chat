<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-code text-primary me-2"></i>โค้ดสำหรับติดตั้ง Live Chat Widget</h4>
        <p class="text-muted small mb-0">คัดลอกโค้ดสคริปต์ด้านล่างนี้ไปวางก่อนปิดแท็ก <code>&lt;/body&gt;</code> ในเว็บไซต์โรงเรียน หรือระบบย่อยอื่นๆ เพื่อเปิดใช้งานแชทสดได้ทันที</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- 1. Script Embed -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">1. โค้ด Embed สคริปต์หลัก (จำเป็นต้องใส่)</h6>
                        <small class="text-muted">นำไปวางไว้ก่อนปิดแท็ก <code>&lt;/body&gt;</code> ในทุกหน้าที่ต้องการเปิดใช้งาน</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="copySnippet('embedCodeSnippet')">
                        <i class="fa-regular fa-copy me-1"></i> คัดลอกโค้ด
                    </button>
                </div>

                <div class="bg-dark text-light p-3 rounded-3 font-monospace small position-relative" style="overflow-x: auto;" id="embedCodeSnippet">&lt;!-- SKJ Live Chat Widget --&gt;
&lt;script src="<?= rtrim(base_url(), '/') ?>/public/assets/js/skj-chat-widget.js?v=1.9" 
        data-chat-server="<?= rtrim(base_url(), '/') ?>"
        data-school-name="โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์"
        data-primary-color="#e91e63"
        async&gt;&lt;/script&gt;</div>
            </div>

            <!-- 2. Custom Buttons and Links Examples -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">2. ทำปุ่มหรือลิงก์กดเปิดแชทเอง (วางไว้ตรงไหนก็ได้)</h6>
                        <small class="text-muted">สามารถนำไปวางใน Navbar เมนู, แบนเนอร์, การ์ดแนะนำ หรือ Footer ได้ทุกที่</small>
                    </div>
                </div>

                <div class="vstack gap-3">
                    <!-- Method A: data attribute -->
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold small text-dark"><i class="fa-solid fa-tag text-pink me-1" style="color:#e91e63;"></i> วิธีที่ 1: ใส่ Attribute <code>data-skj-chat="open"</code> บนปุ่มหรือลิงก์ใดก็ได้ (ง่ายที่สุด)</span>
                            <button class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" onclick="copySnippet('codeAttrBtn')">
                                <i class="fa-regular fa-copy me-1"></i> คัดลอก
                            </button>
                        </div>
                        <div class="bg-dark text-light p-2 px-3 rounded-2 font-monospace small" id="codeAttrBtn">&lt;!-- ตัวอย่างปุ่ม Button --&gt;
&lt;button type="button" class="btn btn-primary" data-skj-chat="open"&gt;
    💬 แชทกับเรา
&lt;/button&gt;</div>
                    </div>

                    <!-- Method B: Link href="#skj-chat" -->
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold small text-dark"><i class="fa-solid fa-link text-primary me-1"></i> วิธีที่ 2: ใช้เป็นลิงก์เมนู <code>href="#skj-chat"</code> หรือ <code>href="#chat"</code> (เหมาะกับ Navbar)</span>
                            <button class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" onclick="copySnippet('codeLinkNav')">
                                <i class="fa-regular fa-copy me-1"></i> คัดลอก
                            </button>
                        </div>
                        <div class="bg-dark text-light p-2 px-3 rounded-2 font-monospace small" id="codeLinkNav">&lt;!-- ตัวอย่างลิงก์ใน Navbar หรือ Footer --&gt;
&lt;a href="#skj-chat" class="nav-link"&gt;
    &lt;i class="fa-solid fa-comments"&gt;&lt;/i&gt; ติดต่อสอบถามสด
&lt;/a&gt;</div>
                    </div>

                    <!-- Method C: JS Call -->
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold small text-dark"><i class="fa-brands fa-js text-warning me-1"></i> วิธีที่ 3: เรียกผ่านคำสั่ง JavaScript <code>SKJChat.open()</code></span>
                            <button class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" onclick="copySnippet('codeJsCall')">
                                <i class="fa-regular fa-copy me-1"></i> คัดลอก
                            </button>
                        </div>
                        <div class="bg-dark text-light p-2 px-3 rounded-2 font-monospace small" id="codeJsCall">&lt;!-- เรียกผ่าน onclick --&gt;
&lt;a href="javascript:void(0)" onclick="SKJChat.open()"&gt;สอบถามข้อมูลทันที&lt;/a&gt;

&lt;script&gt;
    // หรือสั่งเปิดใน JavaScript เมื่อมีเหตุการณ์ใดๆ
    SKJChat.open();    // สั่งเปิดหน้าต่าง
    SKJChat.close();   // สั่งปิดหน้าต่าง
    SKJChat.toggle();  // สั่งสลับเปิด/ปิด
&lt;/script&gt;</div>
                    </div>

                    <!-- Option: Hide Floating Icon -->
                    <div class="border rounded-3 p-3 bg-warning-subtle border-warning-subtle">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold small text-dark"><i class="fa-solid fa-eye-slash text-danger me-1"></i> ตัวเลือกเสริม: หากต้องการ <u>ซ่อนปุ่มวงกลมลอยมุมขวาล่าง</u> (ให้เปิดผ่านปุ่มที่เราสร้างเองเท่านั้น)</span>
                            <button class="btn btn-xs btn-outline-dark rounded-pill py-0 px-2" onclick="copySnippet('codeHideFloating')">
                                <i class="fa-regular fa-copy me-1"></i> คัดลอก
                            </button>
                        </div>
                        <div class="bg-dark text-light p-2 px-3 rounded-2 font-monospace small" id="codeHideFloating">&lt;!-- เพิ่ม data-hide-floating="true" ในแท็ก script --&gt;
&lt;script src="<?= rtrim(base_url(), '/') ?>/public/assets/js/skj-chat-widget.js?v=1.9" 
        data-chat-server="<?= rtrim(base_url(), '/') ?>"
        data-hide-floating="true"
        async&gt;&lt;/script&gt;</div>
                    </div>
                </div>
            </div>

            <!-- Guide Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h6 class="fw-bold mb-3">คำแนะนำการติดตั้งในระบบโรงเรียน</h6>
                <div class="timeline small text-secondary" style="line-height: 1.8;">
                    <div class="mb-3">
                        <strong class="text-dark">1. ติดตั้งบนเว็บหลัก (skj2025):</strong><br>
                        นำโค้ดด้านบนไปวางไว้ในไฟล์ <code>app/Views/User/layout/footer.php</code> ก่อนแท็ก <code>&lt;/body&gt;</code>
                    </div>
                    <div class="mb-3">
                        <strong class="text-dark">2. ติดตั้งบนระบบรับสมัคร (admission) หรือระบบอื่นๆ:</strong><br>
                        สามารถนำสคริปต์ตัวเดียวกันนี้ไปวางได้ทันที โดยทุกระบบจะส่งข้อความรวมเข้ามาที่หน้า Live Chat Desk แห่งนี้ที่เดียวแบบ Real-time
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 text-center mb-4">
                <h6 class="fw-bold mb-3">สถานะเซิร์ฟเวอร์ระบบแชท</h6>
                <div class="d-inline-flex align-items-center gap-2 p-2 px-3 bg-success-subtle text-success rounded-pill font-monospace small mb-3">
                    <span class="status-dot" style="background:#22c55e;"></span> API Server Active
                </div>
                <div class="small text-muted mb-2">Endpoint URL:</div>
                <code class="d-block p-2 bg-light rounded text-truncate mb-3"><?= base_url('api/widget') ?></code>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-lightbulb text-warning me-2"></i>ข้อดีของการทำปุ่มเอง</h6>
                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.7;">
                    <li>วางไว้ตรง Navbar เมนูด้านบนของเว็บได้</li>
                    <li>วางเป็นการ์ดหรือปุ่ม Call-to-Action ในหน้าสมัครเรียน</li>
                    <li>ไม่บังเนื้อหาหน้าจอบนมือถือ</li>
                    <li>สามารถใช้ร่วมกับปุ่มลอยเดิม หรือจะเลือกซ่อนปุ่มลอยแล้วใช้เฉพาะปุ่มของเราเองก็ได้</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function copySnippet(elementId) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const text = el.innerText;
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกโค้ดสำเร็จ!',
                text: 'นำไปวางในตำแหน่งที่ต้องการได้ทันที',
                timer: 1500,
                showConfirmButton: false
            });
        });
    }
</script>
<?= $this->endSection() ?>
