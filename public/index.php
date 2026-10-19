<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-detect base path for both Local Development and cPanel hosting
if (file_exists(__DIR__ . '/../web-ipnet-final/vendor/autoload.php')) {
    $basePath = __DIR__ . '/../web-ipnet-final';
} elseif (file_exists(__DIR__ . '/../web-ipnet/vendor/autoload.php')) {
    $basePath = __DIR__ . '/../web-ipnet';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $basePath = __DIR__ . '/..';
} else {
    $basePath = __DIR__;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

// Bind public path if deployed in public_html
if (basename(__DIR__) === 'public_html' || realpath(__DIR__) !== realpath($basePath . '/public')) {
    $app->usePublicPath(__DIR__);
}

$app->handleRequest(Request::capture());
