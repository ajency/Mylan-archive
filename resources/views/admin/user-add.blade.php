@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"> Home</a>
         </li>
         <li>
            <a href="{{ url( 'admin/users/' ) }}"> User</a>
         </li>
         <li>
            <a href="#" class="active" > Add User</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Add User</span></h3>
   <p>(Create a User)</p>
</div>
@include('admin.flashmessage')
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url('admin/users') }}" data-parsley-validate>
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
                  <input name="email" id="email" type="email" objectId="0" objectType="hospital"  class="authUserEmail form-control" data-parsley-required data-parsley-type="email">

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
         <h4 class="no-margin"><span class="semi-bold">Access</span> Configuration
         <div class="checkbox check-primary custom-checkbox pull-right">
                  <input id="has_all_access" type="checkbox" name="has_all_access" value="yes" >
                  <label for="has_all_access"><h4 class="no-margin">Access to all Hospitals<small> (This would automatically give access to future Hospitals.)</small></h4></label>
               </div>
         </h4>
         <br>
         <!-- <div class="user-description-box">
            <div class="row">
               <div class="col-md-3">Mylan</div>
               <div class="col-md-3">
                  <div class="checkbox check-primary">
                  <input id="had_mylan_access" type="checkbox" name="had_mylan_access" value="yes" >
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
                     <input id="mylan_access_view" type="radio" name="mylan_access" value="view" checked="checked">
                     <label for="mylan_access_view">View</label>
                     <input id="mylan_access_edit" type="radio" name="mylan_access" value="edit">
                     <label for="mylan_access_edit">Edit</label>
                  </div>
               </div>
            </div>

         </div>
         <br> -->
             <div class="user-description-box allHospitalsAccess">
            <div class="row">
               <div class="col-md-4 text-center"><h4 class="user-head">Hospital</h4></div>
               <div class="col-md-4 text-right">
                 <h4 class="user-sub-head">Access (Individual)</h4>
               </div>
            </div>
            <div class="add_user_associates">
            <hr>
            <div class="row hospital_users add-user-container">
               <div class="col-md-4">
                  <select name="hospital[]" id="hospital" class="select2 form-control"  >
                     <option value="">Select Hospital</option>
                     @foreach($hospitals as $hospital)
                     <option value="{{ $hospital['id'] }}">{{ $hospital['name'] }}</option>
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
               <div class="col-md-4 text-center">
                  <a class="deleteUserHospitalAccess hidden" data-id="0"> Delete </a>
                  <button type="button"  object-type="Hospital" class="btn btn-link text-success pullleft add-hospital-user"><i class="fa fa-plus"></i> Add Hospital</button>
               </div>
               <div class="col-md-12">
                    <hr>
               </div>

            </div>
          
            <div class="row">
               <div class="col-md-4">
                  <input type="hidden" name="counter" value="0">
                  
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
               <a href="{{'/admin/users'}}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
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