#!/bin/bash
# اكتب هذه الأوامر على السيرفر لإيجاد المشروع

# 1. البحث عن المشروع
echo "=== البحث عن مجلد newsroom ==="
sudo find / -name "newsroom" -type d 2>/dev/null

# 2. التحقق من المسارات الشائعة
echo -e "\n=== التحقق من المسارات الشائعة ==="
ls -la /var/www/ 2>/dev/null
ls -la /home/ubuntu/ 2>/dev/null
ls -la ~ 2>/dev/null

# 3. عرض المجلد الحالي
echo -e "\n=== المجلد الحالي ==="
pwd
ls -la
