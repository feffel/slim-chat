<?php
declare(strict_types=1);

namespace Chat\Transformers;

use Chat\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return [
            'id'        => (int)$user->id,
            'username'  => $user->username,
        ];
    }
}
