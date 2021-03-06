<?php
declare(strict_types=1);

namespace Tests;

use Chat\Models\Conversation;
use Chat\Models\Message;
use Chat\Models\User;
use FakeFactory;
use Illuminate\Database\DatabaseManager;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class BaseDatabaseTestCase extends BaseTestCase
{
    protected DatabaseManager $db;

    protected FakeFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        require_once ROOT.'database/FakeFactory.php';
        $this->factory = new FakeFactory();
        $this->runMigration();
        $this->db = $this->app->getContainer()->get('db')->getDatabaseManager();
    }

    protected function tearDown(): void
    {
        $this->rollbackMigration();
        parent::tearDown();
    }

    protected function runMigration(): void
    {
        $app = new PhinxApplication();
        $app->doRun(new StringInput('migrate'), new NullOutput());
    }

    protected function rollbackMigration(): void
    {
        $app = new PhinxApplication();
        $app->doRun(new StringInput('rollback -t 0 -f'), new NullOutput());
    }

    protected function assertDatabaseHas($table, array $data): void
    {
        $builder = $this->db->table($table);
        foreach ($data as $filed => $value) {
            $builder->where($filed, $value);
        }
        $this->assertTrue(
            $builder->count() > 0,
            sprintf(
                "$table table does not have %s under the column %s",
                $key = array_keys($data)[0],
                $data[$key]
            )
        );
    }

    protected function assertDatabaseDoesNotHave($table, array $data): void
    {
        $builder = $this->db->table($table);
        foreach ($data as $filed => $value) {
            $builder->where($filed, $value);
        }
        $this->assertFalse($builder->count() > 0, 'Database has unwanted records in table '.$table);
    }

    protected function createUser(): User
    {
        return $this->factory->of(User::class)->create();
    }

    protected function createConversation(): Conversation
    {
        return $this->factory->of(Conversation::class)->create();
    }

    protected function createMessage(): Message
    {
        return $this->factory->of(Message::class)->create();
    }
}
