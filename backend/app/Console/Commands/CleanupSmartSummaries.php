<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;

class CleanupSmartSummaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summaries:cleanup {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تنظيف الملخصات القديمة وغير المستخدمة';

    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 بدء تنظيف الملخصات القديمة...');

        // إظهار إحصائيات قبل التنظيف
        $statsBefore = $this->cacheService->getStatistics();
        $this->table(['المؤشر', 'القيمة'], [
            ['إجمالي الملخصات', $statsBefore['total_summaries']],
            ['ملخصات اليوم', $statsBefore['today_summaries']],
            ['إجمالي الاستخدام', $statsBefore['total_usage']],
        ]);

        if (!$this->option('force')) {
            if (!$this->confirm('هل تريد المتابعة مع التنظيف؟')) {
                $this->info('تم إلغاء العملية.');
                return Command::SUCCESS;
            }
        }

        // تشغيل التنظيف
        $result = $this->cacheService->cleanup();

        // إظهار النتائج
        $this->newLine();
        $this->info('✅ تم التنظيف بنجاح!');
        $this->table(['نوع الحذف', 'العدد'], [
            ['الملخصات القديمة', $result['deleted_old']],
            ['الملخصات الزائدة', $result['deleted_excess']],
            ['المتبقية', $result['remaining']],
        ]);

        // إظهار إحصائيات بعد التنظيف
        $statsAfter = $this->cacheService->getStatistics();
        $this->newLine();
        $this->info('📊 الإحصائيات بعد التنظيف:');
        $this->table(['المؤشر', 'القيمة'], [
            ['إجمالي الملخصات', $statsAfter['total_summaries']],
            ['كفاءة الـ Cache', $statsAfter['cache_efficiency']],
            ['النوع الأكثر شعبية', $statsAfter['most_popular_type'] ?? 'لا يوجد'],
        ]);

        return Command::SUCCESS;
    }
}
