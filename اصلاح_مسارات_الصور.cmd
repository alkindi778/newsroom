@echo off
chcp 65001 > nul
echo ====================================
echo    إصلاح مسارات الصور القديمة
echo ====================================
echo.

cd /d "%~dp0backend"

echo [1/2] نقل الصور من old_photos إلى media/old_photos...
echo.
php artisan media:move-old-photos

echo.
echo.
echo [2/2] تحديث المسارات في قاعدة البيانات...
echo.
php artisan media:update-old-photos-path

echo.
echo ====================================
echo    ✅ اكتمل الإصلاح!
echo ====================================
echo.
echo الآن يمكنك التحقق من الصور في لوحة التحكم:
echo http://localhost/newsroom/backend/public/admin/media
echo.
pause
