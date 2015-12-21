<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    protected $table = 'user_access';

    public function user() {
        return $this->hasOne( 'App/User');
    }
}
