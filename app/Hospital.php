<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    public function projects() {
        return $this->hasMany( 'App\Projects' );
    }

    public function users() {
        return $this->hasMany( 'App\User' );
    }

    // public function toArray()
    // {
    // 	$data = parent::toArray();
    // }
}
