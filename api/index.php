<?php

// Ensure storage directories exist in /tmp for Vercel Serverless environment
$tmpDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Copy SQLite database to /tmp if using sqlite so it is writable in serverless
$dbSource = __DIR__ . '/../database/database.sqlite';
$dbTarget = '/tmp/database.sqlite';
if (!file_exists($dbTarget) && file_exists($dbSource)) {
    @copy($dbSource, $dbTarget);
} elseif (!file_exists($dbTarget)) {
    @touch($dbTarget);
}
putenv("DB_DATABASE={$dbTarget}");
$_ENV['DB_DATABASE'] = $dbTarget;

// Forward to Laravel public/index.php
require __DIR__ . '/../public/index.php';
