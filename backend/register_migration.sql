-- تسجيل Migration للترجمة في جدول migrations
-- قم بتشغيل هذا في phpMyAdmin أو MySQL client

INSERT INTO `migrations` (`migration`, `batch`) 
VALUES ('2025_11_20_000000_add_english_translation_columns_to_articles_table', 
        (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS temp));

-- بعد تشغيل هذا الأمر، شغّل:
-- php artisan migrate:status
-- يجب أن ترى Migration status: Ran
