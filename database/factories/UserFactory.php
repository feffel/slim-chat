<?php

use Chat\Models\User;
use Faker\Generator as Fake;

$this->define(
    User::class,
    fn(Fake $faker) => [
        'username' => $faker->userName,
    ]
);
