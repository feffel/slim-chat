<?php
declare(strict_types=1);

use Chat\Controllers\Auth\RegisterController;

// Api Routes
$app->group(
    '/api',
    function () {
        /** @var \Slim\App $this */
        $this->post('/users/register', RegisterController::class.':register')->setName('auth.user.register');
    }
);
