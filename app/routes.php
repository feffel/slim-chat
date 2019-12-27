<?php
declare(strict_types=1);

use Chat\Controllers\Auth\RegisterController;

// Api Routes
$app->group(
    '/api',
    function () {
        $idAuth = $this->getContainer()->get('idAuth');

        /** @var \Slim\App $this */
        $this->post('/users/register', RegisterController::class.':register')->setName('auth.user.register');
    }
);
