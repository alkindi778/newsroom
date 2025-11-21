<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// جدولة الترجمة التلقائية لكل أنواع المحتوى - كل 10 دقائق
Schedule::command('content:translate --batch=50 --type=all')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground();
