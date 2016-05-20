@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"> Home</a>
         </li>
         <li>
            <a href="#" class="active"> Change Password</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Change Password</span></h3>
 
</div>
@include('admin.flashmessage')
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/changepassword') }}" data-parsley-validate>
<div class="grid simple">
   <div class="grid-body">
      <form class="form-no-horizontal-spacing" id="form-condensed">
         <div class="row">
            <div class="col-sm-3">
               <div class="form-row form-group">
                  <label>New Password</label>
                  <input name="password" id="password" type="password"  class="form-control" data-parsley-required>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm-3">
               <div class="form-row">
                  <label>Confirm Password</label>
                  <input name="confirmpassword" id="confirmpassword" type="password" class="form-control" data-parsley-required data-parsley-equalto="#password"> 
               </div>
            </div>
              
         </div>

         <div class="form-actions">
            <div class="text-right">
               <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
               <button  class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
               <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/') }}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
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