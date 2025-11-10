<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageOptimizationService;

class ConvertImagesToWebP extends Command
{
    /**
     * اسم ووصف الأمر
     *
     * @var string
     */
    protected $signature = 'images:convert-webp 
                            {directory? : المجلد المراد تحويل صوره (افتراضي: storage/app/public)}
                            {--quality=85 : جودة الصورة (1-100)}
                            {--delete-originals : حذف الصور الأصلية بعد التحويل}';

    protected $description = 'تحويل جميع الصور إلى صيغة WebP للحصول على أداء أفضل';

    protected $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * تنفيذ الأمر
     */
    public function handle()
    {
        $directory = $this->argument('directory') ?? storage_path('app/public');
        $quality = (int) $this->option('quality');
        $deleteOriginals = $this->option('delete-originals');

        // التحقق من صحة جودة الصورة
        if ($quality < 1 || $quality > 100) {
            $this->error('❌ جودة الصورة يجب أن تكون بين 1 و 100');
            return 1;
        }

        // التحقق من وجود المجلد
        if (!is_dir($directory)) {
            $this->error("❌ المجلد غير موجود: {$directory}");
            return 1;
        }

        $this->info("🔄 جاري مسح المجلد: {$directory}");
        $this->newLine();

        // الحصول على جميع الصور
        $images = $this->findImages($directory);
        $totalImages = count($images);

        if ($totalImages === 0) {
            $this->warn('⚠️  لم يتم العثور على صور للتحويل');
            return 0;
        }

        $this->info("📊 تم العثور على {$totalImages} صورة");
        $this->newLine();

        // تأكيد من المستخدم
        if (!$this->confirm('هل تريد المتابعة؟', true)) {
            $this->info('تم الإلغاء');
            return 0;
        }

        $this->newLine();

        // شريط التقدم
        $progressBar = $this->output->createProgressBar($totalImages);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');

        $stats = [
            'converted' => 0,
            'skipped' => 0,
            'failed' => 0,
            'deleted' => 0
        ];

        foreach ($images as $imagePath) {
            $filename = basename($imagePath);
            $progressBar->setMessage("معالجة: {$filename}");

            $webpPath = $this->imageService->convertToWebP($imagePath, $quality);

            if ($webpPath) {
                if ($webpPath === $imagePath) {
                    $stats['skipped']++;
                } else {
                    $stats['converted']++;
                    
                    // حذف الصورة الأصلية إذا طلب المستخدم ذلك
                    if ($deleteOriginals) {
                        if (unlink($imagePath)) {
                            $stats['deleted']++;
                        }
                    }
                }
            } else {
                $stats['failed']++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // عرض الإحصائيات
        $this->displayStats($stats, $totalImages);

        return 0;
    }

    /**
     * البحث عن جميع الصور في المجلد
     */
    protected function findImages(string $directory): array
    {
        $images = [];
        $supportedFormats = ['jpg', 'jpeg', 'png', 'gif'];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $extension = strtolower($file->getExtension());
            
            if (in_array($extension, $supportedFormats)) {
                $images[] = $file->getPathname();
            }
        }

        return $images;
    }

    /**
     * عرض الإحصائيات
     */
    protected function displayStats(array $stats, int $total): void
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📈 نتائج التحويل:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $this->table(
            ['العنصر', 'العدد'],
            [
                ['إجمالي الصور', $total],
                ['✅ تم تحويلها', $stats['converted']],
                ['⏭️  تم تخطيها (موجودة)', $stats['skipped']],
                ['❌ فشل التحويل', $stats['failed']],
                ['🗑️  تم حذفها', $stats['deleted']],
            ]
        );

        $this->newLine();

        if ($stats['converted'] > 0) {
            $this->info("✨ رائع! تم تحويل {$stats['converted']} صورة بنجاح إلى WebP");
        }

        if ($stats['failed'] > 0) {
            $this->warn("⚠️  فشل تحويل {$stats['failed']} صورة. تحقق من ملف السجلات للمزيد من التفاصيل");
        }
    }
}
