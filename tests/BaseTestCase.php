<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class BaseTestCase extends TestCase
{
    protected App $app;

    protected bool $withMiddleware = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = $this->createApplication();
    }

    protected function tearDown(): void
    {
        unset($this->app);
        parent::tearDown();
    }

    /**
     * @param string            $requestMethod
     * @param string            $requestUri
     * @param array|object|null $requestData
     * @param array             $headers
     *
     * @return ResponseInterface|Response
     */
    public function runApp(
        string $requestMethod,
        string $requestUri,
        $requestData = null,
        array $headers = []
    ): ResponseInterface {
        $environment = Environment::mock(
            array_merge(
                [
                    'REQUEST_METHOD' => $requestMethod,
                    'REQUEST_URI'    => $requestUri,
                    'Content-Type'   => 'application/json',
                ],
                $headers
            )
        );
        $request     = Request::createFromEnvironment($environment);
        if ($requestData !== null) {
            $request = $request->withParsedBody($requestData);
        }
        $response = new Response();

        return $this->app->process($request, $response);
    }

    protected function createApplication(): App
    {
        // Use the application settings
        $settings = require __DIR__.'/../app/settings.php';
        // Instantiate the application
        $app = new App($settings);
        require ROOT.'app/dependencies.php';
        if ($this->withMiddleware) {
            require ROOT.'app/middleware.php';
        }
        require ROOT.'app/routes.php';

        return $app;
    }
}
