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
 * @property Carbon  updated_at
 * @property Carbon  created_at
 *
 * @package Chat\Models
 */
class User extends Model
{
    public function messages()
    {
        return $this->hasMany();
    }

    public function conversations()
    {
        return $this->belongsToMany();
    }
}
