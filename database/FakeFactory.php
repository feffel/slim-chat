<?php
declare(strict_types=1);

use Illuminate\Database\Eloquent\Factory;

class FakeFactory extends Factory
{
    private const FACTORIES__PATH = ROOT.'database/factories/';

    protected Factory $factory;

    public function __construct()
    {
        parent::__construct(Faker\Factory::create());
        $factories = glob(static::FACTORIES__PATH.'*.php');
        $this->define(\Chat\Models\Conversation::class, fn() => []);
        foreach ($factories as $factory) {
            /** @noinspection PhpIncludeInspection */
            require $factory;
        }
    }
}
