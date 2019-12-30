<?php
declare(strict_types=1);

namespace Chat\Models;

use Chat\Exceptions\MessageAlreadySentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Message
 *
 * @property int          id
 * @property string       content
 * @property User         author       Author of this message
 * @property Conversation conversation Conversation containing this message
 * @property Carbon       updated_at
 * @property Carbon       created_at
 *
 * @package Chat\Models
 */
class Message extends Model
{
    public const TABLE = 'messages';
    protected $table      = self::TABLE;
    protected $dateFormat = 'U';

    protected $touches = ['conversation'];

    protected $fillable = ['content'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function assertNotSent(): void
    {
        throw_if($this->exists, new MessageAlreadySentException("Message {$this->id} previously sent"));
    }
}
