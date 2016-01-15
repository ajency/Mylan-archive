@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"> HOME</a>
         </li>
         <li>
            <a href="{{ url( 'admin/hospitals/' ) }}"> Hospitals</a>
         </li>
         <li>
            <a href="#" class="active"> Add Hospital</a>
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
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url('admin/hospitals') }}" data-parsley-validate>
<div class="grid simple">
   <div class="grid-body">
       <div class="row">
          <div class="col-md-4 text-center">
               
                    <div class="upload">
                        <div class="img-div" id="hospital_logo_block">
                    
                        </div>
                        <span id="loader"></span>
                        <a id="pickfiles" href="javascript:;"> <i class="fa fa-image fa-3x"></i><br>
                        <h5  class="text-muted">Click to upload Hospital Logo</h5></a>
                        <input type="hidden" name="hospital_logo" id="hospital_logo" data-parsley-required>
                    </div>
                
            </div>
           <div class="col-md-8">
               
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Name</label>
                               <input name="name" id="name" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Phone Number</label>
                               <input name="phone" id="phoneNo" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
                   </div>
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
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Email</label>
                               <input name="email" id="email" type="email" class="form-control" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Website URL</label>
                               <input name="website" id="website" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
                   </div>
                   <hr>
                   <div class="form-row">
                       <label>Primary Contact</label>
                       <input name="contact_person" id="contact_person" type="text" class="form-control" data-parsley-required>
                   </div>
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Email</label>
                               <input name="primary_email" id="primary_email" type="email" class="form-control" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Phone Number</label>
                               <input name="primary_phone" id="primary_phone" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
                   </div>
                   <!-- <div class="row form-row">
                       <div class="col-sm-6">
                           <label>Specialities</label>
                           <select name="form3Gender" id="form3Gender" class="select2 form-control">
                               <option value="1">Select</option>
                               <option value="2">Skin Care</option>
                               <option value="2">Carciac Care Care</option>
                           </select>
                       </div>
                       <div class="col-md-6 m-t-30"><a href="#" class="text-success"><i class="fa fa-plus"></i> Add More</a></div>
                   </div> -->
               
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