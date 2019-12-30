<?php
declare(strict_types=1);

namespace Tests\Unit\Repository;

use Chat\Repositories\MessageRepository;
use Tests\BaseDatabaseTestCase;

class MessageRepositoryTest extends BaseDatabaseTestCase
{
    /** @test */
    public function creates_new_message(): void
    {
        $messageRepository = new MessageRepository();
        $conversation      = $this->createConversation();
        $conversation->participants()->save($user = $this->createUser());
        $message = $messageRepository->create($user, $conversation, $messageContent = 'hello world');
        $this->assertNotNull($message->id);
        $this->assertEquals($messageContent, $message->content);
        $this->assertEquals($user->id, $message->author->id);
        $this->assertEquals($conversation->id, $message->conversation->id);
    }

    /** @test */
    public function filters_by_conversation(): void
    {
        $messageRepository = new MessageRepository();
        $messageA = $this->createMessage();
        $messageB = $this->createMessage();
//        $conversationA      = $this->createConversation();
//        $conversationB      = $this->createConversation();
//        $conversationA->participants()->save($user = $this->createUser());
//        $conversationB->participants()->save($user);
//        $messageA = $messageRepository->create($user, $conversationA, $messageContent = 'hello world');
//        $messageB = $messageRepository->create($user, $conversationB, $messageContent = 'hello world');
        $messagesConversationA = $messageRepository->filterByConversation($messageA->conversation)->get();
        $messagesConversationB = $messageRepository->filterByConversation($messageB->conversation)->get();
        $this->assertCount(1, $messagesConversationA);
        $this->assertCount(1, $messagesConversationB);
        $this->assertEquals($messageA->id, $messagesConversationA->first()->id);
        $this->assertEquals($messageB->id, $messagesConversationB->first()->id);
    }
}
