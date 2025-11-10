#!/bin/bash
# تثبيت PHP Extensions المطلوبة

# تحديث قائمة الحزم
sudo apt update

# تثبيت php-curl و php-gd و php-mbstring (مطلوبة لـ web-push)
sudo apt install -y php8.2-curl php8.2-gd php8.2-mbstring php8.2-gmp

# إعادة تشغيل PHP-FPM
sudo systemctl restart php8.2-fpm

# التحقق من التثبيت
php -m | grep curl
php -m | grep gd
php -m | grep gmp

echo "✅ تم تثبيت Extensions بنجاح!"
