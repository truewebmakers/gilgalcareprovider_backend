<?php
// phpinfo(); 
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}
 
// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';


$app = require_once __DIR__.'/../bootstrap/app.php';

// Bootstrap Laravel and handle the request...
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
