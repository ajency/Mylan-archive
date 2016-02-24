@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li>
          <a href="projects.html">Patients</a>
        </li>
        <li><a href="#" class="active">Edit</a> </li>
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
           <div class="grid-body">
      <form class="form-no-horizontal-spacing" id="form-condensed"  method="POST" action="{{ url($hospital['url_slug'].'/patients/'.$patient['id'] ) }}" data-parsley-validate>
              <div class="row form-group">
                <div class="col-md-3">
                  <div class="form-row">
                     <label>Reference Code</label>
                        <input {{ $disabled }} name="reference_code" id="reference_code" class="form-control" type="text" value="{{ $patient['reference_code'] }}"   placeholder="Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                  </div>
                </div>
                <div class="col-sm-3"> 
                     <label>Age</label>
                        <input name="age" id="age" type="text" class="form-control" placeholder="Age" data-parsley-required   value="{{ $patient['age'] }}">
                    </div>
                    <div class="col-sm-3">
                      <label>Weight</label>
                        <input name="weight" id="weight" type="text"  class="validateRefernceCode form-control" placeholder="Weight" data-parsley-required  value="{{ $patient['patient_weight'] }}">
                    </div>
                    <div class="col-sm-3">
                      <label>Height</label>
                        <input name="height" id="height" type="text"  class="validateRefernceCode form-control" placeholder="Height" data-parsley-required  value="{{ $patient['patient_height'] }}">
                    </div>
              </div>
              <div class="row">
              <div class="col-sm-6">
              <div class="row form-row">
               
                    <div class="col-sm-6">
                      <label>Is Smoker</label>
                      <select name="is_smoker" id="is_smoker" class="select2 form-control"  data-parsley-required>
                          <option value="">Select</option>
                          <option {{ ($patient['patient_is_smoker']=='yes')?'selected':'' }} value="yes">Yes</option>
                          <option {{ ($patient['patient_is_smoker']=='no')?'selected':'' }} value="no">No</option> 
                        </select>
                    </div>
                    <div class="col-sm-6">
                      <label>If yes, how many per week</label>
                        <input name="smoke_per_week" id="smoke_per_week" type="text"  class="form-control" placeholder="How many per week" value="{{ $patient['patient_smoker_per_week'] }}">
                    </div>
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="row form-row">
               
                    <div class="col-sm-6">
                      <!-- <label>Is Alcoholic</label>
                      <select name="is_alcoholic" id="is_alcoholic" class="select2 form-control"  data-parsley-required >
                          <option value="">Select</option>
                          <option {{ ($patient['patient_is_alcoholic']=='yes')?'selected':'' }} value="yes">Yes</option>
                          <option {{ ($patient['patient_is_alcoholic']=='no')?'selected':'' }} value="no">No</option> 
                        </select> -->
                    </div>
                    <div class="col-sm-6">
                      <!-- <label>If yes, units per week</label>
                        <input name="units_per_week" id="units_per_week" type="text"  class="form-control" placeholder="Units per week" value="{{ $patient['patient_alcohol_units_per_week'] }}" > -->
                    </div>
                    </div>
                    </div>
                  </div>
               
                
             
                <hr>
               <h4 class="no-margin">Medication <span class="semi-bold">Data</span></h4>
              
              <div class="form-row medication-data">
                          @if(!empty($patientMedications))
                           @foreach($patientMedications as $medication)
                              <div class="row patient-mediaction">
                                 <div class="col-sm-6 m-t-25 ">
                                    <input name="medications[]" id="medications" type="text"  value="{{ $medication['medication'] }}"   placeholder="Enter Medication" class="form-control" >
                                 </div>
                                  
                                 <div class="col-sm-1 text-right m-t-25">
                                    <button type="button" class="btn btn-white delete-madication hidden"><i class="fa fa-trash"></i></button>
                                 </div>
                              </div>
                            @endforeach
                          @endif
                          <div class="row patient-mediaction">
                                 <div class="col-sm-6 m-t-25 ">
                                    <input name="medications[]" id="medications" type="text"   placeholder="Enter Medication" class="form-control" >
                                 </div>
                                  
                                 <div class="col-sm-1 text-right m-t-25">
                                    <button type="button" class="btn btn-white delete-madication hidden"><i class="fa fa-trash"></i></button>
                                 </div>
                              </div>
                           </div>
                    <button type="button" class="btn btn-link text-success add-mediaction"><i class="fa fa-plus"></i> Add Medication</button>
         <hr>
               <h4 class="no-margin">Clinic <span class="semi-bold">Visits</span></h4>
               <br>
             
                 
                           <div class="form-row visit-data">
                           @if(!empty($patientvisits))
                           @foreach($patientvisits as $visit)
                           <?php
                            $visitDate = date('d-m-Y H:i' , strtotime($visit['date_visited']));
                           ?>
                              <div class="row patient-visit">
                                <div class="datetime">
                                  <div class="col-sm-3 m-t-25 form-group">
                                     <div class='input-group date datetimepicker'>
                                        <input name="visit_date[]" id="visit_date" type="text" value="{{ $visitDate }}"  placeholder="Enter Date" class="form-control" >
                                        <span class="input-group-addon" >
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                     </div>
                                  </div>
                                </div>
                
                                  <div class="col-sm-6 m-t-25 ">
                                    <textarea name="note[]" id="note" type="text"   placeholder="Enter Note" class="form-control">{{ $visit['note'] }}</textarea> 
                                 </div>
                                 <div class="col-sm-1 text-right m-t-25">
                                    <button type="button" class="btn btn-white delete-visit hidden"><i class="fa fa-trash"></i></button>
                                 </div>
                              </div>
                          @endforeach
                          @endif
                          <div class="row patient-visit">
                                 <div class="datetime">
                                  <div class="col-sm-3 m-t-25 form-group">
                                     <div class='input-group date datetimepicker'>
                                        <input name="visit_date[]" id="visit_date" type="text"   placeholder="Enter Date" class="form-control"/>
                                        <span class="input-group-addon" >
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                     </div>
                                  </div>
                                </div>
                                  <div class="col-sm-6 m-t-25 ">
                                    <textarea name="note[]" id="note" type="text"   placeholder="Enter Note" class="form-control"></textarea> 
                                 </div>
                                 <div class="col-sm-1 text-right m-t-25">
                                    <button type="button" class="btn btn-white delete-visit hidden"><i class="fa fa-trash"></i></button>
                                 </div>
                              </div>

                           </div>
                  <button type="button" class="btn btn-link text-success add-visit"><i class="fa fa-plus"></i> Add Visit</button>
        </div>
                
              </div>
        <div class="form-actions">
          <div class="text-right">
          <input type="hidden" name="_method" value="PUT">
                   <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
            <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa"></i> Back</button></a>
            <button class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
            <a href="{{ url($hospital['url_slug'].'/patients' ) }}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
          </div>
          </div>
      </form>
            </div>
          </div>



<script type="text/javascript">
  $(document).ready(function() {
    $('.datetimepicker').datetimepicker({
        format: 'DD-MM-YYYY HH:mm'

      });

    $('select[name="is_smoker"]').change(function (event) { 
      if($(this).val()=='yes')
      { 
          $('input[name="smoke_per_week"]').attr('data-parsley-required','');
      }
      else
      {
         $('input[name="smoke_per_week"]').removeAttr('data-parsley-required');
      }
    });

    $('select[name="is_alcoholic"]').change(function (event) { 
      if($(this).val()=='yes')
      { 
         $('input[name="units_per_week"]').attr('data-parsley-required','');
      }
      else
      {
          $('input[name="units_per_week"]').removeAttr('data-parsley-required');
      }
    });
 
  }); 
</script>
<!-- END PLACE PAGE CONTENT HERE -->
@endsection