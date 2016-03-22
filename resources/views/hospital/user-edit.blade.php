@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > Home</a>
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
   <p>(Update a user under {{ $hospital['name'] }})</p>
</div>
<form onsubmit="return validateHospitalUser();" class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url($hospital['url_slug'].'/users/'.$user['id']) }}" data-parsley-validate>
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
                  <input name="email" id="email" type="email"  value="{{ $user['email'] }}" objectId="{{ $user['id'] }}" objectType="project" class="authUserEmail form-control" data-parsley-required data-parsley-type="email">
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
             <div class="user-description-box allProjectsAccess">
            <div class="row">
               <div class="col-md-3">Projects</div>
               <div class="col-md-3">
                  <div class="checkbox check-primary">
                  <input id="checkbox6" type="checkbox" name="has_all_access" value="yes" {{ ($user['has_all_access']=='yes') ? 'checked':''}} >
                  <label for="checkbox6">Access to all Projects<small> (This would automatically give access to future projects.)</small></label>
               </div>
                  Access  (Individual)
               </div>

            </div>
            <div class="add_user_associates {{ ($user['has_all_access']=='yes') ? 'hidden':''}}">
            <br>
            <?php
               $i=0;
            ?>
            @foreach($userAccess as $value)
            <div class="row project_users">
               <div class="col-md-3">
               <input type="hidden" name="user_access[]" value="{{ $value['id'] }}">
                  <select name="projects[]" id="project" class="select2 form-control"  >
                     <option value="">Select Hospital</option>
                     @foreach($projects as $project)
                     <option {{ ($project['id']==$value['object_id']) ? 'selected':''}} value="{{ $project['id'] }}">{{ $project['name'] }}</option>
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
                @if(hasHospitalPermission($hospital['url_slug'],['edit']))
               <div class="col-md-3">
                  <a class="deleteUserProjectAccess" data-id="{{ $value['id'] }}"> delete </a>
               </div>
               @endif
            </div>
            <hr>
            <?php
               $i++;
            ?>
            @endforeach

            @if(hasHospitalPermission($hospital['url_slug'],['edit']))
            <div class="row project_users">
               <div class="col-md-3">
                  <input type="hidden" name="user_access[]" value="">
                  <select name="projects[]" id="projects" class="select2 form-control"  >
                     <option value="">Select Project</option>
                     @foreach($projects as $project)
                     <option   value="{{ $project['id'] }}">{{ $project['name'] }}</option>
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
                  <a class="deleteUserProjectAccess hidden" data-id="0"> delete </a>
               </div>
            </div>
            <hr>
            
            <div class="row">
               <div class="col-md-3">
                  <input type="hidden" name="counter" value="{{ $i }}">
                  <button type="button"  object-type="Project" object-id="{{ $project['id']}}" class="btn btn-link text-success pullleft  add-project-user"><i class="fa fa-plus"></i> Add Project</button>
               </div>
               <div class="col-md-3">
                 
               </div>
               <div class="col-md-3">
                  
               </div>
               <div class="col-md-3">
                 
               </div>
            </div>
            @endif
         </div>
         </div>
         <div class="form-actions">
            <div class="text-right">
               <input type="hidden" name="_method" value="PUT">
               <input type="hidden" value="{{ csrf_token()}}" name="_token"/>

               @if(hasHospitalPermission($hospital['url_slug'],['edit']))
               <button  class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
               @endif

               <a href="{{ url($hospital['url_slug'].'/users') }}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
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