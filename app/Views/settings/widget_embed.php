<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-code text-primary me-2"></i>โค้ดสำหรับติดตั้ง Live Chat Widget</h4>
        <p class="text-muted small mb-0">คัดลอกโค้ดสคริปต์ด้านล่างนี้ไปวางก่อนปิดแท็ก <code>&lt;/body&gt;</code> ในเว็บไซต์โรงเรียน หรือระบบย่อยอื่นๆ เพื่อเปิดใช้งานปุ่มแชท</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">โค้ด Embed แบบ JavaScript (แนะนำ)</h6>
                    <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="copyEmbedCode()">
                        <i class="fa-regular fa-copy me-1"></i> คัดลอกโค้ด
                    </button>
                </div>

                <div class="bg-dark text-light p-3 rounded-3 font-monospace small position-relative" style="overflow-x: auto;" id="embedCodeSnippet">
&lt;!-- SKJ Live Chat Widget --&gt;
&lt;script src="<?= rtrim(base_url(), '/') ?>/public/assets/js/skj-chat-widget.js?v=1.7" 
        data-chat-server="<?= rtrim(base_url(), '/') ?>"
        data-school-name="โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์"
        data-primary-color="#e91e63"
        async&gt;&lt;/script&gt;
                </div>
            </div>

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
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 text-center">
                <h6 class="fw-bold mb-3">สถานะเซิร์ฟเวอร์ระบบแชท</h6>
                <div class="d-inline-flex align-items-center gap-2 p-2 px-3 bg-success-subtle text-success rounded-pill font-monospace small mb-3">
                    <span class="status-dot" style="background:#22c55e;"></span> API Server Active
                </div>
                <div class="small text-muted mb-2">Endpoint URL:</div>
                <code class="d-block p-2 bg-light rounded text-truncate mb-3"><?= base_url('api/widget') ?></code>
            </div>
        </div>
    </div>
</div>

<script>
    function copyEmbedCode() {
        const text = document.getElementById('embedCodeSnippet').innerText;
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกโค้ดสำเร็จ!',
                text: 'นำไปวางใน footer ของเว็บไซต์ที่ต้องการได้ทันที',
                timer: 1500,
                showConfirmButton: false
            });
        });
    }
</script>
<?= $this->endSection() ?>
