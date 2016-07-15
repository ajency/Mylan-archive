@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"> Home</a>
         </li>
         <li>
            <a href="{{ url( 'admin/users/' ) }}"> User</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active"> Edit Hospital User</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Edit Hospital User</span></h3>
   <p>(Update User under Mylan)</p>
</div>
@include('admin.flashmessage')
<form onsubmit="return validateHospitalUser();" class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url('admin/users/'.$user['id']) }}" data-parsley-validate>
<div class="grid simple">
   <div class="grid-body">
      <form class="form-no-horizontal-spacing" id="form-condensed">
         <div class="row">
            <div class="col-sm-3">
               <div class="form-row form-group">
                  <label>Name</label>
                  <input name="name" id="name" type="text"  value="{{ $user['name'] }}" class="form-control" data-parsley-required>
               </div>
            </div>
            <div class="col-sm-3">
               <div class="form-row">
                  <label>Email</label>
                  <input name="email" id="email" type="email"  value="{{ $user['email'] }}" objectId="{{ $user['id'] }}" objectType="hospital" class="authUserEmail form-control" data-parsley-required data-parsley-type="email" readonly="true">
               </div>
            </div>
            <div class="col-sm-3">
               <div class="form-row">
                  <label>Phone Number</label>
                  <input name="phone" id="phone" type="text"  value="{{ $user['phone'] }}"  class="form-control" data-parsley-required>
               </div>
            </div>
              
         </div>
          <hr>
			 <h4 class="no-margin">Reset <span class="semi-bold">Password</span></h4>	
			 <div class="row">
				 <div class="col-sm-4 text-left passGenerate">
					<button type="button" class="btn btn-primary generate_new_password" object-id="{{ $user['id'] }}" identify-user ="hospital user" >Generate New Password <i class="fa"></i></button> 
					<span id="generatePassword" class="generatePassword"></span>
				 </div>
			 </div>
          <hr>
         <h4 class="no-margin"><span class="semi-bold">Hospital Access</span> Configuration
                  
         <div class="checkbox check-primary custom-checkbox pull-right">
            <input id="has_all_access" type="checkbox" name="has_all_access" onclick="validateCheck();" value="yes" {{ ($user['has_all_access']=='yes') ? 'checked':''}} >
            <label for="has_all_access"><h4 class="no-margin">Access to all Hospitals<small> (This would automatically give access to future Hospitals.)</small></h4></label>
         </div>

         </h4>
         <hr>
         <!-- <div class="user-description-box">
            <div class="row">
               <div class="col-md-3">Mylan</div>
               <div class="col-md-3">
                  <div class="checkbox check-primary">

                  <input id="had_mylan_access" type="checkbox" name="had_mylan_access" value="yes"  {{ ($user['mylan_access']=='yes') ? 'checked':''}}>
                  <label for="had_mylan_access">Access to all Mylan </label>
               </div>
                  Access  (Individual)
               </div>
            </div>
            <br>
            <div class="row hospital_users">
               <div class="col-md-3">
                   
               </div>
               <div class="col-md-3">
                  <div class="radio radio-primary">
                     <input type="hidden" name="mylan_access_id" value="{{ $mylanUserAccess['id'] }}">
                     <input id="mylan_access_view" type="radio" name="mylan_access" value="view" {{ ('view'==$mylanUserAccess['access_type']) ? 'checked':''}} >
                     <label for="mylan_access_view">View</label>
                     <input id="mylan_access_edit" type="radio" name="mylan_access" value="edit" {{ ('edit'==$mylanUserAccess['access_type']) ? 'checked':''}}>
                     <label for="mylan_access_edit">Edit</label>
                  </div>
               </div>
            </div>

         </div>
         <br> -->
          <div class="row">
               <div class="col-md-4 text-center"><h4 class="user-head">Hospital</h4></div>
               <div class="col-md-4 text-right">
          
               <h4 class="user-sub-head">Access (Permissions)</h4>
               </div>
         </div>
         <div class="row">
            <div class="allignHR">
               <hr>
            </div>
         </div>

             <div class="user-description-box allHospitalsAccess">
           
         <div class="add_user_associates {{ ($user['has_all_access']=='yes') ? 'hidden':''}}">
            
            <?php
               $i=0;
            ?>
            @foreach($userAccess as $value)
            <div class="row hospital_users">
               <div class="col-md-4">
               <input type="hidden" name="user_access[]" value="{{ $value['id'] }}">
                  <select name="hospital[]" id="hospital" class="select2 form-control hasToggle"  data-parsley-required="true" >
                     <option value="">Select Hospital</option>
                     @foreach($hospitals as $hospital)
                     <option {{ ($hospital['id']==$value['object_id']) ? 'selected':''}} value="{{ $hospital['id'] }}">{{ $hospital['name'] }}</option>
                     @endforeach
 
                  </select>
               </div>
               <div class="col-md-4">
                  <div class="radio radio-primary text-right">
                     <input id="access_view_{{ $i }}" type="radio" name="access_{{ $i }}" value="view" {{ ('view'==$value['access_type']) ? 'checked':''}} >
                     <label for="access_view_{{ $i }}">View</label>
                     <input id="access_edit_{{ $i }}" type="radio" name="access_{{ $i }}" value="edit" {{ ('edit'==$value['access_type']) ? 'checked':''}}>
                     <label for="access_edit_{{ $i }}">Edit</label>
                  </div>
               </div>
			   
               <div class="col-md-4 text-center">
                  <a class="deleteUserHospitalAccess first-hospitals" data-id="{{ $value['id'] }}"> Delete </a>
               </div>
               
            </div>
            <!-- <hr> -->
            <?php
               $i++;
            ?>
            @endforeach
            <div class="row hospital_users add-user-container">
               <div class="col-md-4">
                  <input type="hidden" name="user_access[]" value="">
                  <select name="hospital[]" id="hospital" class="select2 form-control toggleRequired" data-parsley-required="true" >
                     <option value="">Select Hospital</option>
                     @foreach($hospitals as $hospital)
                     <option   value="{{ $hospital['id'] }}">{{ $hospital['name'] }}</option>
                     @endforeach
 
                  </select>
               </div>
               <div class="col-md-4">
                  <div class="radio radio-primary text-right">
                     <input id="access_view_{{ $i }}" type="radio" name="access_{{ $i }}" value="view" checked="checked">
                     <label for="access_view_{{ $i }}">View</label>
                     <input id="access_edit_{{ $i }}" type="radio" name="access_{{ $i }}" value="edit">
                     <label for="access_edit_{{ $i }}">Edit</label>
                  </div>
               </div>
               <div class="col-md-4 text-center">
                  <a class="deleteUserHospitalAccess hidden" data-id="0"> Delete </a>
                  <button type="button"  object-type="Hospital" class="btn btn-link text-success pullleft add-hospital-user"><i class="fa fa-plus"></i> Add Hospital</button>
               </div>
            </div>
            <!-- <hr> -->
            <div class="row">
               <div class="col-md-3">
                  <input type="hidden" name="counter" value="{{ $i }}">
                  
               </div>
               <div class="col-md-3">
                 
               </div>
               <div class="col-md-3">
                  
               </div>
               <div class="col-md-3">
                 
               </div>
            </div>
            </div>
         </div>
         <div class="form-actions">
            <div class="text-right">
               <input type="hidden" name="_method" value="PUT">
               <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
               <button  class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
               <a href="{{'/admin/users'}}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
            </div>
         </div>
      </form>
   </div>
</div>
</form>
<script>
   var HOSPITAL_ID = 0;
   var user_access = "{{ $user['has_all_access'] }}";
   function validateCheck(){
		if( $("#has_all_access").is(":checked")){
			$("[id = 'hospital']").removeAttr("data-parsley-required");
		}else{
			$("[id = 'hospital']").attr("data-parsley-required","true");
			if($(".hasToggle")[0]){	
				$(".toggleRequired").removeAttr("data-parsley-required");
			}	
		}
	}
	
	function defaultCheck(){
		if(user_access == "yes"){
			$("[id = 'hospital']").removeAttr("data-parsley-required");
		}else{
			$("[id = 'hospital']").attr("data-parsley-required","true");
			if($(".hasToggle")[0]){
				$(".toggleRequired").removeAttr("data-parsley-required");
			}	
		}
	}
	
	$(document).ready(function(e){
		defaultCheck();
	});
	
</script>
@endsection