<?php
declare(strict_types=1);

use Chat\Models\PrivateConversation;
use Tests\BaseDatabaseTestCase;

class PrivateConversationTest extends BaseDatabaseTestCase
{
    /** @test */
    public function get_previous_private_conversation(): void
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $conversation = $this->createConversation();
        $conversation->participants()->saveMany([$userA, $userB]);
        $this->assertEquals(PrivateConversation::getByParticipants($userA, $userB)->id, $conversation->id);
    }

    /** @test */
    public function create_new_private_conversation(): void
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $conversation = PrivateConversation::getByParticipants($userA, $userB);
        $this->assertNotNull($conversation->id);
        $this->assertCount(2, $conversation->participants);
        $this->assertTrue($conversation->participants->contains($userA));
        $this->assertTrue($conversation->participants->contains($userB));
    }
}
