<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportVideosFromDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:import-from-dump {--file= : SQL dump file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import videos from old SQL dump file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->option('file') ?: 'c:\xampp\htdocs\newsroom\adenstc_db.sql\adenstc_db.sql';

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info('Reading SQL dump file...');

        // قراءة الملف سطر بسطر للعثور على INSERT INTO `videos`
        $file = fopen($filePath, 'r');
        $videosData = [];
        
        while (($line = fgets($file)) !== false) {
            if (strpos($line, "INSERT INTO `videos` VALUES") !== false) {
                // استخراج البيانات من السطر
                $videosData = $this->parseVideosLine($line);
                break;
            }
        }
        fclose($file);

        if (empty($videosData)) {
            $this->error('No videos data found in SQL dump');
            return 1;
        }

        $this->info(count($videosData) . ' videos found in dump file');

        // الحصول على first user as fallback
        $defaultUser = User::first();
        if (!$defaultUser) {
            $this->error('No users found in database. Please create a user first.');
            return 1;
        }

        $this->info('Starting import...');
        $imported = 0;
        $skipped = 0;

        foreach ($videosData as $oldVideo) {
            try {
                // التحقق من عدم وجود الفيديو مسبقاً (بناءً على URL)
                $existingVideo = Video::where('video_url', $oldVideo['url'])->first();
                if ($existingVideo) {
                    $this->warn("Video already exists: {$oldVideo['title']}");
                    $skipped++;
                    continue;
                }

                // استخراج معرف الفيديو من YouTube URL
                $videoId = $this->extractYouTubeId($oldVideo['url']);
                $videoType = 'youtube';

                // إنشاء الفيديو الجديد
                $video = Video::create([
                    'title' => $oldVideo['title'],
                    'slug' => Str::slug($oldVideo['title']),
                    'description' => $oldVideo['brief'],
                    'video_url' => $oldVideo['url'],
                    'thumbnail' => $oldVideo['image_name'] ? 'videos/' . $oldVideo['image_name'] : null,
                    'video_type' => $videoType,
                    'video_id' => $videoId,
                    'views' => $oldVideo['counts'] ?? 0,
                    'is_published' => $oldVideo['status'] == 0 ? true : false,
                    'is_featured' => false,
                    'published_at' => $oldVideo['created_at'] ? Carbon::parse($oldVideo['created_at']) : now(),
                    'user_id' => $defaultUser->id,
                    'created_at' => $oldVideo['created_at'] ? Carbon::parse($oldVideo['created_at']) : now(),
                    'updated_at' => $oldVideo['updated_at'] ? Carbon::parse($oldVideo['updated_at']) : now(),
                ]);

                $this->info("✓ Imported: {$video->title}");
                $imported++;

            } catch (\Exception $e) {
                $this->error("Error importing video: {$oldVideo['title']}");
                $this->error($e->getMessage());
                $skipped++;
            }
        }

        $this->info("\n=== Import Complete ===");
        $this->info("Imported: {$imported}");
        $this->info("Skipped: {$skipped}");
        $this->info("Total: " . count($videosData));

        return 0;
    }

    /**
     * Parse the videos INSERT line from SQL dump
     */
    private function parseVideosLine($line)
    {
        $videos = [];
        
        // استخراج البيانات من INSERT INTO statement
        // النمط: (id, user_id, title, category_id, url, image_name, brief, counts, status, deleted_at, created_at, updated_at)
        
        // إزالة بداية السطر
        $line = str_replace("INSERT INTO `videos` VALUES ", "", $line);
        $line = rtrim($line, ";\n\r");

        // تقسيم على ()، مع تجاهل الأقواس داخل القيم
        preg_match_all('/\(([^)]+(?:\([^)]*\)[^)]*)*)\)/', $line, $matches);

        foreach ($matches[1] as $match) {
            $values = $this->parseValues($match);
            
            if (count($values) >= 12) {
                $videos[] = [
                    'id' => $values[0],
                    'user_id' => $values[1],
                    'title' => $values[2],
                    'category_id' => $values[3],
                    'url' => $values[4],
                    'image_name' => $values[5],
                    'brief' => $values[6],
                    'counts' => $values[7],
                    'status' => $values[8],
                    'deleted_at' => $values[9],
                    'created_at' => $values[10],
                    'updated_at' => $values[11],
                ];
            }
        }

        return $videos;
    }

    /**
     * Parse values from SQL row
     */
    private function parseValues($string)
    {
        $values = [];
        $current = '';
        $inQuote = false;
        $quoteChar = '';
        
        for ($i = 0; $i < strlen($string); $i++) {
            $char = $string[$i];
            
            if (($char === '"' || $char === "'") && ($i === 0 || $string[$i-1] !== '\\')) {
                if (!$inQuote) {
                    $inQuote = true;
                    $quoteChar = $char;
                } elseif ($char === $quoteChar) {
                    $inQuote = false;
                    $quoteChar = '';
                }
            } elseif ($char === ',' && !$inQuote) {
                $values[] = $this->cleanValue($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        // إضافة القيمة الأخيرة
        if ($current !== '') {
            $values[] = $this->cleanValue($current);
        }
        
        return $values;
    }

    /**
     * Clean value from quotes and trim
     */
    private function cleanValue($value)
    {
        $value = trim($value);
        
        // إزالة الأقواس
        if (($value[0] === '"' || $value[0] === "'") && $value[0] === $value[strlen($value) - 1]) {
            $value = substr($value, 1, -1);
        }
        
        // معالجة NULL
        if (strtoupper($value) === 'NULL') {
            return null;
        }
        
        return $value;
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYouTubeId($url)
    {
        // Patterns for YouTube URLs
        $patterns = [
            '/youtube\.com\/watch\?v=([^&]+)/',
            '/youtu\.be\/([^?]+)/',
            '/youtube\.com\/embed\/([^?]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
