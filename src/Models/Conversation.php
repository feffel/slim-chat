<?php
declare(strict_types=1);

namespace Chat\Models;

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
        return $this->hasMany();
    }

    public function participants()
    {
        return $this->belongsToMany();
    }

    public function assertUserIsParticipant(User $user): void
    {
    }
}
