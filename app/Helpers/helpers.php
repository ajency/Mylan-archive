<?php
 
/**
 * 
 * @param type $type_id
 * @return string
 */
function getUserApiKey( $userId ) {

    $key = App\ApiKeys::where('user_id',$userId)->get()->pluck('key');
         
    return $key[0];
}    

