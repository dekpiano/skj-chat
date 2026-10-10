<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'An Error Was Encountered') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@400;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'K2D', sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 24px; line-height: 1.6; }
        .container { max-width: 1000px; margin: 0 auto; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%); color: #ffffff; padding: 20px 24px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; }
        .content { padding: 24px; }
        .error-box { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 10px; padding: 16px; margin-bottom: 20px; color: #9f1239; font-weight: 600; }
        .trace-box { background: #0f172a; color: #f8fafc; border-radius: 10px; padding: 16px; font-family: 'JetBrains Mono', monospace; font-size: 13px; overflow-x: auto; white-space: pre-wrap; line-height: 1.5; }
        .back-btn { display: inline-block; margin-top: 16px; padding: 10px 20px; background: #e91e63; color: #ffffff; text-decoration: none; border-radius: 20px; font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ เกิดข้อผิดพลาดของระบบ (System Exception)</h1>
        </div>
        <div class="content">
            <div class="error-box">
                <?= esc($message ?? 'Unknown Exception occurred.') ?>
            </div>
            <?php if (ENVIRONMENT !== 'production'): ?>
                <h3 style="font-size:15px; font-weight:700; margin-bottom:8px;">Stack Trace:</h3>
                <div class="trace-box"><?= esc($trace ?? (isset($exception) ? $exception->getTraceAsString() : 'No trace available')) ?></div>
            <?php endif; ?>
            <a href="javascript:history.back()" class="back-btn">⬅ ย้อนกลับ</a>
        </div>
    </div>
</body>
</html>
