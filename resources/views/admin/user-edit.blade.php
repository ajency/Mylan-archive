@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
         <li>
            <a href="#"> User</a>
         </li>
         <li>
            <a href="#"> Edit User</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Edit User</span></h3>
   <p>(Update a Hospital under Mylan)</p>
</div>
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
                  <input name="email" id="email" type="email"  value="{{ $user['email'] }}" class="form-control" data-parsley-required data-parsley-type="email">
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
         <h4 class="no-margin"><span class="semi-bold">Access</span> Configuration</h4>
         <br>
         <div class="user-description-box">
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
         <br>
             <div class="user-description-box">
            <div class="row">
               <div class="col-md-3">Hospital</div>
               <div class="col-md-3">
                  <div class="checkbox check-primary">
                  <input id="checkbox6" type="checkbox" name="has_access" value="yes" {{ ($user['project_access']=='yes') ? 'checked':''}} >
                  <label for="checkbox6">Access to all Projects<small> (This would automatically give access to future projects.)</small></label>
               </div>
                  Access  (Individual)
               </div>

            </div>
            <br>
            <?php
               $i=0;
            ?>
            @foreach($userAccess as $value)
            <div class="row hospital_users">
               <div class="col-md-3">
               <input type="hidden" name="user_access[]" value="{{ $value['id'] }}">
                  <select name="hospital[]" id="hospital" class="select2 form-control"  >
                     <option value="">Select Hospital</option>
                     @foreach($hospitals as $hospital)
                     <option {{ ($hospital['id']==$value['object_id']) ? 'selected':''}} value="{{ $hospital['id'] }}">{{ $hospital['name'] }}</option>
                     @endforeach
 
                  </select>
               </div>
               <div class="col-md-3">
                  <div class="radio radio-primary">
                     <input id="access_view_{{ $i }}" type="radio" name="access_{{ $i }}" value="view" {{ ('view'==$value['access_type']) ? 'checked':''}} >
                     <label for="access_view_{{ $i }}">View</label>
                     <input id="access_edit_{{ $i }}" type="radio" name="access_{{ $i }}" value="edit" {{ ('edit'==$value['access_type']) ? 'checked':''}}>
                     <label for="access_edit_{{ $i }}">Edit</label>
                  </div>
               </div>
               <div class="col-md-3">
                  <a class="deleteUserHospitalAccess" data-id="{{ $value['id'] }}"> delete </a>
               </div>
            </div>
            <hr>
            <?php
               $i++;
            ?>
            @endforeach
            <div class="row hospital_users">
               <div class="col-md-3">
                  <input type="hidden" name="user_access[]" value="">
                  <select name="hospital[]" id="hospital" class="select2 form-control"  >
                     <option value="">Select Hospital</option>
                     @foreach($hospitals as $hospital)
                     <option   value="{{ $hospital['id'] }}">{{ $hospital['name'] }}</option>
                     @endforeach
 
                  </select>
               </div>
               <div class="col-md-3">
                  <div class="radio radio-primary">
                     <input id="access_view_{{ $i }}" type="radio" name="access_{{ $i }}" value="view" checked="checked">
                     <label for="access_view_{{ $i }}">View</label>
                     <input id="access_edit_{{ $i }}" type="radio" name="access_{{ $i }}" value="edit">
                     <label for="access_edit_{{ $i }}">Edit</label>
                  </div>
               </div>
               <div class="col-md-3">
                  <a class="deleteUserHospitalAccess hidden" data-id="0"> delete </a>
               </div>
            </div>
            <hr>
            <div class="row">
               <div class="col-md-3">
                  <input type="hidden" name="counter" value="{{ $i }}">
                  <button type="button"  object-type="Hospital" class="btn btn-link text-success pullleft add-hospital-user"><i class="fa fa-plus"></i> Add Hospital</button>
               </div>
               <div class="col-md-3">
                 
               </div>
               <div class="col-md-3">
                  
               </div>
               <div class="col-md-3">
                 
               </div>
            </div>
         </div>
         <div class="form-actions">
            <div class="text-right">
               <input type="hidden" name="_method" value="PUT">
               <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
               <button  class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
               <button class="btn btn-danger btn-cons-md" type="submit"><i class="icon-ok"></i> Save and Add Another</button>
               <button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button>
            </div>
         </div>
      </form>
   </div>
</div>
</form>
<script>
   var HOSPITAL_ID = 0;
</script>
@endsection