<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 إصلاح مسارات صور الكُتاب...\n\n";

// الحصول على جميع الكُتاب الذين لهم صور
$writers = DB::table('writers')
    ->whereNotNull('image')
    ->where('image', '!=', '')
    ->get();

echo "عدد الكُتاب الذين لهم صور: " . $writers->count() . "\n\n";

$updated = 0;

foreach ($writers as $writer) {
    $oldImage = $writer->image;
    
    // تخطي إذا كان المسار صحيحاً بالفعل
    if (str_starts_with($oldImage, 'storage/')) {
        continue;
    }
    
    // إضافة storage/media/ في البداية
    $newImage = 'storage/media/' . $oldImage;
    
    // تحديث
    DB::table('writers')
        ->where('id', $writer->id)
        ->update(['image' => $newImage]);
    
    $updated++;
    
    if ($updated <= 5) {
        echo "✓ ID {$writer->id} ({$writer->name}):\n";
        echo "  القديم: {$oldImage}\n";
        echo "  الجديد: {$newImage}\n\n";
    }
}

echo "\n✅ تم تحديث {$updated} كاتب!\n";
