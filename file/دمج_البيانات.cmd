@echo off
chcp 65001 >nul
color 0A
title دمج قاعدة البيانات القديمة

echo ========================================
echo    دمج البيانات من القاعدة القديمة
echo ========================================
echo.

cd /d "%~dp0backend"

echo ✅ التحقق من الاتصال...
php artisan tinker --execute="echo 'التوصيل يعمل ✅'; exit;"
if errorlevel 1 (
    echo ❌ فشل الاتصال! تأكد من تشغيل XAMPP
    pause
    exit /b 1
)

echo.
echo ========================================
echo اختر نوع الدمج:
echo ========================================
echo.
echo 1. دمج كل شيء (الكتاب + المقالات + الأخبار)
echo 2. دمج الكتاب فقط
echo 3. دمج مقالات الرأي فقط
echo 4. دمج الأخبار فقط
echo 5. إلغاء
echo.

set /p choice=اختيارك (1-5): 

if "%choice%"=="1" (
    echo.
    echo 🔄 بدء دمج جميع البيانات...
    php artisan migrate:old-database
) else if "%choice%"=="2" (
    echo.
    echo 🔄 بدء دمج الكتاب...
    php artisan migrate:old-database --step=writers
) else if "%choice%"=="3" (
    echo.
    echo 🔄 بدء دمج مقالات الرأي...
    php artisan migrate:old-database --step=opinions
) else if "%choice%"=="4" (
    echo.
    echo 🔄 بدء دمج الأخبار...
    php artisan migrate:old-database --step=articles
) else if "%choice%"=="5" (
    echo.
    echo ⏭️ تم الإلغاء
    pause
    exit /b 0
) else (
    echo.
    echo ❌ اختيار غير صحيح
    pause
    exit /b 1
)

echo.
echo ========================================
echo ✅ اكتملت العملية!
echo ========================================
echo.

echo 📋 للتحقق من النتائج:
echo    cd backend
echo    php artisan tinker
echo    App\Models\Writer::count()
echo    App\Models\Opinion::count()
echo    App\Models\Article::count()
echo.

pause
