@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
         <li>
            <a href="#"> Hospitals</a>
         </li>
         <li>
            <a href="#"> Edit Hospital</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Edit Hospital</span></h3>
   <p>(Update the Hospital details)</p>
</div>
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="/admin/hospitals/{{$hospital['id']}}" data-parsley-validate>
<div class="grid simple">
   <div class="grid-body">
       <div class="row">
           <div class="col-md-4 text-center">
               <div class="row-fluid">
                  
                  <div id="hospital_logo_block">
                   @if($hospital['logo']!='')
                  <img src="{{ $imagePath }}">
                  <a  class="deleteHospitalLogo" data-type="hospital" data-value="{{ $hospital['id'] }}" href="javascript:;">[delete]</a>
                  @endif
                  </div>
                  <a class="{{ ($hospital['logo']!='')?'hidden':'' }}" id="pickfiles" href="javascript:;">[Select files]</a>
                  <span id="loader"></span>
                   

                  
                  <input type="hidden" name="hospital_logo" id="hospital_logo">     
               </div>
           </div>
           <div class="col-md-8">
               
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Name</label>
                               <input name="name" id="name" type="text" value="{{ $hospital['name'] }}" class="form-control" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Phone Number</label>
                               <input name="phone" id="phoneNo" type="text" class="form-control"  value="{{ $hospital['phone'] }}" data-parsley-required>
                           </div>
                       </div>
                   </div>
                   <div class="form-row">
                   
                           <div class="form-row">
                               <label>Address Line 1</label>
                               <input name="address_line_1" id="address_line_1" value="{{ $hospital['address_line_1'] }}" type="text" class="form-control" data-parsley-required>
                           </div>
                       
                        
                   </div>
                   <div class="form-row">
                        
                           <div class="form-row">
                               <label>Address Line 2</label>
                               <input name="address_line_2" id="address_line_2" value="{{ $hospital['address_line_2'] }}" type="text" class="form-control" data-parsley-required>
                           </div>
                      
                   </div>
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Town/City</label>
                               <input name="city" id="city" type="text" value="{{ $hospital['city'] }}" class="form-control" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Country</label>
                               <input name="country" id="country" value="{{ $hospital['country'] }}" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
                   </div>
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Postal Code</label>
                               <input name="postal_code" id="postal_code" value="{{ $hospital['postal_code'] }}" type="text" class="form-control" data-parsley-required>
                           </div>
                       </div>
            
                   </div>
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Email</label>
                               <input name="email" id="email" type="email" class="form-control"  value="{{ $hospital['email'] }}" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Website URL</label>
                               <input name="website" id="website" type="text" class="form-control"  value="{{ $hospital['website'] }}" data-parsley-required>
                           </div>
                       </div>
                   </div>
                   <hr>
                   <div class="form-row">
                       <label>Primary Contact</label>
                       <input name="contact_person" id="contact_person" type="text" class="form-control"  value="{{ $hospital['contact_person_name'] }}" data-parsley-required>
                   </div>
                   <div class="row form-row">
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Email</label>
                               <input name="primary_email" id="primary_email" type="email" class="form-control"  value="{{ $hospital['primary_email'] }}" data-parsley-required>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-row">
                               <label>Phone Number</label>
                               <input name="primary_phone" id="primary_phone" type="text" class="form-control"  value="{{ $hospital['primary_phone'] }}" data-parsley-required>
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
                   <input type="hidden" name="_method" value="PUT">
                   <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
                   <button class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
                   <button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button>
               </div>
           </div>
       </div>
   </div>
</div>
</form>
<script>
   var HOSPITAL_ID = {{ $hospital['id'] }};
</script>
@endsection