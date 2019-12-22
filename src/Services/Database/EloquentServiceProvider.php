<?php
declare(strict_types=1);

namespace Chat\Services\Database;

use Illuminate\Database\Capsule\Manager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EloquentServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $capsule = new Manager();
        $config  = $container['settings']['database'];
        $capsule->addConnection(
            [
                'driver'    => $config['driver'],
                'database'  => $config['database'],
                'charset'   => $config['charset'],
                'collation' => $config['collation'],
                'prefix'    => $config['prefix'],
            ]
        );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $container['db'] = fn($c) => $capsule;
    }
}
