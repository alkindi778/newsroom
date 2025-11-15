<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Services\SocialMediaService;

// الحصول على أول مقالة منشورة
$article = Article::where('is_published', true)
    ->whereNotNull('published_at')
    ->first();

if (!$article) {
    echo "❌ لا توجد مقالات منشورة في قاعدة البيانات!\n";
    exit(1);
}

echo "📰 المقالة المختارة:\n";
echo "العنوان: " . $article->title . "\n";
echo "ID: " . $article->id . "\n";
echo "الفئة: " . ($article->category->name ?? 'بدون فئة') . "\n\n";

// إنشاء خدمة النشر
$socialMediaService = app(SocialMediaService::class);

// نشر على Telegram
echo "🚀 جاري النشر على Telegram...\n";
try {
    $results = $socialMediaService->publishArticle($article);
    
    if (isset($results['telegram'])) {
        if ($results['telegram']['success'] ?? false) {
            echo "✅ تم النشر بنجاح على Telegram!\n";
            echo "تحقق من القناة: @stcaden2025\n";
        } else {
            echo "❌ فشل النشر على Telegram\n";
            echo "الخطأ: " . ($results['telegram']['error'] ?? 'خطأ غير معروف') . "\n";
        }
    } else {
        echo "⚠️ Telegram غير مفعّل أو لم يتم النشر عليه\n";
    }
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}
