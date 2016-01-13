@extends('layouts.single-hospital')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li>
          <a href="projects.html">Patients</a>
        </li>
        <li><a href="#" class="active">New</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div class="page-title">
        <h3><span class="semi-bold">Patients</span></h3>
      </div> 
     <div class="grid simple">
     @include('hospital.flashmessage')
           <div class="grid-body">
      <form class="form-no-horizontal-spacing" id="form-condensed"  method="POST" action="{{ url($hospital['url_slug'].'/patients') }}" data-parsley-validate>
              <div class="row column-seperation">
                <div class="col-md-6">
                  <div class="form-row">
                     <label>Reference Code</label>
                        <input name="reference_code" id="reference_code" type="text"  class="validateRefernceCode" placeholder="Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                    </div>
                    <div class="form-row">
                       <label>Project</label>
                      <select name="project" id="project" class="select2 form-control"  data-parsley-required>
                          <option value="">Select</option>
                          @foreach($projects as $project)
                          <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                          @endforeach
                        </select>
                      
                    </div>
                    <div class="row form-row">
               
                    <div class="col-sm-4">
                     <label>Age</label>
                        <input name="age" id="age" type="text" placeholder="Age" data-parsley-required >
                    </div>
                    <div class="col-sm-4">
                      <label>Weight</label>
                        <input name="weight" id="weight" type="text"  class="validateRefernceCode" placeholder="Weight" data-parsley-required >
                    </div>
                    <div class="col-sm-4">
                      <label>Height</label>
                        <input name="height" id="height" type="text"  class="validateRefernceCode" placeholder="Height" data-parsley-required >
                    </div>
                    </div>

                    <div class="row form-row">
               
                    <div class="col-sm-6">
                      <label>Is Smoker</label>
                      <select name="is_smoker" id="is_smoker" class="select2 form-control"  data-parsley-required>
                          <option value="">Select</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option> 
                        </select>
                    </div>
                    <div class="col-sm-6">
                      <label>If yes, how many per week</label>
                        <input name="smoke_per_week" id="smoke_per_week" type="text"  placeholder="How many per week" >
                    </div>
                    </div>

                    <div class="row form-row">
               
                    <div class="col-sm-6">
                      <label>Is Alcoholic</label>
                      <select name="is_alcoholic" id="is_alcoholic" class="select2 form-control"  data-parsley-required>
                          <option value="">Select</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option> 
                        </select>
                    </div>
                    <div class="col-sm-6">
                      <label>If yes, units per week</label>
                        <input name="units_per_week" id="units_per_week" type="text"  placeholder="Units per week" >
                    </div>
                    </div>
                     
                </div>
                
              </div>
                <br>
               <h4 class="no-margin">Medication <span class="semi-bold">Data</span></h4>
               <br>
               <div class="user-description-box">
                  <div class="sortable">
                     <div class="grid simple bg-gray">
                        <div class="grid-body">
                           <div class="form-row medication-data">
                              <div class="row patient-mediaction">
                                 <div class="col-sm-6 m-t-25 ">
                                    <input name="medications[]" id="medications" type="text"   placeholder="Enter Medication" class="form-control" >
                                 </div>
                                  
                                 <div class="col-sm-1 text-right m-t-25">
                                    <button type="button" class="btn btn-white delete-madication hidden"><i class="fa fa-trash"></i></button>
                                 </div>
                              </div>


                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <button type="button" class="btn btn-link text-success add-mediaction"><i class="fa fa-plus"></i> Add Medication</button>
        
               </div>
           <br>
               <h4 class="no-margin">Clinic <span class="semi-bold">Visits</span></h4>
               <br>
               <div class="user-description-box">
                  <div class="sortable">
                     <div class="grid simple bg-gray">
                        <div class="grid-body">
                           <div class="form-row visit-data">
                              <div class="row patient-visit">
                                 <div class="col-sm-3 m-t-25 input-daterange">
                                    <input name="visit_date[]" id="visit_date" type="text"   placeholder="Enter Date" class="form-control" >
                                 </div>
                                  <div class="col-sm-6 m-t-25 ">
                                    <textarea name="note[]" id="note" type="text"   placeholder="Enter Note" class="form-control"></textarea> 
                                 </div>
                                 <div class="col-sm-1 text-right m-t-25">
                                    <button type="button" class="btn btn-white delete-visit hidden"><i class="fa fa-trash"></i></button>
                                 </div>
                              </div>


                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <button type="button" class="btn btn-link text-success add-visit"><i class="fa fa-plus"></i> Add Visit</button>
        
               </div>
        <div class="form-actions">
          <div class="text-right">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
            <button class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
            <!-- <button class="btn btn-danger btn-cons-md" type="submit"><i class="icon-ok"></i> Save and Add Another</button> -->
            <a href="{{'/admin/patients'}}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
          </div>
          </div>
      </form>
            </div>
          </div>
<script type="text/javascript">
  $(document).ready(function() {
     $('.input-daterange input').datetimepicker({
        format: "dd-mm-yyyy hh:ii",
        autoclose: true,
        todayBtn: true,
         
    });

 
  }); 
    var PATIENT_ID = 0;
</script>
 
<!-- END PLACE PAGE CONTENT HERE -->
@endsection