<?php
declare(strict_types=1);

namespace Chat\Controllers;

use Chat\Models\Conversation;
use Chat\Models\Message;
use Chat\Models\User;
use Chat\Repositories\ConversationRepository;
use Chat\Repositories\MessageRepository;
use Chat\Services\Pagination\CursorPaginator;
use Chat\Transformers\MessageTransformer;
use Chat\Validation\Validator;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Psr\Container\ContainerInterface;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class MessageController extends BaseController
{
    protected MessageRepository      $messageRepo;
    protected ConversationRepository $conversationRepo;

    public function __construct(ContainerInterface $container)
    {
        $this->messageRepo      = new MessageRepository();
        $this->conversationRepo = new ConversationRepository();
        parent::__construct($container);
    }

    public function index(Request $request, Response $response, array $args): Response
    {
        $conversationId = (int)$args['conversation'];
        if (!$this->conversationExists($request->getAttribute('user'), $conversationId)) {
            return $this->notFound($response);
        }
        $createField = $this->messageRepo->alias(Message::TABLE, 'created_at');
        $builder     = $this->messageRepo->filterByConversation($this->conversationRepo->find($conversationId))
            ->orderBy($createField, 'DESC');
        $cursor      = (new CursorPaginator(false, $createField))
            ->paginate($request, $builder);

        $resource = (new Collection($builder->get(), new MessageTransformer()))->setCursor($cursor);
        return $response->withJson($this->serialize($resource), 200);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $messageId      = (int)$args['id'];
        $conversationId = (int)$args['conversation'];
        if (!$this->conversationExists($request->getAttribute('user'), $conversationId)
            || !$this->messageExists($messageId, $conversationId)) {
            return $this->notFound($response);
        }

        $resource = new Item($this->messageRepo->find($messageId), new MessageTransformer());
        return $response->withJson($this->serialize($resource), 200);
    }

    public function post(Request $request, Response $response, array $args): Response
    {
        $args = array_merge($args, $request->getParams());
        $validation = $this->validatePostRequest($args);
        if ($validation->failed()) {
            return $response->withJson(['errors' => $validation->getErrors()], 400);
        }
        $user           = $request->getAttribute('user');
        $conversationId = (int)$args['conversation'];
        if (!$this->conversationExists($user, $conversationId)) {
            return $this->notFound($response);
        }
        $message = $this->messageRepo->create($user, $this->conversationRepo->find($conversationId), $args['content']);

        $resource = new Item($message, new MessageTransformer());
        return $response->withJson($this->serialize($resource), 201);
    }

    protected function conversationExists(User $user, int $conversationId): bool
    {
        return $this->conversationRepo
            ->filterByParticipant($user)
            ->where(
                $this->conversationRepo->alias(Conversation::TABLE, 'id'),
                '=',
                $conversationId
            )
            ->exists();
    }

    protected function messageExists(int $messageId, int $conversationId): bool
    {
        return Message::query()
            ->where('id', '=', $messageId)
            ->where('conversation_id', '=', $conversationId)
            ->exists();
    }

    protected function validatePostRequest(array $values): Validator
    {
        return $this->validator->validateArray(
            $values,
            [
                'conversation' => v::notEmpty()->noWhitespace()->digit(),
                'content'      => v::notEmpty()
            ]
        );
    }
}
