<?php
declare(strict_types=1);

namespace Chat\Repositories;

use Chat\Models\User;

class UserRepository extends BaseRepository
{
    protected function getAliases(): array
    {
        return [
            User::TABLE => 'u',
        ];
    }

    protected function getModel(): string
    {
        return User::class;
    }

    public function create(string $username): User
    {
        $user           = new User();
        $user->username = $username;
        $user->save();

        return $user;
    }
}
