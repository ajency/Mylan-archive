<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
 	public function attributes() {
        return $this->morphMany( 'App\Attributes', 'object' );
    }
}
