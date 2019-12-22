<?php
declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

$app->add(
    function (Request $request, Response $response, callable $next) {
        $uri  = $request->getUri();
        $path = $uri->getPath();
        if ($path !== '/' && substr($path, -1) === '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));
            return $next($request->withUri($uri), $response);
        }

        return $next($request, $response);
    }
);

$app->add(
    function ($req, $res, $next) {
        $response = $next($req, $res);

        return $response
            ->withHeader('Access-Control-Allow-Origin', $this->get('settings')['cors'])
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
);
