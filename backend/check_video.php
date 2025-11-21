<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$video = App\Models\Video::first();

echo "Title (AR): " . $video->title . PHP_EOL;
echo "Title (EN): " . ($video->title_en ?? 'NULL') . PHP_EOL;
echo PHP_EOL;

// Check API response
$resource = new App\Http\Resources\VideoResource($video);
$array = $resource->toArray(new Illuminate\Http\Request());

echo "API Response includes title_en: " . (isset($array['title_en']) ? 'YES' : 'NO') . PHP_EOL;
if (isset($array['title_en'])) {
    echo "title_en value: " . $array['title_en'] . PHP_EOL;
}
