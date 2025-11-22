-- حذف جميع البيانات المستوردة من القاعدة القديمة
-- استخدم هذا في phpMyAdmin أو MySQL

USE newsrooom;

SET FOREIGN_KEY_CHECKS = 0;

-- حذف المقالات
TRUNCATE TABLE articles;

-- حذف مقالات الرأي  
TRUNCATE TABLE opinions;

-- حذف الكتاب
TRUNCATE TABLE writers;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'تم حذف جميع البيانات بنجاح!' as message;
