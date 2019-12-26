<?php
declare(strict_types=1);

use Chat\Models\Conversation;
use Chat\Models\Message;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\BaseDatabaseTestCase;

class UserTest extends BaseDatabaseTestCase
{
    /** @test */
    public function can_have_many_comments(): void
    {
        $user = $this->createUser();
        $this->assertInstanceOf(HasMany::class, $user->messages());
        $this->assertInstanceOf(Message::class, $user->messages()->getRelated());
    }

    /** @test */
    public function can_participate_in_many_convesations(): void
    {
        $user = $this->createUser();
        $this->assertInstanceOf(BelongsToMany::class, $user->conversations());
        $this->assertInstanceOf(Conversation::class, $user->conversations()->getRelated());
    }

    /** @test */
    public function can_send_message(): void
    {
        $user         = $this->createUser();
        $conversation = $this->createConversation();
        $conversation->participants()->save($user);
        $message = new Message(['content' => 'hello world']);
        $user->sendMessage($message, $conversation);
        $this->assertCount(1, $user->messages);
    }

    /** @test */
    public function can_not_send_message_to_anonymous_conversation(): void
    {
        $user         = $this->createUser();
        $conversation = $this->createConversation();
        $message      = new Message(['content' => 'hello world']);
        $this->expectException(\Chat\Exceptions\ForbiddenConversationException::class);
        $this->expectExceptionCode(\Chat\Exceptions\ForbiddenConversationException::E_CODE);
        $user->sendMessage($message, $conversation);
        $this->assertCount(0, $user->messages);
    }

    /** @test */
    public function can_not_send_previously_sent_message(): void
    {
        $user         = $this->createUser();
        $conversation = $this->createConversation();
        $conversation->participants()->save($user);
        $message = $this->createMessage();
        $this->expectException(\Chat\Exceptions\MessageAlreadySentException::class);
        $this->expectExceptionCode(\Chat\Exceptions\MessageAlreadySentException::E_CODE);
        $user->sendMessage($message, $conversation);
    }
}
