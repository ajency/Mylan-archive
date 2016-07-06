@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php
	$previousProject = explode("/",$_SERVER['REQUEST_URI']);
	$projUrl = $previousProject[1]."/".$previousProject[2];
?>
   <p>
      <ul class="breadcrumb">
         <!--li>
            <a href="#" class="active" > Home</a>
         </li-->
         <li>
            <a href="{{ url() }}/<?php echo $projUrl; ?>" class="active"> User</a>
         </li>
         <li>
            <a href="#"> Edit Project User</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Edit Project User</span></h3>
   <p>(Update User under {{ $hospital['name'] }})</p>
</div>
@include('admin.flashmessage')
<form onsubmit="return validateHospitalUser();" class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url($hospital['url_slug'].'/users/'.$user['id']) }}" data-parsley-validate>
<div class="grid simple">
   <div class="grid-body">
      <form class="form-no-horizontal-spacing" id="form-condensed">
         <div class="row">
            <div class="col-sm-3">
               <div class="form-row form-group">
                  <label>Name <span class="text-primary">*</span></label>
                  <input name="name" id="name" type="text"  value="{{ $user['name'] }}" class="form-control" data-parsley-required>
               </div>
            </div>
            <div class="col-sm-3">
               <div class="form-row">
                  <label>Email <span class="text-primary">*</span></label>
                  <input name="email" id="email" type="email"  value="{{ $user['email'] }}" objectId="{{ $user['id'] }}" objectType="project" class="authUserEmail form-control" data-parsley-required data-parsley-type="email">
               </div>
            </div>
            <div class="col-sm-3">
               <div class="form-row">
                  <label>Phone Number <span class="text-primary">*</span></label>
                  <input name="phone" id="phone" type="text"  value="{{ $user['phone'] }}"  class="form-control" data-parsley-required>
               </div>
            </div>
              
         </div>
          <hr>
         <h4 class="no-margin"><span class="semi-bold">Access</span> Configuration 
         <div class="checkbox check-primary custom-checkbox pull-right">
                  <input id="checkbox6" type="checkbox" name="has_all_access" value="yes" {{ ($user['has_all_access']=='yes') ? 'checked':''}} >
                  <label for="checkbox6"><h4 class="no-margin">Access to all Projects<small> (This would automatically give access to future projects.)</small></h4></label>
               </div>
         </h4>
         <hr>

          <div class="row">
               <div class="col-md-4 text-center"><h4 class="user-head">Projects</h4></div>
               <div class="col-md-4 text-right">
                  
                  <h4 class="user-sub-head">Access (Individual)</h4>
               </div>

         </div>

         <div class="row">
            <div class="allignHR">
               <hr>
            </div>
         </div>

             <div class="user-description-box allProjectsAccess">
              
            <div class="add_user_associates {{ ($user['has_all_access']=='yes') ? 'hidden':''}}">
         
            <!-- <br> -->
            <?php
               $i=0;
            ?>
            @foreach($userAccess as $value)
            <div class="row project_users">
               <div class="col-md-4">
               <input type="hidden" name="user_access[]" value="{{ $value['id'] }}">
                  <select name="projects[]" id="project" class="select2 form-control"  >
                     <option value="">Select Hospital</option>
                     @foreach($projects as $project)
                     <option {{ ($project['id']==$value['object_id']) ? 'selected':''}} value="{{ $project['id'] }}">{{ $project['name'] }}</option>
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
                @if(hasHospitalPermission($hospital['url_slug'],['edit']))
               <div class="col-md-4 text-center">
                  <a class="deleteUserProjectAccess" data-id="{{ $value['id'] }}"> Delete </a>
               </div>

               @endif
            </div>
            <!-- <hr> -->
            <?php
               $i++;
            ?>
            @endforeach

            @if(hasHospitalPermission($hospital['url_slug'],['edit']))
            <div class="row project_users add-user-container">
               <div class="col-md-4">
                  <input type="hidden" name="user_access[]" value="">
                  <select name="projects[]" id="projects" class="select2 form-control"  >
                     <option value="">Select Project</option>
                     @foreach($projects as $project)
                     <option   value="{{ $project['id'] }}">{{ $project['name'] }}</option>
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
                  <a class="deleteUserProjectAccess hidden" data-id="0"> Delete </a>
                  <button type="button"  object-type="Project" object-id="{{ $project['id']}}" class="btn btn-link text-success pullleft  add-project-user"><i class="fa fa-plus"></i> Add Project</button>
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