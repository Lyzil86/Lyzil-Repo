<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// 1. Direct Static File Serving for Vercel Serverless
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$publicPath = realpath(__DIR__.'/../public');
$filePath = realpath($publicPath.$uri);

// Serve existing static files directly with correct MIME types
if ($filePath && str_starts_with($filePath, $publicPath) && is_file($filePath)) {
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'json' => 'application/json',
    ];

    $contentType = $mimeTypes[$extension] ?? (mime_content_type($filePath) ?: 'application/octet-stream');
    header("Content-Type: {$contentType}");
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Content-Length: '.filesize($filePath));
    readfile($filePath);
    exit;
}

// 2. Prepare writable /tmp directories for serverless Laravel
$tmpStorage = '/tmp/storage';
$tmpDirs = [
    $tmpStorage.'/app/public',
    $tmpStorage.'/framework/cache/data',
    $tmpStorage.'/framework/sessions',
    $tmpStorage.'/framework/views',
    $tmpStorage.'/logs',
];

foreach ($tmpDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 3. Ensure SQLite database is in writable /tmp
$dbSource = __DIR__.'/../database/database.sqlite';
$dbTarget = '/tmp/database.sqlite';
if (! file_exists($dbTarget) && file_exists($dbSource)) {
    @copy($dbSource, $dbTarget);
    @chmod($dbTarget, 0666);
} elseif (! file_exists($dbTarget)) {
    @touch($dbTarget);
    @chmod($dbTarget, 0666);
}

// 4. Set environment defaults for Vercel
$defaultAppKey = 'base64:pn5qtQPVbhXFYOl+vly/1U5bELpwYPOdi/KdfTQzsnI=';
putenv('APP_KEY='.(getenv('APP_KEY') ?: $defaultAppKey));
$_ENV['APP_KEY'] = getenv('APP_KEY') ?: $defaultAppKey;

putenv('APP_ENV=production');
$_ENV['APP_ENV'] = 'production';

putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';

putenv('DB_CONNECTION=sqlite');
$_ENV['DB_CONNECTION'] = 'sqlite';

putenv("DB_DATABASE={$dbTarget}");
$_ENV['DB_DATABASE'] = $dbTarget;

putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';

putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';

putenv('VIEW_COMPILED_PATH='.$tmpStorage.'/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage.'/framework/views';

// 5. Bootstrap Laravel 11/12/13 native HTTP lifecycle
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

try {
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->useStoragePath($tmpStorage);

    // Call native Laravel handleRequest
    $request = Request::capture();
    $response = $app->handleRequest($request);
    $response->send();
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Terjadi Kendala Serverless (Error 500)</h1>';
    echo '<p><strong>Pesan:</strong> '.htmlspecialchars($e->getMessage()).'</p>';
    echo '<p><strong>File:</strong> '.htmlspecialchars($e->getFile()).' (baris '.$e->getLine().')</p>';
    echo "<pre style='background:#f4f4f4;padding:10px;border-radius:5px;overflow:auto;'>".htmlspecialchars($e->getTraceAsString()).'</pre>';
}
