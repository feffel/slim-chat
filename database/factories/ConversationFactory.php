<?php

use Chat\Models\Conversation;
use Faker\Generator as Fake;

$this->define(
    Conversation::class,
    fn(Fake $faker) => []
);

