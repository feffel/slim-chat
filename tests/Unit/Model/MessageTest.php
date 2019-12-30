<?php
declare(strict_types=1);

namespace Tests\Unit\Model;

use Chat\Models\Conversation;
use Chat\Models\Message;
use Chat\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\BaseDatabaseTestCase;

class MessageTest extends BaseDatabaseTestCase
{
    /** @test */
    public function belongs_to_one_author(): void
    {
        $message = $this->createMessage();
        $this->assertInstanceOf(BelongsTo::class, $message->author());
        $this->assertInstanceOf(User::class, $message->author()->getRelated());
    }

    /** @test */
    public function belongs_to_one_conversation(): void
    {
        $message = $this->createMessage();
        $this->assertInstanceOf(BelongsTo::class, $message->conversation());
        $this->assertInstanceOf(Conversation::class, $message->conversation()->getRelated());
    }

    /** @test */
    public function doesnt_throw_if_new_message(): void
    {
        $message = new Message(['content' => 'hello world']);
        $message->author()->associate($this->createUser());
        $message->conversation()->associate($this->createConversation());
        $message->assertNotSent();
        $this->assertTrue(true);
    }

    /** @test */
    public function throws_if_previously_sent(): void
    {
        $message = $this->createMessage();
        $this->expectException(\Chat\Exceptions\MessageAlreadySentException::class);
        $this->expectExceptionCode(\Chat\Exceptions\MessageAlreadySentException::E_CODE);
        $message->assertNotSent();
    }
}
