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


function convertStonePoundsToKgs($stones,$pounds)
{
    $value1 = $pounds / 2.2; 

    $pounds = $stones * 14; 

    $value2 = $pounds / 2.2; 

    $result = $value1 + $value2;     

    return round($result);   
}

function getInputValues($values,$withLabel=true)
{
    /*********
    $array['kgs']=50
    $array=['ST'=>50,'lbs'=>30];

    /******/
    if(count($values)==1)
    {
        $result = current($values).' ';
        $result .= ($withLabel)? key($values) :'';
    }
    else
    {
        if(isset($values['ST']) && isset($values['lbs']))
        {
            $result = convertStonePoundsToKgs($values['ST'],$values['lbs']) ;
            $result .= ($withLabel)? ' Kgs' :'';
        }
        else
        {
            $result = current($values).' ';
            $result .= ($withLabel)? key($values) :'';
        }
        
    }

    return $result;
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
    $userType = $user->type; 
    $hasAccess = $user->mylan_access; 
    
    $flag = false;
    
    $permissions =[];
 	$userAccess = [];
 	
    if($userType=='mylan_admin')
    {
        if($hasAccess=='no')      //GET ROLES ONLY FOR THE PROJECT
        {
            $userAccess = $user->access()->where(['object_type'=>'mylan', 'object_id'=>0])->whereIn('access_type',$userPermission)->get()->toArray();
        	
        	if(!empty($userAccess))
    	        $flag = true;
        }
        else
        	$flag = true;
    }
    
    return $flag;
}

function hasHospitalPermission($hospitalSlug,$userPermission)
{  
    $userId =  Auth::user()->id;
    $user = App\User::find($userId); 
    $hasAccess = $user->project_access; 
    $userType =  $user->type; 

    
    $flag = false;
    
    $permissions =[];
    $userAccess = [];

    $hospital = App\Hospital::where('url_slug',$hospitalSlug)->first()->toArray(); 
    

    if($userType=='mylan_admin' || $userType=='hospital_user')
    {
        if($hasAccess=='no')      //GET ROLES ONLY FOR THE PROJECT
        {
            $userAccess = $user->access()->where(['object_type'=>'hospital', 'object_id'=>$hospital['id']])->whereIn('access_type',$userPermission)->get()->toArray();
            
            if(!empty($userAccess))
                $flag = true;
        }
        else
            $flag = true;
    }
    
    return $flag;
}


function hospitalImageExist($hospital)
{ 
    $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];
    $filepath = public_path(). "/mylan/hospitals/".$hospital['logo']; 

    if($hospital['logo']!='' && file_exists($filepath))
        $logo = '<img src="'.$logoUrl.'" class="logo" alt=""  data-src="'.$logoUrl.'" data-src-retina="'.$logoUrl.'" width="auto" height="40"/>';
    else
        $logo = '<h3 class="inline hospital-name">'.$hospital['name'].' <span class="text-muted"> | </span></h3>';

    echo $logo;  
}

function verifyProjectSlug($hospitalSlug ,$projectSlug)
{
    $hospital = App\Hospital::where('url_slug',$hospitalSlug)->first();
    if(empty($hospital))
        abort(404); 

    $project = App\Projects::where('project_slug',$projectSlug)->where('hospital_id',$hospital['id'])->first(); 
    if(empty($project))
        abort(404);

    $data['hospital']=$hospital;
    $data['project']=$project;

    return $data;
}

