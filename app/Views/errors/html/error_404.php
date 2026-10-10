<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'K2D', sans-serif; background: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
        .card { max-width: 480px; width: 100%; background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 40px 30px; text-align: center; border: 1px solid #e2e8f0; }
        .code { font-size: 72px; font-weight: 800; color: #e91e63; line-height: 1; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: 700; margin-bottom: 12px; color: #334155; }
        .desc { font-size: 14px; color: #64748b; margin-bottom: 24px; }
        .btn { display: inline-block; padding: 10px 24px; background: linear-gradient(135deg, #e91e63 0%, #1976d2 100%); color: #ffffff; text-decoration: none; border-radius: 24px; font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">404</div>
        <div class="title">ไม่พบหน้าที่คุณต้องการ</div>
        <div class="desc"><?= esc($message ?? 'ขออภัย หน้าเว็บที่คุณกำลังเรียกหาไม่มีอยู่หรือถูกย้ายไปแล้ว') ?></div>
        <a href="<?= base_url() ?>" class="btn">กลับหน้าหลัก</a>
    </div>
</body>
</html>
