<?php
declare(strict_types=1);


require_once './vendor/autoload.php';

use Chat\Services\Database\EloquentServiceProvider;
use Slim\App;
$settings  = require './app/settings.php';
$app       = new App($settings);
$container = $app->getContainer();
$container->register(new EloquentServiceProvider());
$config = $container['settings']['database'];

return [
    'paths'                => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds',
    ],
    'migration_base_class' => 'Migration',
    'templates'            => [
        'class' => 'TemplateGenerator',
    ],
    'environments'         => [
        'default_migration_table' => 'migrations',
        'default_database'        => 'development',
        'development'             => [
            'name'       => $config['database'],
            'connection' => $container->get('db')->getConnection()->getPdo(),
        ],
    ],
];
