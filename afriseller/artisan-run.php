<?php

use Illuminate\Contracts\Console\Kernel;

// ─── Force correct base path ──────────────────────────────────
$basePath = __DIR__;

// Override env before Laravel boots
$_ENV['APP_BASE_PATH'] = $basePath;
putenv('APP_BASE_PATH=' . $basePath);

// ─── Create all required storage folders ──────────────────────
$storagePath = $basePath . '/storage';
$dirs = [
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
    $basePath . '/bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

// ─── Boot Laravel ─────────────────────────────────────────────
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// ─── Force correct storage path AFTER boot ────────────────────
$app->useStoragePath($storagePath);

$kernel = $app->make(Kernel::class);

// ─── Output ───────────────────────────────────────────────────
echo "<pre style='font-family:monospace; background:#0f172a; color:#a3e635; padding:20px; font-size:14px; line-height:1.8;'>";

echo "📂 Base Path:    $basePath\n";
echo "📂 Storage Path: $storagePath\n\n";

// ─── optimize:clear ───────────────────────────────────────────
echo "▶ php artisan optimize:clear\n";
$kernel->call('optimize:clear');
echo $kernel->output();
echo "✅ optimize:clear — SUCCESS\n\n";

// ─── optimize ─────────────────────────────────────────────────
echo "▶ php artisan optimize\n";
$kernel->call('optimize');
echo $kernel->output();
echo "✅ optimize — SUCCESS\n\n";


// ─── cache:clear ──────────────────────────────────────────────
echo "▶ php artisan cache:clear\n";
$kernel->call('cache:clear');
echo $kernel->output();
echo "✅ cache:clear — SUCCESS\n\n";

// ─── config:clear ─────────────────────────────────────────────
echo "▶ php artisan config:clear\n";
$kernel->call('config:clear');
echo $kernel->output();
echo "✅ config:clear — SUCCESS\n\n";

// ─── route:clear ──────────────────────────────────────────────
echo "▶ php artisan route:clear\n";
$kernel->call('route:clear');
echo $kernel->output();
echo "✅ route:clear — SUCCESS\n\n";

// ─── view:clear ───────────────────────────────────────────────
echo "▶ php artisan view:clear\n";
$kernel->call('view:clear');
echo $kernel->output();
echo "✅ view:clear — SUCCESS\n\n";

// ─── storage:link ─────────────────────────────────────────────
echo "▶ php artisan storage:link\n";
$kernel->call('storage:link');
echo $kernel->output();
echo "✅ storage:link — SUCCESS\n\n";

// ─── Done ─────────────────────────────────────────────────────
echo "🎉 All commands completed successfully!\n";

echo "</pre>";