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
            <a href="{{ url( 'admin/hospitals/' ) }}"> Hospitals</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active"> Add Hospital</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Add Hospital</span></h3>
   <p>(Create a Hospital under Mylan)</p>
</div>
@include('admin.flashmessage')
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url('admin/hospitals') }}" data-parsley-validate>
<div class="grid simple">
   <div class="grid-body">
       <div class="row">
            <div class="col-md-5">
            
                   <h4 class="m-b-20"><span class="semi-bold">Hospital Logo</span></h4>
                   
                <div class="upload upload-border text-center">
                        <a class="deleteHospitalLogo btn btn-link btn-xs pull-right hidden" data-type="hospital" data-value="0" href="javascript:;"><i class="fa fa-close text-danger"></i></a>
                        <div class="img-div" id="hospital_logo_block">
                                                 
                        <a id="pickfiles" class="" href="javascript:;">
                        <i class=" fa fa-image fa-3x text-danger"></i><br>
                        <h5 class="text-muted">Click to upload Hospital Logo</h5>
                        </a> 
                        <div class="loader progress transparent progress-small no-radius hidden" >
                        </div>                    
                        </div>
                        
                        

                        <input type="hidden" name="hospital_logo" id="hospital_logo" data-parsley-required>
                    </div>
                <p class="text-center m-t-10 fosz12">Upload a logo having dimensions of approximately 200 X 50.</p>

                                   <br>
                   <h4 class="m-b-0" style="margin-top: 22px;"><span class="semi-bold">Primary Contact Details</span></h4>
                   <hr>
                   <div class="form-row">
                       <label>Contact name</label>
                       <input name="contact_person" id="contact_person" type="text" class="form-control" data-parsley-required data-parsley-trigger="change">
                   </div>
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Email</label>
                               <input name="primary_email" id="primary_email" type="email" class="form-control" data-parsley-type="email" data-parsley-required data-parsley-trigger="change">
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Phone Number</label>
                               <input name="primary_phone" id="primary_phone" type="text" class="form-control" data-parsley-required data-parsley-type="number"  data-parsley-trigger="change">
                           </div>
                       </div>
                   </div>
           </div>
           <div class="col-md-7">
              <h4 class="m-b-0"><span class="semi-bold">Basic Information</span></h4>
                   <hr> 
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Hospital Name</label>
                               <input name="name" id="name" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Phone Number</label>
                               <input name="phone" id="phoneNo" type="text" class="form-control" data-parsley-required data-parsley-type="number"  data-parsley-trigger="change">
                           </div>
                       </div>
                   </div>
                   
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Email</label>
                               <input name="email" id="email" type="email" class="form-control" data-parsley-type="email" data-parsley-required data-parsley-trigger="change">
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Website URL</label>
                               <input name="website" id="website" type="text" class="form-control" data-parsley-type="url" data-parsley-required data-parsley-trigger="change">
                           </div>
                       </div>
                   </div>

                   <br>
                   <h4 class="m-b-0"><span class="semi-bold">Hospital Address</span></h4>
                   <hr>
                   <div class="form-row">
                   
                           <div class="form-row">
                               <label>Address Line 1</label>
                               <input name="address_line_1" id="address_line_1" type="text" class="form-control" data-parsley-required>
                           </div>
                       
                        
                   </div>
                   <div class="form-row">
                        
                           <div class="form-row">
                               <label>Address Line 2</label>
                               <input name="address_line_2" id="address_line_2" type="text" class="form-control" data-parsley-required>
                           </div>
                      
                   </div>
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Town/City</label>
                               <input name="city" id="city" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Country</label>
                               <input name="country" id="country" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
                   </div>
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Postal Code</label>
                               <input name="postal_code" id="postal_code" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
            
                   </div>

                  
               
               <div class="text-right m-t-20">
                   <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
                   <button class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
                   <a href="{{'/admin/hospitals'}}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
               </div>
           </div>
       </div>
   </div>
</div>
</form>
<script>
   var HOSPITAL_ID = 0;
</script>
@endsection