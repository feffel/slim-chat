<?php
declare(strict_types=1);

namespace Tests\Unit\Repository;

use Chat\Repositories\UserRepository;
use Tests\BaseDatabaseTestCase;

class UserRepositoryTest extends BaseDatabaseTestCase
{
    /** @test */
    public function creates_new_user(): void
    {
        $userRepository = new UserRepository();
        $username       = 'felfel';
        $user           = $userRepository->create($username);
        $this->assertNotNull($user->id);
        $this->assertEquals($username, $user->username);
    }
}
