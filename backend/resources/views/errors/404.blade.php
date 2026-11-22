@php
    $frontendUrl = rtrim(env('FRONTEND_URL', url('/')), '/');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>خطأ 404 - الصفحة غير موجودة</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap">
    <style>
        :root {
            color-scheme: light;
            --primary: #213761;
            --primary-50: #F0F4F8;
            --primary-600: #172a4a;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
            --font-body: "IBM Plex Arabic", "IBM Plex Sans Arabic", Tahoma, Arial, sans-serif;
            --font-display: "Amiri", "Traditional Arabic", serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            font-size: 1.0625rem;
            line-height: 1.95;
            -webkit-font-smoothing: antialiased;
            direction: rtl;
            text-align: right;
            color: #243449;
            background-color: #ffffff;
        }

        .error-page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to bottom right, var(--gray-50), var(--gray-100));
            padding: 4rem 1rem;
        }

        .error-container {
            max-width: 42rem;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        .icon-section {
            margin-bottom: 2rem;
        }

        .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 6rem;
            height: 6rem;
            border-radius: 9999px;
            margin-left: auto;
            margin-right: auto;
            background-color: var(--primary-50);
        }

        .icon-svg {
            width: 3rem;
            height: 3rem;
            color: var(--primary);
        }

        .code-section {
            margin-bottom: 1rem;
        }

        .error-code {
            font-family: var(--font-display);
            font-size: 3.75rem;
            line-height: 1;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.25rem;
            line-height: 1.75rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .error-message {
            color: var(--gray-600);
            margin-bottom: 2rem;
            font-size: 1rem;
            line-height: 1.625;
        }

        .actions-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-family: var(--font-body);
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--primary-600);
            transform: scale(1.05);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
        }

        .btn-secondary:hover {
            background-color: var(--gray-50);
        }

        .btn-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .help-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
        }

        .help-label {
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: var(--gray-500);
            margin-bottom: 1rem;
        }

        .help-links {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            align-items: center;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .help-link {
            position: relative;
            overflow: hidden;
            color: var(--primary);
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .help-link:hover {
            color: var(--primary-600);
        }

        .help-dot {
            color: var(--gray-400);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 640px) {
            .actions-wrapper {
                flex-direction: row;
            }
        }

        @media (min-width: 768px) {
            .error-code {
                font-size: 4.5rem;
            }
            .error-title {
                font-size: 1.5rem;
                line-height: 2rem;
            }
            .error-message {
                font-size: 1.125rem;
                line-height: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-page-wrapper" dir="rtl">
        <div class="error-container">
            <!-- Error Icon -->
            <div class="icon-section">
                <div class="icon-circle">
                    <!-- 404 Icon -->
                    <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div class="code-section">
                <h1 class="error-code">404</h1>
                <p class="error-title">الصفحة غير موجودة</p>
            </div>

            <!-- Error Message -->
            <p class="error-message">
                عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها إلى عنوان آخر. يمكنك العودة للصفحة الرئيسية أو البحث عن محتوى آخر.
            </p>

            <!-- Action Buttons -->
            <div class="actions-wrapper">
                <a href="{{ $frontendUrl }}/" class="btn btn-primary">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    الصفحة الرئيسية
                </a>

                <button type="button" onclick="goBack()" class="btn btn-secondary">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة للخلف
                </button>
            </div>

            <!-- Additional Help Links -->
            <div class="help-section">
                <p class="help-label">هل تحتاج مساعدة؟</p>
                <div class="help-links">
                    <a href="{{ $frontendUrl }}/about" class="help-link">من نحن</a>
                    <span class="help-dot">•</span>
                    <a href="{{ $frontendUrl }}/contact" class="help-link">اتصل بنا</a>
                    <span class="help-dot">•</span>
                    <button type="button" onclick="reportError()" class="help-link">الإبلاغ عن مشكلة</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var frontendUrl = @json($frontendUrl ?: '/');
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = frontendUrl + '/';
            }
        }
        function reportError() {
            alert('شكراً لإبلاغنا! سنقوم بمراجعة المشكلة في أقرب وقت.');
            window.location.href = frontendUrl + '/contact';
        }
    </script>
</body>
</html>
