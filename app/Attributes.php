<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{	
	protected $table = "attributes";	
    protected $fillable = ['label', 'control_type', 'object_type', 'object_id', 'values','validate'];
     
    public function object() {
        return $this->morphTo();
    }
   
    public function getObjectTypeAttribute($value){
        return 'App\'' . $value;
    }
}
