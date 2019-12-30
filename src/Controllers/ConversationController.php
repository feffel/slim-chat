<?php
declare(strict_types=1);

namespace Chat\Controllers;

use Chat\Models\Conversation;
use Chat\Repositories\ConversationRepository;
use Chat\Repositories\UserRepository;
use Chat\Services\Pagination\CursorPaginator;
use Chat\Transformers\ConversationTransformer;
use Chat\Validation\Validator;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class ConversationController extends BaseController
{
    protected UserRepository         $userRepo;
    protected ConversationRepository $conversationRepo;

    public function __construct(ContainerInterface $container)
    {
        $this->userRepo         = new UserRepository();
        $this->conversationRepo = new ConversationRepository();
        parent::__construct($container);
    }

    public function index(Request $request, Response $response): Response
    {
        $updateField = $this->conversationRepo->alias(Conversation::TABLE, 'updated_at');
        $builder     = $this->conversationRepo->filterByParticipant($request->getAttribute('user'))
            ->orderBy($updateField, 'DESC');
        $cursor      = (new CursorPaginator(false, $updateField))
            ->paginate($request, $builder);

        $resource = (new Collection($builder->get(), new ConversationTransformer()))
            ->setCursor($cursor);
        return $response->withJson($this->serialize($resource), 200);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $validation = $this->validateGetRequest($args);
        if ($validation->failed()) {
            return $response->withJson(['errors' => $validation->getErrors()], 400);
        }
        $conversation = $this->conversationRepo
            ->filterByParticipant($request->getAttribute('user'))
            ->where($this->conversationRepo->alias(Conversation::TABLE, 'id'), '=', $args['id'])
            ->first();
        if ($conversation === null) {
            return $this->notFound($response);
        }

        $resource = new Item($conversation, new ConversationTransformer());
        return $response->withJson($this->serialize($resource), 200);
    }

    public function search(Request $request, Response $response): Response
    {
        $params = $request->getParams();
        $validation = $this->validateSearchRequest($params);
        if ($validation->failed()) {
            return $response->withJson(['errors' => $validation->getErrors()], 400);
        }
        $conversation = $this->conversationRepo
            ->getOrCreate($request->getAttribute('user'), $this->userRepo->find($params['user']));

        $resource = new Item($conversation, new ConversationTransformer());
        return $response->withJson($this->serialize($resource), 200);
    }

    protected function validateGetRequest(array $values): Validator
    {
        return $this->validator->validateArray($values, ['id' => v::notEmpty()->noWhitespace()->digit(),]);
    }

    protected function validateSearchRequest(array $values): Validator
    {
        return $this->validator->validateArray(
            $values,
            ['user' => v::notEmpty()->noWhitespace()->digit(),]
        );
    }
}
