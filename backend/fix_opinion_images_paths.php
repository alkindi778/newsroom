<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 إصلاح مسارات صور مقالات الرأي...\n\n";

// الحصول على جميع مقالات الرأي التي لها صور
$opinions = DB::table('opinions')
    ->whereNotNull('image')
    ->where('image', '!=', '')
    ->get();

echo "عدد مقالات الرأي التي لها صور: " . $opinions->count() . "\n\n";

$updated = 0;

foreach ($opinions as $opinion) {
    $oldImage = $opinion->image;
    
    // إزالة أي مجلدات تاريخ (6 أرقام)
    $newImage = preg_replace('#^old_photos/\d{6}/#', 'old_photos/', $oldImage);
    
    // إضافة storage/media/ في البداية إذا لم تكن موجودة
    if (!str_starts_with($newImage, 'storage/')) {
        $newImage = 'storage/media/' . $newImage;
    }
    
    // تحديث فقط إذا تغير المسار
    if ($newImage !== $oldImage) {
        DB::table('opinions')
            ->where('id', $opinion->id)
            ->update(['image' => $newImage]);
        
        $updated++;
        
        if ($updated <= 5) {
            echo "✓ ID {$opinion->id}:\n";
            echo "  القديم: {$oldImage}\n";
            echo "  الجديد: {$newImage}\n\n";
        }
    }
}

echo "\n✅ تم تحديث {$updated} مقالة رأي!\n";
