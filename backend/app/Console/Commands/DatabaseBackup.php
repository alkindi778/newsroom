<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Database Backup Command
 * أمر لإنشاء نسخ احتياطية من قاعدة البيانات
 */
class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database 
                            {--compress : ضغط ملف النسخة الاحتياطية}
                            {--keep=7 : عدد أيام الاحتفاظ بالنسخ القديمة}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء نسخة احتياطية من قاعدة البيانات';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 بدء عملية النسخ الاحتياطي...');

        try {
            // إنشاء مجلد النسخ الاحتياطية
            $backupPath = 'backups/database';
            if (!Storage::exists($backupPath)) {
                Storage::makeDirectory($backupPath);
            }

            // اسم ملف النسخة الاحتياطية
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $fullPath = storage_path('app/' . $backupPath . '/' . $filename);

            // معلومات قاعدة البيانات
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');

            // أمر mysqldump
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s %s %s > %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $password ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($fullPath)
            );

            // تنفيذ الأمر
            $output = [];
            $returnVar = 0;
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('فشل في تنفيذ mysqldump: ' . implode("\n", $output));
            }

            // التحقق من وجود الملف
            if (!file_exists($fullPath)) {
                throw new \Exception('لم يتم إنشاء ملف النسخة الاحتياطية');
            }

            $fileSize = filesize($fullPath);

            // ضغط الملف إذا طُلب
            if ($this->option('compress')) {
                $this->info('📦 جاري ضغط النسخة الاحتياطية...');
                
                $gzFilename = $filename . '.gz';
                $gzPath = storage_path('app/' . $backupPath . '/' . $gzFilename);
                
                $file = fopen($fullPath, 'rb');
                $gzFile = gzopen($gzPath, 'wb9');
                
                while (!feof($file)) {
                    gzwrite($gzFile, fread($file, 1024 * 512));
                }
                
                fclose($file);
                gzclose($gzFile);
                
                // حذف الملف الأصلي
                unlink($fullPath);
                
                $filename = $gzFilename;
                $fileSize = filesize($gzPath);
            }

            // حذف النسخ القديمة
            $keepDays = (int) $this->option('keep');
            $deletedCount = $this->cleanOldBackups($backupPath, $keepDays);

            // تسجيل في Log
            Log::info('Database backup completed', [
                'filename' => $filename,
                'size' => $this->formatBytes($fileSize),
                'deleted_old' => $deletedCount,
            ]);

            $this->info("✅ تم إنشاء النسخة الاحتياطية بنجاح!");
            $this->table(
                ['المعلومات', 'القيمة'],
                [
                    ['اسم الملف', $filename],
                    ['الحجم', $this->formatBytes($fileSize)],
                    ['المسار', $backupPath . '/' . $filename],
                    ['النسخ المحذوفة', $deletedCount],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ فشل في إنشاء النسخة الاحتياطية: ' . $e->getMessage());
            
            Log::error('Database backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * حذف النسخ الاحتياطية القديمة
     */
    protected function cleanOldBackups(string $path, int $keepDays): int
    {
        $deletedCount = 0;
        $cutoffDate = now()->subDays($keepDays);

        $files = Storage::files($path);
        
        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);
            
            if ($lastModified < $cutoffDate->timestamp) {
                Storage::delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * تنسيق حجم الملف
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor(log($bytes, 1024));
        
        return sprintf('%.2f %s', $bytes / pow(1024, $factor), $units[$factor]);
    }
}
