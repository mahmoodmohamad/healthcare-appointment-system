<?php

/**
 * Laravel Application Entry Point
 * 
 * This is the entry point for all requests entering the application.
 * It handles the initialization, request processing, and response delivery.
 * 
 * @package    Healthcare Appointment System
 * @author     Your Name
 * @version    1.0.0
 * @since      2024-12-25
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Start Time
|--------------------------------------------------------------------------
|
| Record the start time of the application to enable performance monitoring
| and benchmarking. This helps identify slow requests and optimization needs.
|
*/

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance mode via the "php artisan down" command,
| we will load the maintenance file to display a user-friendly message instead
| of showing errors or starting the full framework.
|
*/

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader that
| makes it easy to load our application classes. We'll require it into the
| script here so all classes are available automatically.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application instance, we can handle the incoming request
| through the application's HTTP kernel. The response is then sent back
| to the client's browser, completing the request-response cycle.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Handle The Request
|--------------------------------------------------------------------------
|
| Create the HTTP kernel instance, capture the incoming request, process it
| through the middleware stack and router, and send the response back.
|
*/

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

/*
|--------------------------------------------------------------------------
| Terminate The Application
|--------------------------------------------------------------------------
|
| Once we have sent the response, we need to call the terminate method on
| the kernel to perform any final cleanup and allow middleware to execute
| their termination logic.
|
*/

$kernel->terminate($request, $response);