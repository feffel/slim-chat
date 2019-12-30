<?php
declare(strict_types=1);

use Chat\Controllers\Auth\RegisterController;
use Chat\Controllers\ConversationController;
use Chat\Controllers\MessageController;

// Api Routes
$app->group(
    '/api',
    function () {
        $idAuth = $this->getContainer()->get('idAuth');

        /** @var \Slim\App $this */
        $this->post('/users/register', RegisterController::class.':register')->setName('auth.user.register');

        $this->get('/conversations/{conversation:[0-9]+}/messages', MessageController::class.':index')->add($idAuth)->setName('conversation.message.index');
        $this->get('/conversations/{conversation:[0-9]+}/messages/{id:[0-9]+}', MessageController::class.':get')->add($idAuth)->setName('conversation.message.get');
        $this->post('/conversations/{conversation:[0-9]+}/messages', MessageController::class.':post')->add($idAuth)->setName('conversation.message.send');

        $this->get('/conversations', ConversationController::class.':index')->add($idAuth)->setName('conversation.index');
        $this->get('/conversations/search', ConversationController::class.':search')->add($idAuth)->setName('conversation.search');
        $this->get('/conversations/{id:[0-9]+}', ConversationController::class.':get')->add($idAuth)->setName('conversation.get');

    }
);
