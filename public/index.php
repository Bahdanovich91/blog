<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Container\Container;
use App\Core\Http\App;
use App\Core\Routing\Router;

$app = new App(new Router(new Container()));
$app->handle();
