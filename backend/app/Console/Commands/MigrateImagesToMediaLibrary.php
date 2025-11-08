<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\Writer;
use App\Models\Opinion;
use App\Models\User;
use App\Helpers\MediaHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MigrateImagesToMediaLibrary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:migrate-images {--force : Force migration even if media already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing images from storage to Spatie Media Library';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 بدء نقل الصور إلى Media Library...');
        
        $force = $this->option('force');
        
        // إحصائيات
        $stats = [
            'articles' => ['migrated' => 0, 'skipped' => 0, 'errors' => 0],
            'writers' => ['migrated' => 0, 'skipped' => 0, 'errors' => 0],
            'opinions' => ['migrated' => 0, 'skipped' => 0, 'errors' => 0],
            'users' => ['migrated' => 0, 'skipped' => 0, 'errors' => 0],
        ];

        // نقل صور المقالات
        $this->info('📰 نقل صور المقالات...');
        $this->migrateArticleImages($stats['articles'], $force);

        // نقل صور الكُتاب
        $this->info('✍️ نقل صور الكُتاب...');
        $this->migrateWriterImages($stats['writers'], $force);

        // نقل صور مقالات الرأي
        $this->info('💭 نقل صور مقالات الرأي...');
        $this->migrateOpinionImages($stats['opinions'], $force);

        // نقل صور المستخدمين
        $this->info('👤 نقل صور المستخدمين...');
        $this->migrateUserImages($stats['users'], $force);

        // عرض الإحصائيات النهائية
        $this->displayFinalStats($stats);

        $this->info('✅ تم الانتهاء من نقل الصور!');
        return 0;
    }

    /**
     * نقل صور المقالات
     */
    private function migrateArticleImages(&$stats, $force)
    {
        $articles = Article::whereNotNull('image')->get();
        
        $progressBar = $this->output->createProgressBar($articles->count());
        $progressBar->start();

        foreach ($articles as $article) {
            try {
                // التحقق من وجود صورة في Media Library
                if (!$force && MediaHelper::hasImage($article, MediaHelper::COLLECTION_ARTICLES)) {
                    $stats['skipped']++;
                    $progressBar->advance();
                    continue;
                }

                // التحقق من وجود الملف
                $imagePath = storage_path('app/public/' . $article->image);
                if (!File::exists($imagePath)) {
                    $stats['errors']++;
                    $progressBar->advance();
                    continue;
                }

                // نقل الصورة إلى Media Library
                $article->addMedia($imagePath)
                    ->preservingOriginal()
                    ->withCustomProperties([
                        'alt' => $article->title,
                        'title' => $article->title,
                        'migrated_from' => $article->image
                    ])
                    ->toMediaCollection(MediaHelper::COLLECTION_ARTICLES);

                $stats['migrated']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("خطأ في نقل صورة المقال {$article->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * نقل صور الكُتاب
     */
    private function migrateWriterImages(&$stats, $force)
    {
        $writers = Writer::whereNotNull('image')->get();
        
        $progressBar = $this->output->createProgressBar($writers->count());
        $progressBar->start();

        foreach ($writers as $writer) {
            try {
                // التحقق من وجود صورة في Media Library
                if (!$force && MediaHelper::hasImage($writer, MediaHelper::COLLECTION_WRITERS)) {
                    $stats['skipped']++;
                    $progressBar->advance();
                    continue;
                }

                // التحقق من وجود الملف
                $imagePath = storage_path('app/public/' . $writer->image);
                if (!File::exists($imagePath)) {
                    $stats['errors']++;
                    $progressBar->advance();
                    continue;
                }

                // نقل الصورة إلى Media Library
                $writer->addMedia($imagePath)
                    ->preservingOriginal()
                    ->withCustomProperties([
                        'alt' => $writer->name,
                        'title' => $writer->name,
                        'migrated_from' => $writer->image
                    ])
                    ->toMediaCollection(MediaHelper::COLLECTION_WRITERS);

                $stats['migrated']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("خطأ في نقل صورة الكاتب {$writer->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * نقل صور مقالات الرأي
     */
    private function migrateOpinionImages(&$stats, $force)
    {
        $opinions = Opinion::whereNotNull('image')->get();
        
        $progressBar = $this->output->createProgressBar($opinions->count());
        $progressBar->start();

        foreach ($opinions as $opinion) {
            try {
                // التحقق من وجود صورة في Media Library
                if (!$force && MediaHelper::hasImage($opinion, MediaHelper::COLLECTION_OPINIONS)) {
                    $stats['skipped']++;
                    $progressBar->advance();
                    continue;
                }

                // التحقق من وجود الملف
                $imagePath = storage_path('app/public/' . $opinion->image);
                if (!File::exists($imagePath)) {
                    $stats['errors']++;
                    $progressBar->advance();
                    continue;
                }

                // نقل الصورة إلى Media Library
                $opinion->addMedia($imagePath)
                    ->preservingOriginal()
                    ->withCustomProperties([
                        'alt' => $opinion->title,
                        'title' => $opinion->title,
                        'migrated_from' => $opinion->image
                    ])
                    ->toMediaCollection(MediaHelper::COLLECTION_OPINIONS);

                $stats['migrated']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("خطأ في نقل صورة مقال الرأي {$opinion->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * نقل صور المستخدمين
     */
    private function migrateUserImages(&$stats, $force)
    {
        $users = User::whereNotNull('avatar')->get();
        
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            try {
                // التحقق من وجود صورة في Media Library
                if (!$force && MediaHelper::hasImage($user, MediaHelper::COLLECTION_USERS)) {
                    $stats['skipped']++;
                    $progressBar->advance();
                    continue;
                }

                // التحقق من وجود الملف
                $imagePath = storage_path('app/public/' . $user->avatar);
                if (!File::exists($imagePath)) {
                    $stats['errors']++;
                    $progressBar->advance();
                    continue;
                }

                // نقل الصورة إلى Media Library
                $user->addMedia($imagePath)
                    ->preservingOriginal()
                    ->withCustomProperties([
                        'alt' => $user->name,
                        'title' => $user->name,
                        'migrated_from' => $user->avatar
                    ])
                    ->toMediaCollection(MediaHelper::COLLECTION_USERS);

                $stats['migrated']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("خطأ في نقل صورة المستخدم {$user->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * عرض الإحصائيات النهائية
     */
    private function displayFinalStats($stats)
    {
        $this->newLine();
        $this->info('📊 إحصائيات النقل:');
        $this->newLine();

        $headers = ['النوع', 'تم النقل', 'تم التخطي', 'أخطاء', 'الإجمالي'];
        $rows = [];

        foreach ($stats as $type => $data) {
            $total = $data['migrated'] + $data['skipped'] + $data['errors'];
            $typeNames = [
                'articles' => 'المقالات',
                'writers' => 'الكُتاب',
                'opinions' => 'مقالات الرأي',
                'users' => 'المستخدمين'
            ];
            
            $rows[] = [
                $typeNames[$type] ?? $type,
                "<info>{$data['migrated']}</info>",
                "<comment>{$data['skipped']}</comment>",
                $data['errors'] > 0 ? "<error>{$data['errors']}</error>" : $data['errors'],
                $total
            ];
        }

        $this->table($headers, $rows);
    }
}
