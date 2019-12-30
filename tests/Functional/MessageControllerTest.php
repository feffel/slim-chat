<?php
declare(strict_types=1);

namespace Tests\Functional;

use Chat\Repositories\ConversationRepository;
use Illuminate\Support\Arr;
use Tests\ApiTestTrait;
use Tests\BaseDatabaseTestCase;

class MessageControllerTest extends BaseDatabaseTestCase
{
    use ApiTestTrait;

    /** @test */
    public function can_index_messages()
    {
        $message = $this->createMessage();
        $message->conversation->participants()->save($message->author);
        $conversationId = $message->conversation->id;
        $response = $this->request('GET', "/api/conversations/$conversationId/messages", null, $message->author);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['data', 'meta'], $response));
        $this->assertIsArray($messages = $this->responseFieldValue('data', $response));
        $this->assertTrue(Arr::has($msg = Arr::first($messages), ['id', 'content', 'author']));
        $this->assertEquals($message->id, $msg['id']);
    }

    /** @test */
    public function index_messages_not_found_on_conversation_doesnt_exist()
    {
        $message = $this->createMessage();
        $response = $this->request('GET', '/api/conversations/999/messages', null, $message->author);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function index_messages_not_found_on_user_is_not_participant()
    {
        $message = $this->createMessage();
        $conversationId = $message->conversation->id;
        $response = $this->request('GET', "/api/conversations/$conversationId/messages", null, $message->author);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function can_get_message()
    {
        $message = $this->createMessage();
        $message->conversation->participants()->save($message->author);
        $conversationId = $message->conversation->id;
        $response = $this->request('GET', "/api/conversations/$conversationId/messages/$message->id", null, $message->author);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['id', 'content', 'author'], $response));
        $this->assertEquals($message->id, $this->responseFieldValue('id', $response));
        $this->assertEquals($message->content, $this->responseFieldValue('content', $response));
        $this->assertEquals($message->author->id, $this->responseFieldValue('author.id', $response));
    }

    /** @test */
    public function get_not_found_on_conversation_doesnt_exist()
    {
        $message = $this->createMessage();
        $message->conversation->participants()->save($message->author);
        $response = $this->request('GET', "/api/conversations/999/messages/$message->id", null, $message->author);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function can_create_message()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation();
        $conversation->participants()->save($user);
        $content = 'hello from the other side';
        $response = $this->request('POST', "/api/conversations/$conversation->id/messages", ['content'=>$content], $user);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['id', 'content', 'author'], $response));
        $this->assertEquals($content, $this->responseFieldValue('content', $response));
        $this->assertEquals($user->id, $this->responseFieldValue('author.id', $response));
    }

    /** @test */
    public function create_message_error_on_missing_content()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation();
        $conversation->participants()->save($user);
        $response = $this->request('POST', "/api/conversations/$conversation->id/messages", null, $user);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['errors'], $response));
    }

    /** @test */
    public function create_message_not_found_on_conversation_doesnt_exist()
    {
        $user = $this->createUser();
        $content = 'hello from the other side';
        $response = $this->request('POST', '/api/conversations/999/messages', ['content' =>$content], $user);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function create_message_not_found_on_user_not_participant_in_conversation()
    {
        $user = $this->createUser();
        $conversation = $this->createConversation();
        $content = 'hello from the other side';
        $response = $this->request('POST', "/api/conversations/$conversation->id/messages", ['content' =>$content], $user);
        $this->assertEquals(404, $response->getStatusCode());
    }
}