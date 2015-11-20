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

function getPassword($referenceCode , $password)
{
	$referenceCodePart1 = mb_substr($referenceCode, 0, 4);
	$referenceCodePart2 = mb_substr($referenceCode, 4, 7);

	$newPassword =  $referenceCodePart2.$password.$referenceCodePart1;

	return $newPassword;
}


