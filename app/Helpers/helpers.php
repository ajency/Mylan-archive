<?php
 
/**
 * 
 * @param type $type_id
 * @return string
 */

//patient status array
// $patientStatus = ['created'=>'created','active'=>'Active','suspended'=>'Suspended'];
// $userTypes = ['mylan_admin'=>'Mylan Admin','internal_user'=>'Internal User','pateints'=>'Patient'];
function getRoles()
{
	$roles = ['edit'=>'Edit','view'=>'View'];
	return $roles;
}


//constatnts
define("SETUP_LIMIT", 5);

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

function randomPassword() {
	$chars = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	$password = substr( str_shuffle( $chars ), 0, 6 );
	return $password;
}

function hasMylanPermission($userPermission)
{  
    $userId =  Auth::user()->id;
    $user = App\User::find($userId); 
    $hasAccess = $user->mylan_access; 
    
 
    $flag = false;
    
    $permissions =[];
 	$userAccess = [];
 	
    if($hasAccess=='no')      //GET ROLES ONLY FOR THE PROJECT
    {
        $userAccess = $user->access()->where(['object_type'=>'mylan', 'object_id'=>0])->whereIn('access_type',$userPermission)->get()->toArray();
    	
    	if(!empty($userAccess))
	    {
	        $flag = true;
	    }
    }
    else
    {
    	$flag = true;
    }
    
    
    

     
    return $flag;
 
}


