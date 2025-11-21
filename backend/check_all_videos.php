<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$videos = App\Models\Video::select('id', 'title', 'title_en')->get();

foreach ($videos as $video) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . PHP_EOL;
    echo "ID: " . $video->id . PHP_EOL;
    echo "AR: " . $video->title . PHP_EOL;
    echo "EN: " . ($video->title_en ?? 'NULL') . PHP_EOL;
}

echo PHP_EOL;
echo "Total: " . $videos->count() . PHP_EOL;
echo "Translated: " . $videos->where('title_en', '!=', null)->count() . PHP_EOL;
