<?php

/**
 * اختبار سريع لنظام الترجمة
 * 
 * الاستخدام:
 * php test_translation.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GeminiTranslationService;
use Illuminate\Support\Facades\Log;

echo "🧪 اختبار نظام الترجمة التلقائي\n";
echo "================================\n\n";

// 1. اختبار التكوين
echo "1️⃣ فحص التكوين...\n";
$apiKey = config('services.gemini.api_key');
$model = config('services.gemini.model');
$baseUrl = config('services.gemini.base_url');

if (empty($apiKey)) {
    echo "❌ خطأ: GEMINI_API_KEY غير موجود في .env\n";
    echo "   احصل على API Key من: https://makersuite.google.com/app/apikey\n";
    exit(1);
}

echo "✅ API Key: " . substr($apiKey, 0, 10) . "...\n";
echo "✅ Model: {$model}\n";
echo "✅ Base URL: {$baseUrl}\n\n";

// 2. اختبار الاتصال بـ API
echo "2️⃣ اختبار الاتصال بـ Gemini API...\n";
$service = new GeminiTranslationService();

try {
    $connected = $service->testConnection();
    
    if ($connected) {
        echo "✅ الاتصال ناجح!\n\n";
    } else {
        echo "❌ فشل الاتصال بـ Gemini API\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. اختبار الترجمة
echo "3️⃣ اختبار الترجمة...\n";
$testTitle = "اختبار نظام الترجمة";
$testContent = "<p>هذا نص تجريبي لاختبار نظام الترجمة التلقائي</p>";

echo "   العنوان العربي: {$testTitle}\n";
echo "   المحتوى العربي: {$testContent}\n\n";

try {
    echo "   جاري إرسال الطلب إلى Gemini...\n";
    $translation = $service->translateContent($testTitle, $testContent);
    
    if ($translation && isset($translation['title_en']) && isset($translation['content_en'])) {
        echo "✅ الترجمة ناجحة!\n\n";
        echo "   Title (EN): {$translation['title_en']}\n";
        echo "   Content (EN): {$translation['content_en']}\n\n";
        
        // التحقق من الحفاظ على HTML
        if (strpos($translation['content_en'], '<p>') !== false && 
            strpos($translation['content_en'], '</p>') !== false) {
            echo "✅ تم الحفاظ على أكواد HTML\n\n";
        } else {
            echo "⚠️  تحذير: قد تكون أكواد HTML تغيرت\n\n";
        }
    } else {
        echo "❌ فشلت الترجمة\n\n";
        echo "📋 تفاصيل النتيجة:\n";
        var_dump($translation);
        echo "\n";
        
        // فحص آخر الأخطاء في الـ logs
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            echo "📄 آخر سطور من Log file:\n";
            $lines = file($logFile);
            $lastLines = array_slice($lines, -20);
            foreach ($lastLines as $line) {
                if (stripos($line, 'error') !== false || stripos($line, 'translation') !== false) {
                    echo "   " . trim($line) . "\n";
                }
            }
        }
        
        exit(1);
    }
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

// 4. اختبار Queue
echo "4️⃣ فحص إعدادات Queue...\n";
$queueConnection = config('queue.default');
echo "✅ Queue Connection: {$queueConnection}\n";

if ($queueConnection === 'sync') {
    echo "⚠️  تحذير: Queue معين على 'sync' - الترجمة ستتم بشكل متزامن\n";
    echo "   للأداء الأفضل، غيّر QUEUE_CONNECTION في .env إلى 'database'\n\n";
} else {
    echo "✅ Queue معين بشكل صحيح\n";
    echo "   تذكر تشغيل: php artisan queue:work\n\n";
}

// 5. فحص جدول Articles
echo "5️⃣ فحص جدول Articles...\n";
try {
    $articlesTable = \DB::table('articles')->limit(1)->count();
    echo "✅ جدول Articles موجود\n";
    
    // التحقق من وجود الأعمدة الجديدة
    $columns = \DB::getSchemaBuilder()->getColumnListing('articles');
    $hasTitleEn = in_array('title_en', $columns);
    $hasContentEn = in_array('content_en', $columns);
    
    if ($hasTitleEn && $hasContentEn) {
        echo "✅ أعمدة الترجمة (title_en, content_en) موجودة\n\n";
    } else {
        echo "❌ أعمدة الترجمة غير موجودة\n";
        echo "   شغّل: php artisan migrate\n\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "❌ خطأ في فحص قاعدة البيانات: " . $e->getMessage() . "\n";
    exit(1);
}

// النتيجة النهائية
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎉 جميع الاختبارات نجحت!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📋 الخطوات التالية:\n";
echo "1. تأكد من تشغيل Queue Worker: php artisan queue:work\n";
echo "2. أنشئ مقالاً جديداً\n";
echo "3. راقب الترجمة في الخلفية\n";
echo "4. تحقق من الحقول: title_en و content_en\n\n";

echo "💡 نصائح:\n";
echo "- مراقبة Logs: tail -f storage/logs/laravel.log\n";
echo "- فحص Jobs الفاشلة: php artisan queue:failed\n";
echo "- ترجمة المقالات الموجودة: php artisan articles:translate\n\n";

exit(0);
