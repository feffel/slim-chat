<?php
declare(strict_types=1);

namespace Chat\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class User
 *
 * @property int        id
 * @property string     username
 * @property Collection messages      Messages sent by this user
 * @property Collection conversations Conversations accessible by this user
 * @property Carbon     updated_at
 * @property Carbon     created_at
 *
 * @package Chat\Models
 */
class User extends Model
{
    public const TABLE = 'users';
    protected $table      = self::TABLE;
    protected $dateFormat = 'U';

    public function messages()
    {
        return $this->hasMany(Message::class, 'author_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    public function sendMessage(Message $message, Conversation $conversation): void
    {
        $message->assertNotSent();
        $conversation->assertUserIsParticipant($this);
        $message->author()->associate($this);
        $message->conversation()->associate($conversation);
        $message->save();
    }
}
