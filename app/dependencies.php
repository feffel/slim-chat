<?php
declare(strict_types=1);

// DIC configuration
use Chat\Services\Database\EloquentServiceProvider;

$container = $app->getContainer();
// App Service Providers
$container->register(new EloquentServiceProvider());
