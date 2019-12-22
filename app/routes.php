<?php
declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

// Api Routes
$app->group(
    '/api',
    function () {
        /** @var \Slim\App $this */
        $this->get(
            '/hello',function (Request $request, Response $response, array $args) {
                $response->getBody()->write(json_encode(['msg' => 'Hello world!']));
                return $response;
            })->setName('hello.world');
    }
);
