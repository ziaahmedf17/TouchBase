<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Laravel Root Path
|--------------------------------------------------------------------------
| On shared hosting the contents of public/ are placed in public_html/
| while the rest of the Laravel project lives in a sibling directory.
| Set LARAVEL_ROOT in public_html/.htaccess to point to that directory:
|   SetEnv LARAVEL_ROOT /home/username/touchbase
|
| On localhost (Laragon) no variable is needed — the fallback resolves
| to the standard ../  relative path automatically.
*/
$basePath = getenv('LARAVEL_ROOT') ?: realpath(__DIR__ . '/..');

if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
