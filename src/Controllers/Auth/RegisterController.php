<?php

namespace Chat\Controllers\Auth;

use Chat\Models\User;
use Chat\Validation\Validator;
use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class RegisterController
{
    protected Validator $validator;
    /** @var Manager */
    protected $db;

    public function __construct(ContainerInterface $container)
    {
        $this->db        = $container->get('db');
        $this->validator = $container->get('validator');
    }

    public function register(Request $request, Response $response): Response
    {
        $params     = $request->getParams();
        $validation = $this->validateRegisterRequest($params);
        if ($validation->failed()) {
            return $response->withJson(['errors' => $validation->getErrors()], 422);
        }
        return $response->withJson($this->createNewUser($params)->toArray(), 201);
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

    protected function createNewUser(array $data): User
    {
        $user           = new User();
        $user->username = $data['username'];
        $user->save();
        return $user;
    }
}
