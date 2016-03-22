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
            <a href="#"> Add User</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Add User</span></h3>
   <p>(Create a Hospital under {{ $hospital['name'] }})</p>
</div>
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url($hospital['url_slug'].'/users') }}" data-parsley-validate>
<div class="grid simple">
   <div class="grid-body">
      <form class="form-no-horizontal-spacing" id="form-condensed">
         <div class="row">
            <div class="col-sm-3">
               <div class="form-row form-group">
                  <label>Name</label>
                  <input name="name" id="name" type="text"  class="form-control" data-parsley-required>
               </div>
            </div>
            <div class="col-sm-3">
               <div class="form-row">
                  <label>Email</label>
                  <input name="email" id="email" type="email"  objectId="0" objectType="project" class="authUserEmail form-control" data-parsley-required data-parsley-type="email">
               </div>
            </div>
            <div class="col-sm-3">
               <div class="form-row">
                  <label>Phone Number</label>
                  <input name="phone" id="phone" type="text"  class="form-control" data-parsley-required>
               </div>
            </div>
              
         </div>
          <hr>
         <h4 class="no-margin"><span class="semi-bold">Access</span> Configuration</h4>
         <br>
          
             <div class="user-description-box allProjectsAccess">
            <div class="row">
               <div class="col-md-4"><h4 class="user-head">Projects</h4></div>
               <div class="col-md-8 text-center">
                  <div class="checkbox check-primary">
                  <input id="has_access" type="checkbox" name="has_all_access" value="yes" >
                  <label for="has_access"><h4 class="no-margin">Access to all Projects<small> (This would automatically give access to future Projects.)</small></h4></label>
               </div>
                  <h5 class="user-sub-head">Access (Individual)</h5>
               </div>
            </div>
            <div class="add_user_associates">
            <hr>
            <div class="row project_users">
               <div class="col-md-4">
                  <select name="projects[]" id="projects" class="select2 form-control"  >
                     <option value="">Select Project</option>
                     @foreach($projects as $project)
                     <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                     @endforeach
 
                  </select>
               </div>
               <div class="col-md-4">
                  <div class="radio radio-primary text-right">
                     <input id="access_view_0" type="radio" name="access_0" value="view" checked="checked">
                     <label for="access_view_0">View</label>
                     <input id="access_edit_0" type="radio" name="access_0" value="edit">
                     <label for="access_edit_0">Edit</label>
                  </div>
               </div>
            </div>
            <hr>
            <div class="row">
               <div class="col-md-4">
                  <input type="hidden" name="counter" value="0">
                  <button type="button" object-type="Project" class="btn btn-link text-success pullleft add-project-user"><i class="fa fa-plus"></i> Add Project</button>
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
               <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
               <button class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
               <a href="{{ url($hospital['url_slug'].'/users') }}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
            </div>
         </div>
      </form>
   </div>
</div>
</form>

@endsection