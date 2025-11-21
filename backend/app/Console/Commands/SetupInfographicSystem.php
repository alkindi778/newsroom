<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupInfographicSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infographic:setup {--seed : Also seed sample data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the Infographic System (migrate, permissions, and optionally seed sample data)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 بدء إعداد نظام الإنفوجرافيك...');
        $this->newLine();

        // 1. Run Migration
        $this->info('📊 1/3 تشغيل Migration...');
        $this->call('migrate', [
            '--path' => 'database/migrations',
            '--force' => true,
        ]);
        $this->info('✅ تم تشغيل Migration بنجاح');
        $this->newLine();

        // 2. Setup Permissions
        $this->info('🔐 2/3 إضافة الصلاحيات...');
        $this->call('db:seed', [
            '--class' => 'InfographicPermissionsSeeder',
            '--force' => true,
        ]);
        $this->info('✅ تم إضافة الصلاحيات بنجاح');
        $this->newLine();

        // 3. Seed Sample Data (optional)
        if ($this->option('seed')) {
            $this->info('🎨 3/3 إضافة بيانات تجريبية...');
            $this->call('db:seed', [
                '--class' => 'InfographicSeeder',
                '--force' => true,
            ]);
            $this->warn('⚠️  تذكير: ستحتاج إلى إضافة صور تجريبية في storage/app/public/infographics/');
            $this->info('✅ تم إضافة البيانات التجريبية بنجاح');
            $this->newLine();
        } else {
            $this->info('⏭️  3/3 تم تخطي البيانات التجريبية (استخدم --seed لإضافتها)');
            $this->newLine();
        }

        // 4. Check Storage Link
        $this->info('🔗 التحقق من Storage Link...');
        if (!file_exists(public_path('storage'))) {
            $this->call('storage:link');
            $this->info('✅ تم إنشاء Storage Link');
        } else {
            $this->info('✅ Storage Link موجود مسبقاً');
        }
        $this->newLine();

        // Success Message
        $this->info('═══════════════════════════════════════════');
        $this->info('🎉 تم إعداد نظام الإنفوجرافيك بنجاح!');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        // Next Steps
        $this->info('📋 الخطوات التالية:');
        $this->line('  1. اذهب إلى لوحة التحكم: /admin/infographics');
        $this->line('  2. أضف إنفوجرافيك جديد من "إضافة إنفوجرافيك جديد"');
        $this->line('  3. أضف قسم في "قوالب الصفحة الرئيسية" (نوع: infographic)');
        $this->line('  4. راجع التوثيق: INFOGRAPHIC_SYSTEM_README.md');
        $this->newLine();

        // API Test
        $this->info('🔌 اختبار API:');
        $this->line('  curl ' . config('app.url') . '/api/v1/infographics');
        $this->newLine();

        return Command::SUCCESS;
    }
}
