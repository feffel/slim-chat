<?php

use Chat\Models\Conversation;
use Chat\Models\Message;
use Chat\Models\User;
use Faker\Generator as Fake;

$this->define(
    Message::class,
    fn(Fake $faker) => [
        'content'         => $faker->sentences(random_int(1, 3), true),
        'conversation_id' => fn() => $this->of(Conversation::class)->create()->id,
        'author_id'       => fn() => $this->of(User::class)->create()->id,
    ]
);

