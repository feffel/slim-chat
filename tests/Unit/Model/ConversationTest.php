<?php
declare(strict_types=1);

use Chat\Models\Message;
use Chat\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\BaseDatabaseTestCase;

class ConversationTest extends BaseDatabaseTestCase
{
    /** @test */
    public function has_many_messages(): void
    {
        $conversation = $this->createConversation();
        $this->assertInstanceOf(HasMany::class, $conversation->messages());
        $this->assertInstanceOf(Message::class, $conversation->messages()->getRelated());
    }

    /** @test */
    public function has_many_participants(): void
    {
        $conversation = $this->createConversation();
        $this->assertInstanceOf(BelongsToMany::class, $conversation->participants());
        $this->assertInstanceOf(User::class, $conversation->participants()->getRelated());
    }

    /** @test */
    public function doesnt_throw_if_user_is_participant(): void
    {
        $conversation = $this->createConversation();
        $conversation->participants()->save($user = $this->createUser());
        $conversation->assertUserIsParticipant($user);
        $this->assertTrue(true);
    }

    /** @test */
    public function throws_if_user_is_not_participant(): void
    {
        $conversation = $this->createConversation();
        $this->expectException(\Chat\Exceptions\ForbiddenConversationException::class);
        $this->expectExceptionCode(\Chat\Exceptions\ForbiddenConversationException::E_CODE);
        $conversation->assertUserIsParticipant($this->createUser());
    }

    /** @test */
    public function updated_on_new_message(): void
    {
        $conversation = $this->createConversation();
        $updatedAt    = $conversation->updated_at->copy();
        sleep(1);
        $this->createMessage()->conversation()->associate($conversation)->save();
        $conversation->refresh();
        $this->assertNotEquals($updatedAt, $conversation->updated_at);
    }
}
