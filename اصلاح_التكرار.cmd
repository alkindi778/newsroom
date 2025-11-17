@echo off
chcp 65001 > nul
echo ====================================
echo    إصلاح تكرار الصور
echo ====================================
echo.

cd /d "%~dp0backend"

echo [1/3] حذف جميع سجلات الصور القديمة...
echo.
php artisan tinker --execute="DB::table('media')->where('collection_name', 'old_photos')->delete(); echo 'تم حذف جميع السجلات القديمة\n';"

echo.
echo.
echo [2/3] إعادة استيراد الصور من media/old_photos...
echo.
php artisan media:import-old-photos-from-media

echo.
echo ====================================
echo    ✅ اكتمل الإصلاح!
echo ====================================
echo.
echo الآن يمكنك التحقق من الصور في لوحة التحكم
echo http://localhost/newsroom/backend/public/admin/media
echo.
pause
