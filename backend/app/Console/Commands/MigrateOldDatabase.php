<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Writer;
use App\Models\Opinion;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class MigrateOldDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:old-database {--step=all : Which step to run: categories, writers, opinions, articles, or all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from old adenstc_db database to new newsroom database';

    protected $oldConnection = 'old_database';
    protected $writerMapping = []; // Map old writer IDs to new writer IDs
    protected $categoryMapping = []; // Map old category IDs to new category IDs
    protected $defaultUserId;
    protected $defaultCategoryId;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting migration from old database...');

        // Get default user for articles (use first admin or create one)
        $this->defaultUserId = User::where('email', 'admin@example.com')->first()?->id ?? 1;
        
        // Get or create default category
        $defaultCategory = Category::firstOrCreate(
            ['slug' => 'general'],
            ['name' => 'عام', 'description' => 'فئة عامة']
        );
        $this->defaultCategoryId = $defaultCategory->id;

        $step = $this->option('step');

        try {
            if ($step === 'all' || $step === 'categories') {
                $this->migrateCategories();
            }

            if ($step === 'all' || $step === 'writers') {
                $this->migrateWriters();
            }

            if ($step === 'all' || $step === 'opinions') {
                $this->migrateOpinions();
            }

            if ($step === 'all' || $step === 'articles') {
                $this->migrateArticles();
            }

            $this->info('✅ Migration completed successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    /**
     * Migrate categories from old database
     */
    protected function migrateCategories()
    {
        $this->info('📁 Migrating categories...');

        try {
            $oldCategories = DB::connection($this->oldConnection)
                ->table('categories')
                ->get();

            $bar = $this->output->createProgressBar(count($oldCategories));
            $bar->start();

            foreach ($oldCategories as $oldCategory) {
                try {
                    $slug = Str::slug($oldCategory->name);
                    $counter = 1;
                    $originalSlug = $slug;
                    
                    // Ensure unique slug
                    while (Category::where('slug', $slug)->exists()) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }

                    $newCategory = Category::create([
                        'name' => $oldCategory->name,
                        'slug' => $slug,
                        'description' => $oldCategory->description ?? null,
                        'created_at' => $oldCategory->created_at ?? now(),
                        'updated_at' => $oldCategory->updated_at ?? now(),
                    ]);

                    // Map old category ID to new category ID
                    $this->categoryMapping[$oldCategory->id] = $newCategory->id;

                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("\nFailed to migrate category ID {$oldCategory->id}: {$e->getMessage()}");
                }
            }

            $bar->finish();
            $this->newLine();
            $this->info('✅ Categories migrated: ' . count($this->categoryMapping));
        } catch (\Exception $e) {
            $this->warn('⚠️  Categories table not found in old database or error: ' . $e->getMessage());
            $this->info('Continuing without categories migration...');
        }
    }

    /**
     * Migrate writers from old database
     */
    protected function migrateWriters()
    {
        $this->info('📝 Migrating writers...');

        $oldWriters = DB::connection($this->oldConnection)
            ->table('writers')
            ->where('status', 0)
            ->get();

        $bar = $this->output->createProgressBar(count($oldWriters));
        $bar->start();

        foreach ($oldWriters as $oldWriter) {
            try {
                $slug = Str::slug($oldWriter->name);
                $counter = 1;
                $originalSlug = $slug;
                
                // Ensure unique slug
                while (Writer::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $newWriter = Writer::create([
                    'name' => $oldWriter->name,
                    'slug' => $slug,
                    'email' => $oldWriter->email,
                    'bio' => $oldWriter->brief,
                    'image' => $this->migrateImagePath($oldWriter->image_name),
                    'facebook_url' => $oldWriter->facebook,
                    'twitter_url' => $oldWriter->twitter,
                    'is_active' => $oldWriter->status == 0,
                    'created_at' => $oldWriter->created_at,
                    'updated_at' => $oldWriter->updated_at,
                ]);

                // Map old ID to new ID
                $this->writerMapping[$oldWriter->id] = $newWriter->id;

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed to migrate writer ID {$oldWriter->id}: {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Writers migrated: ' . count($this->writerMapping));
    }

    /**
     * Migrate opinions (articles from old database)
     */
    protected function migrateOpinions()
    {
        $this->info('📰 Migrating opinions (old articles)...');

        $oldArticles = DB::connection($this->oldConnection)
            ->table('articles')
            ->where('status', 0)
            ->orderBy('published_at', 'desc')
            ->get();

        $bar = $this->output->createProgressBar(count($oldArticles));
        $bar->start();

        foreach ($oldArticles as $oldArticle) {
            try {
                // Get the new writer ID
                $writerId = $this->writerMapping[$oldArticle->writer_id] ?? null;
                
                if (!$writerId) {
                    // Create a default writer if not found
                    $defaultWriter = Writer::firstOrCreate(
                        ['slug' => 'كاتب-افتراضي'],
                        [
                            'name' => 'كاتب افتراضي',
                            'bio' => 'كاتب',
                            'is_active' => true,
                        ]
                    );
                    $writerId = $defaultWriter->id;
                }

                $slug = Str::slug($oldArticle->title);
                $counter = 1;
                $originalSlug = $slug;
                
                // Ensure unique slug
                while (Opinion::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                Opinion::create([
                    'title' => $oldArticle->title,
                    'slug' => $slug,
                    'excerpt' => $oldArticle->brief,
                    'content' => $this->cleanContent($oldArticle->content),
                    'image' => $this->migrateImagePath($oldArticle->image_name),
                    'writer_id' => $writerId,
                    'is_published' => $oldArticle->status == 0,
                    'published_at' => $oldArticle->published_at ?? $oldArticle->created_at,
                    'views' => $oldArticle->counts ?? 0,
                    'is_featured' => $oldArticle->editor_choice == 1,
                    'created_at' => $oldArticle->created_at,
                    'updated_at' => $oldArticle->updated_at,
                ]);

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed to migrate opinion ID {$oldArticle->id}: {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Opinions migrated successfully!');
    }

    /**
     * Migrate articles (posts from old database)
     */
    protected function migrateArticles()
    {
        $this->info('📄 Migrating articles (old posts)...');

        $oldPosts = DB::connection($this->oldConnection)
            ->table('posts')
            ->where('status', 0)
            ->orderBy('published_at', 'desc')
            ->get();

        $bar = $this->output->createProgressBar(count($oldPosts));
        $bar->start();

        $migratedCount = 0;

        foreach ($oldPosts as $oldPost) {
            try {
                // Map old category to new category
                $categoryId = $this->categoryMapping[$oldPost->category_id] ?? $this->defaultCategoryId;

                $slug = Str::slug($oldPost->title);
                $counter = 1;
                $originalSlug = $slug;
                
                // Ensure unique slug
                while (Article::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                Article::create([
                    'title' => $oldPost->title,
                    'slug' => $slug,
                    'subtitle' => $oldPost->intro_title,
                    'excerpt' => $oldPost->brief,
                    'content' => $this->cleanContent($oldPost->content),
                    'image' => $this->migrateImagePath($oldPost->image_name),
                    'source' => $oldPost->source,
                    'category_id' => $categoryId,
                    'user_id' => $this->defaultUserId,
                    'is_published' => $oldPost->status == 0,
                    'approval_status' => 'approved',
                    'approved_at' => $oldPost->published_at ?? $oldPost->created_at,
                    'published_at' => $oldPost->published_at ?? $oldPost->created_at,
                    'views_count' => $oldPost->counts ?? 0,
                    'is_featured' => $oldPost->editor_choice == 1,
                    'show_in_slider' => $oldPost->priority >= 5,
                    'is_breaking_news' => false,
                    'created_at' => $oldPost->created_at,
                    'updated_at' => $oldPost->updated_at,
                ]);

                $migratedCount++;
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed to migrate article ID {$oldPost->id}: {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Articles migrated: {$migratedCount} / " . count($oldPosts));
    }

    /**
     * Migrate image path from old format to new format
     */
    protected function migrateImagePath($oldPath)
    {
        if (empty($oldPath) || $oldPath === '0') {
            return null;
        }

        // Old format: 012020/5e1479924fcb3.jpeg or 1.jpg
        // New format: we'll keep it as is for now, or you can copy files to new location

        return 'old_photos/' . $oldPath;
    }

    /**
     * Clean HTML content
     */
    protected function cleanContent($content)
    {
        if (empty($content)) {
            return '';
        }

        // Remove excessive line breaks
        $content = preg_replace('/(<p>&nbsp;<\/p>\s*){2,}/i', '<p>&nbsp;</p>', $content);
        
        // Update image paths
        $content = str_replace('/photos/', '/storage/old_photos/', $content);

        return $content;
    }
}
