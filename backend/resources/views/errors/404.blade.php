<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - الصفحة غير موجودة</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            text-align: center;
            color: white;
            max-width: 600px;
        }
        .error-code {
            font-size: 120px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        h1 {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 25px rgba(0,0,0,0.3);
        }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔍</div>
        <div class="error-code">404</div>
        <h1>الصفحة غير موجودة</h1>
        <p>عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها إلى موقع آخر.</p>
        <a href="/" class="btn">العودة للرئيسية</a>
    </div>
</body>
</html>
