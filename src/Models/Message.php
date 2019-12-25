<?php
declare(strict_types=1);

namespace Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Message
 *
 * @property int          id
 * @property string       content
 * @property User         author       Author of this message
 * @property Conversation conversation Conversation containing this message
 * @property Carbon  updated_at
 * @property Carbon  created_at
 *
 * @package Chat\Models
 */
class Message extends Model
{
    public function author()
    {
        return $this->belongsTo();
    }

    public function conversation()
    {
        return $this->belongsTo();
    }
}
