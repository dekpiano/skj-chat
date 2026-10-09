<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบเจ้าหน้าที่ | SKJ Live Chat Portal</title>
    <link rel="icon" type="image/png" href="https://skj.ac.th/assets/img/logo/logo-skj.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Google Identity Services -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <style>
        body {
            font-family: 'K2D', sans-serif;
            background: linear-gradient(135deg, #fff0f5 0%, #e6f0fa 50%, #f4f6fa 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
        }

        .login-header {
            padding: 2.5rem 2rem 1.5rem;
            text-align: center;
            position: relative;
        }

        .login-logo {
            width: 76px;
            height: 76px;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fce4ec 0%, #e3f2fd 100%);
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(233, 30, 99, 0.12);
        }

        .login-logo img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .login-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .login-subtitle {
            font-size: 0.85rem;
            color: #64748b;
        }

        .login-body {
            padding: 1.5rem 2rem 2.5rem;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #94a3b8;
            font-size: 0.8rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        .divider::before { margin-right: .75em; }
        .divider::after { margin-left: .75em; }

        .domain-tag {
            background-color: #f1f5f9;
            color: #475569;
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 0.78rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <img src="<?= base_url('public/assets/images/logo-skj.png') ?>" alt="SKJ Logo" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=SKJ&background=fce4ec&color=c2185b';">
            </div>
            <div class="login-title">SKJ Live Chat Desk</div>
            <div class="login-subtitle">ระบบจัดการแชทและคลังความรู้ AI ประจำโรงเรียน</div>
        </div>

        <div class="login-body">
            <!-- Google Sign-In Container -->
            <div class="text-center mb-3">
                <p class="text-muted small mb-3">เข้าสู่ระบบด้วยบัญชี Google ของสถานศึกษา</p>

                <?php if (!empty($googleClientId)): ?>
                    <!-- Google GIS Button -->
                    <div id="g_id_onload"
                         data-client_id="<?= esc($googleClientId) ?>"
                         data-context="signin"
                         data-ux_mode="popup"
                         data-callback="handleGoogleCredentialResponse"
                         data-auto_prompt="true">
                    </div>

                    <div class="g_id_signin d-flex justify-content-center"
                         data-type="standard"
                         data-shape="pill"
                         data-theme="outline"
                         data-text="signin_with"
                         data-size="large"
                         data-logo_alignment="left"
                         data-width="320">
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning small text-start mb-3">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        <strong>ยังไม่ได้ระบุ Google Client ID:</strong><br>
                        ผู้ดูแลระบบสามารถระบุ Client ID ในเมนู <em>ตั้งค่า Google OAuth</em>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Allowed domain notice -->
            <div class="d-flex align-items-center justify-content-center gap-1 mt-3">
                <span class="text-secondary small">โดเมนที่อนุญาต:</span>
                <span class="domain-tag">@<?= esc($allowedDomains ?: 'skj.ac.th') ?></span>
            </div>

            <div class="mt-4 text-center">
                <a href="https://skj.ac.th" class="text-decoration-none small text-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าเว็บไซต์หลัก skj.ac.th
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
        function handleGoogleCredentialResponse(response) {
            if (!response.credential) {
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบโทเค็นจาก Google', 'error');
                return;
            }

            Swal.fire({
                title: 'กำลังตรวจสอบสิทธิ์...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const formData = new FormData();
            formData.append('credential', response.credential);

            fetch('<?= base_url('auth/verify-google') ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ',
                        text: 'ยินดีต้อนรับเข้าสู่ระบบจัดการ Live Chat',
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect || '<?= base_url('chat/desk') ?>';
                    });
                } else {
                    Swal.fire('เข้าสู่ระบบไม่สำเร็จ', data.message || 'เกิดข้อผิดพลาด', 'error');
                }
            })
            .catch(err => {
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
            });
        }
    </script>
</body>
</html>
