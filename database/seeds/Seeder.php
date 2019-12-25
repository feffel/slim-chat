<?php

use Chat\Models\Conversation;
use Chat\Models\Message;
use Chat\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Phinx\Seed\AbstractSeed;

require_once __DIR__.'/../FakeFactory.php';

class Seeder extends AbstractSeed
{
    protected FakeFactory $factory;

    protected const USERS_COUNT        = 10;
    protected const CONVERSATION_COUNT = 10;

    protected function init()
    {
        parent::init();
        $this->factory = new FakeFactory();
    }

    public function run()
    {
        /** @var Collection $users */
        $users         = $this->factory->of(User::class)->times(self::USERS_COUNT)->create();
        /** @var Collection $conversations */
        $conversations = $this->factory->of(Conversation::class)->times(self::CONVERSATION_COUNT)->create();
        $conversations->map(
            function (Conversation $conversation) use ($users) {
                $conversationUsers = $users->random(2);
                $conversation->participants()->saveMany($conversationUsers);
                $this->factory->of(Message::class)->times(random_int(0, 15))->create(
                    ['conversation_id' => $conversation->id, 'author_id' => $conversationUsers->random()->id]
                );
            }
        );
    }
}
