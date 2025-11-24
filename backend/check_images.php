<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$article = App\Models\Article::find(7254);

if ($article) {
    preg_match_all('/<img[^>]+src="([^"]+)"/', $article->content, $matches);
    
    echo "عدد الصور: " . count($matches[1]) . "\n\n";
    
    foreach ($matches[1] as $src) {
        echo $src . "\n";
    }
} else {
    echo "المقالة غير موجودة\n";
}
