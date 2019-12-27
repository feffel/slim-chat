<?php
declare(strict_types=1);


$container = $app->getContainer();

// App Service Providers
$container->register(new \Chat\Services\Database\EloquentServiceProvider());

// Request Validator
$container['validator'] = function ($container) {
    \Respect\Validation\Validator::with('\\Chat\\Validation\\Rules');
    return new \Chat\Validation\Validator();
};

$container['idAuth'] = fn($c) => new \Chat\Services\Auth\IdAuth($c);
