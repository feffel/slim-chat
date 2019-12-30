<?php

namespace Tests;

use Chat\Models\User;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

trait ApiTestTrait
{
    protected function request(
        string $requestMethod,
        string $requestUri,
        $requestData = null,
        User $auth = null,
        array $headers = []
    ): ResponseInterface {
        $environment = Environment::mock(
            array_merge(
                [
                    'REQUEST_METHOD' => $requestMethod,
                    'REQUEST_URI'    => $requestUri,
                    'Content-Type'   => 'application/json',
                ],
                array_filter(['HTTP_AUTHORIZATION' => $auth->id]),
                $headers
            )
        );
        $request = Request::createFromEnvironment($environment);
        if ($requestData !== null) {
            $request = $request->withParsedBody($requestData);
        }
        $response = new Response();

        return $this->app->process($request, $response);
    }

    protected function parseResponseData(ResponseInterface $response): array
    {
        return json_decode((string)$response->getBody(), true);
    }

    protected function responseHas($fields, ResponseInterface $response): bool
    {
        return Arr::has($this->parseResponseData($response), $fields);
    }

    protected function responseFieldValue(string $field, ResponseInterface $response)
    {
        return Arr::get($this->parseResponseData($response), $field, null);
    }
}
