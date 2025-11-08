<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Services\ImageOptimizerService;

class OptimizeExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize 
                            {--collection= : ضغط الصور من مجموعة معينة فقط}
                            {--force : إعادة ضغط جميع الصور حتى التي تم ضغطها مسبقاً}
                            {--limit= : الحد الأقصى لعدد الصور للمعالجة}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ضغط جميع الصور الموجودة في النظام لتصبح أقل من 200 كيلوبايت';

    protected ImageOptimizerService $optimizer;

    public function __construct(ImageOptimizerService $optimizer)
    {
        parent::__construct();
        $this->optimizer = $optimizer;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  بدء عملية ضغط الصور...');
        $this->newLine();

        // بناء الاستعلام
        $query = Media::query();

        // تصفية حسب المجموعة إذا تم تحديدها
        if ($collection = $this->option('collection')) {
            $query->where('collection_name', $collection);
            $this->info("📁 سيتم ضغط الصور من مجموعة: {$collection}");
        }

        // تطبيق الحد الأقصى
        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
            $this->info("⚠️  سيتم معالجة {$limit} صورة فقط");
        }

        // الحصول على جميع الصور
        $images = $query->get();
        
        if ($images->isEmpty()) {
            $this->warn('⚠️  لم يتم العثور على صور للمعالجة!');
            return Command::SUCCESS;
        }

        $totalImages = $images->count();
        $this->info("📊 عدد الصور المراد معالجتها: {$totalImages}");
        $this->newLine();

        // إحصائيات
        $stats = [
            'processed' => 0,
            'optimized' => 0,
            'skipped' => 0,
            'failed' => 0,
            'total_saved' => 0
        ];

        // Progress bar
        $progressBar = $this->output->createProgressBar($totalImages);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
        $progressBar->setMessage('جاري المعالجة...');
        $progressBar->start();

        foreach ($images as $media) {
            $stats['processed']++;
            
            // التحقق من أن الملف موجود
            if (!file_exists($media->getPath())) {
                $stats['skipped']++;
                $progressBar->setMessage("⚠️  الملف غير موجود: {$media->file_name}");
                $progressBar->advance();
                continue;
            }

            $originalSize = filesize($media->getPath());

            // تخطي إذا كانت الصورة أصغر من 200 كيلوبايت ولم يتم استخدام --force
            if ($originalSize <= 200 * 1024 && !$this->option('force')) {
                $stats['skipped']++;
                $progressBar->setMessage("⏭️  تم تخطي: {$media->file_name} (حجم مناسب)");
                $progressBar->advance();
                continue;
            }

            // ضغط الصورة
            $progressBar->setMessage("🔄 معالجة: {$media->file_name}");
            
            if ($this->optimizer->optimizeImage($media->getPath())) {
                $newSize = filesize($media->getPath());
                $saved = $originalSize - $newSize;
                $stats['total_saved'] += $saved;
                $stats['optimized']++;
                
                // تحديث حجم الملف في قاعدة البيانات
                $media->size = $newSize;
                $media->save();
                
                $progressBar->setMessage("✅ تم: {$media->file_name} (وفّرنا " . $this->formatBytes($saved) . ")");
            } else {
                $stats['failed']++;
                $progressBar->setMessage("❌ فشل: {$media->file_name}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // عرض الإحصائيات النهائية
        $this->displayStats($stats);

        return Command::SUCCESS;
    }

    /**
     * عرض الإحصائيات
     */
    private function displayStats(array $stats): void
    {
        $this->info('📊 ملخص العملية:');
        $this->table(
            ['البيان', 'القيمة'],
            [
                ['إجمالي الصور المعالجة', $stats['processed']],
                ['تم ضغطها بنجاح', "✅ {$stats['optimized']}"],
                ['تم تخطيها', "⏭️  {$stats['skipped']}"],
                ['فشلت', "❌ {$stats['failed']}"],
                ['إجمالي المساحة الموفرة', "💾 " . $this->formatBytes($stats['total_saved'])],
            ]
        );

        if ($stats['optimized'] > 0) {
            $avgSaved = $stats['total_saved'] / $stats['optimized'];
            $this->info("💡 متوسط التوفير لكل صورة: " . $this->formatBytes($avgSaved));
        }

        $this->newLine();
        $this->info('✨ اكتملت عملية الضغط!');
    }

    /**
     * تنسيق حجم الملف
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
