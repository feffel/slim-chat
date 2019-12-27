<?php
declare(strict_types=1);

namespace Chat\Models;

use Chat\Exceptions\ForbiddenConversationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Conversation
 *
 * @property int        id
 * @property Collection messages       Messages in this conversation
 * @property Collection participants   Participants in this conversation
 * @property Carbon     updated_at
 * @property Carbon     created_at
 *
 * @package Chat\Models
 */
class Conversation extends Model
{

    public function __construct(array $attributes = []) { parent::__construct($attributes); }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants', 'conversation_id');
    }

    public function assertUserIsParticipant(User $user): void
    {
        throw_unless(
            $this->participants->contains($user),
            new ForbiddenConversationException(
                "User {$user->id} is not a participant in this conversation {$this->id}"
            )
        );
    }
}
