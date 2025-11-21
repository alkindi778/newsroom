<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Simulate API request
$request = new Illuminate\Http\Request();
$videoController = new App\Http\Controllers\Api\VideoController(new App\Services\VideoService());

$response = $videoController->featured($request);
$data = json_decode($response->getContent(), true);

echo "API Response:" . PHP_EOL;
echo "Success: " . ($data['success'] ? 'YES' : 'NO') . PHP_EOL;
echo "Videos count: " . count($data['data']) . PHP_EOL;
echo PHP_EOL;

if (!empty($data['data'])) {
    $firstVideo = $data['data'][0];
    echo "First Video:" . PHP_EOL;
    echo "  ID: " . $firstVideo['id'] . PHP_EOL;
    echo "  Title (AR): " . $firstVideo['title'] . PHP_EOL;
    echo "  Has title_en: " . (isset($firstVideo['title_en']) ? 'YES' : 'NO') . PHP_EOL;
    if (isset($firstVideo['title_en'])) {
        echo "  Title (EN): " . $firstVideo['title_en'] . PHP_EOL;
    }
}
