<?php
declare(strict_types=1);

namespace Chat\Services\Auth;

use Chat\Repositories\UserRepository;
use Chat\Validation\Validator;
use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class IdAuth
{
    protected const AUTH_HEADER = 'HTTP_AUTHORIZATION';

    protected Manager        $db;
    protected Validator      $validator;
    protected UserRepository $userRepo;

    public function __construct(ContainerInterface $container)
    {
        $this->db        = $container->get('db');
        $this->validator = $container->get('validator');
        $this->userRepo  = new UserRepository();
    }

    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (!$this->canAuthenticate($request)) {
            return $response->withJson(['errors' => $this->validator->getErrors()], 401);
        }
        $user = $this->userRepo->find($request->getHeader(self::AUTH_HEADER));
        return $next($request->withAttribute('user', $user), $response);
    }

    private function canAuthenticate(Request $request): bool
    {
        $rules = [
            self::AUTH_HEADER => v::notEmpty()->arrayType()
                ->arrayVal()->key('0', v::notEmpty())
                ->arrayVal()->key('0', v::digit())
                ->existsInTable($this->db::table('users'), 'id'),
        ];
        $this->validator->validateArray($request->getHeaders(), $rules);
        return !$this->validator->failed();
    }
}
