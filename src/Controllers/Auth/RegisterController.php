<?php

namespace Chat\Controllers\Auth;

use Chat\Controllers\BaseController;
use Chat\Repositories\UserRepository;
use Chat\Transformers\UserTransformer;
use Chat\Validation\Validator;
use League\Fractal\Resource\Item;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class RegisterController extends BaseController
{
    protected UserRepository $repo;

    public function __construct(ContainerInterface $container)
    {
        $this->repo = new UserRepository();
        parent::__construct($container);
    }

    public function register(Request $request, Response $response): Response
    {
        $params     = $request->getParams();
        $validation = $this->validateRegisterRequest($params);
        if ($validation->failed()) {
            return $response->withJson(['errors' => $validation->getErrors()], 422);
        }
        $resource = new Item($this->repo->create($params['username']), new UserTransformer());
        return $response->withJson($this->serialize($resource), 201);
    }

    protected function validateRegisterRequest(array $values): Validator
    {
        return $this->validator->validateArray(
            $values,
            [
                'username' => v::notEmpty()->noWhitespace()
                    ->not(v::existsInTable($this->db::table('users'), 'username')),
            ]
        );
    }
}
