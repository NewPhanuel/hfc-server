<?php
declare(strict_types=1);

use Whoops\Run as WhoopsRun;
use Whoops\Handler\JsonResponseHandler;
use DevPhanuel\Config\AllowCors;
use DevPhanuel\Core\Router;
use DevPhanuel\Config\Database;

require_once '../helpers.php';
require_once basePath("vendor/autoload.php");

date_default_timezone_set('Africa/Lagos');
$whoops = new WhoopsRun;
$whoops->pushHandler(new JsonResponseHandler);
$whoops->register();

require_once basePath("src/Config/Env.php");

// Fixing Cors
(new AllowCors)->init();

// Initialise RedBeanPHP ORM
(new Database)->init();

// Instantiating the router
$router = new Router();

// Get routes
require_once basePath('src/Core/routes.php');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route the request
$router->route($uri);