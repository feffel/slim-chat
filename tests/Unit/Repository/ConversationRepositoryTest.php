<?php
declare(strict_types=1);

namespace Tests\Unit\Repository;

use Chat\Repositories\ConversationRepository;

class ConversationRepositoryTest extends \Tests\BaseDatabaseTestCase
{
    /** @test */
    public function get_previous_private_conversation(): void
    {
        $userA        = $this->createUser();
        $userB        = $this->createUser();
        $conversation = $this->createConversation();
        $conversation->participants()->saveMany([$userA, $userB]);
        $conversationRepo = new ConversationRepository();
        $this->assertEquals($conversation->id, $conversationRepo->get($userA, $userB)->id);
    }

    /** @test */
    public function null_if_no_previous_private_conversation(): void
    {
        $userA            = $this->createUser();
        $userB            = $this->createUser();
        $conversationRepo = new ConversationRepository();
        $this->assertNull($conversationRepo->get($userA, $userB));
    }

    /** @test */
    public function create_new_private_conversation(): void
    {
        $userA            = $this->createUser();
        $userB            = $this->createUser();
        $conversationRepo = new ConversationRepository();
        $conversation     = $conversationRepo->create($userA, $userB);
        $this->assertNotNull($conversation->id);
        $this->assertCount(2, $conversation->participants);
        $this->assertTrue($conversation->participants->contains($userA));
        $this->assertTrue($conversation->participants->contains($userB));
    }

    /** @test */
    public function get_previous_private_conversation_if_exists(): void
    {
        $userA        = $this->createUser();
        $userB        = $this->createUser();
        $conversation = $this->createConversation();
        $conversation->participants()->saveMany([$userA, $userB]);
        $conversationRepo = new ConversationRepository();
        $this->assertEquals($conversation->id, $conversationRepo->getOrCreate($userA, $userB)->id);
    }

    /** @test */
    public function creates_private_conversation_if_doesnt_exist(): void
    {
        $userA            = $this->createUser();
        $userB            = $this->createUser();
        $conversationRepo = new ConversationRepository();
        $conversation     = $conversationRepo->getOrCreate($userA, $userB);
        $this->assertNotNull($conversation->id);
        $this->assertCount(2, $conversation->participants);
        $this->assertTrue($conversation->participants->contains($userA));
        $this->assertTrue($conversation->participants->contains($userB));
    }

    /** @test */
    public function filters_by_user(): void
    {
        $userA            = $this->createUser();
        $userB            = $this->createUser();
        $userC            = $this->createUser();
        $conversationRepo = new ConversationRepository();
        $conversationRepo->create($userA, $userB);
        $conversationRepo->create($userA, $userC);
        $this->assertCount(2, $conversationRepo->filterByParticipant($userA)->get());
        $this->assertCount(1, $conversationRepo->filterByParticipant($userB)->get());
        $this->assertCount(1, $conversationRepo->filterByParticipant($userC)->get());
    }
}