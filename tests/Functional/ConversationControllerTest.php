<?php
declare(strict_types=1);

namespace Tests\Functional;

use Illuminate\Support\Arr;
use Tests\ApiTestTrait;
use Tests\BaseDatabaseTestCase;

class ConversationControllerTest extends BaseDatabaseTestCase
{
    use ApiTestTrait;

    /** @test */
    public function can_index_conversations()
    {
        $conversation = $this->createConversation();
        $conversation->participants()->save($user = $this->createUser());
        $response = $this->request('GET', '/api/conversations', null, $user);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['data', 'meta'], $response));
        $this->assertIsArray($convos = $this->responseFieldValue('data', $response));
        $this->assertTrue(Arr::has($convo = Arr::first($convos), ['id', 'participants']));
        $this->assertEquals($conversation->id, $convo['id']);
    }


    /** @test */
    public function index_conversations_filtered_by_user()
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $this->createConversation()->participants()->save($userA);
        $this->createConversation()->participants()->saveMany([$userA, $userB]);
        $response = $this->request('GET', '/api/conversations', null, $userA);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, $this->responseFieldValue('data', $response));
        $response = $this->request('GET', '/api/conversations', null, $userB);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $this->responseFieldValue('data', $response));
    }

    /** @test */
    public function can_get_conversation()
    {
        $conversation = $this->createConversation();
        $conversation->participants()->save($user = $this->createUser());
        $response = $this->request('GET', "/api/conversations/$conversation->id", null, $user);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['id', 'participants'], $response));
        $this->assertEquals($conversation->id, $this->responseFieldValue('id', $response));
    }

    /** @test */
    public function get_not_found_on_conversation_doesnt_exist()
    {
        $response = $this->request('GET', '/api/conversations/999', null, $this->createUser());
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function get_not_found_on_user_not_participant()
    {
        $conversation = $this->createConversation();
        $response = $this->request('GET', "/api/conversations/$conversation->id", null, $this->createUser());
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function can_search_for_conversation_by_user()
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $conversation = $this->createConversation();
        $conversation->participants()->saveMany([$userA, $userB]);
        $response = $this->request('GET', '/api/conversations/search', ['user' =>$userB->id], $userA);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['id', 'participants'], $response));
        $this->assertEquals($conversation->id, $this->responseFieldValue('id', $response));
    }

    /** @test */
    public function creates_new_conversation_for_user()
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $response = $this->request('GET', '/api/conversations/search', ['user' =>$userB->id], $userA);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['id', 'participants'], $response));
        $this->assertEquals(array_map(fn($u) => $u->id, [$userA, $userB]), Arr::pluck($this->responseFieldValue('participants.data', $response), 'id'));
    }

    /** @test */
    public function can_include_messages()
    {
        $conversation = $this->createConversation();
        $conversation->participants()->save($user = $this->createUser());
        $msg = $this->createMessage();
        $msg->conversation()->associate($conversation);
        $msg->save();
        $_GET['include'] = 'messages';
        $response = $this->request('GET', "/api/conversations/$conversation->id", null, $user);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($this->responseHas(['id', 'participants', 'messages'], $response));
    }
}
