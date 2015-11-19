<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HospitalUser extends Model
{
    public function hospital() {
        return $this->hasMany( 'App\Hospital' , 'id' );
    }

    public function department() {
        return $this->hasMany( 'App\Department' , 'id' );
    }
}
