<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (!class_exists(Dotenv::class)) {
    throw new \RuntimeException('The "symfony/dotenv" component is required to load environment variables.');
}

$dotenv = new Dotenv();
$dotenv->loadEnv(dirname(__DIR__).'/.env');

use App\Kernel;
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
