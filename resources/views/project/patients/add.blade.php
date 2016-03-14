@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="#" class="active">Add</a> </li>
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
      <form class="form-no-horizontal-spacing" id="patientform" name="patientform"  method="POST" action="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients') }}" data-parsley-validate>
              <div class="row form-group">
                <div class="col-md-3">
                  <div class="form-row">
                     <label>Reference Code</label>
                        <input name="reference_code" id="reference_code" type="text"  class="validateRefernceCode form-control" placeholder="Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                  </div>
                </div>
                <div class="col-sm-3">
                     <label>Age</label>
                        <input name="age" id="age" type="text" class=" form-control" placeholder="Age" data-parsley-required >
                    </div>
                      <div class="col-sm-3">
                      <label>Weight</label>
                        <input name="weight" id="weight" type="text"  class="validateRefernceCode  form-control" placeholder="Weight" data-parsley-required >
                    </div>
                     <div class="col-sm-3">
                      <label>Height</label>
                        <input name="height" id="height" type="text"  class="validateRefernceCode form-control" placeholder="Height" data-parsley-required >
                    </div>

                    @foreach($projectAttributes as $attributeLabel => $attribute)
                        <div class="col-md-3">
                             
                                <label>{{ $attributeLabel }} </label>
                                @if(count($attribute) > 1 && 'textbox' === $attribute[0]['control_type'])
                                    @foreach($attribute as $attributevalue)
                                        <input type="text"  name="attributes[{{ $attributeLabel }}][{{ $attributevalue['values'] }}]"  
                                       placeholder="Enter {{ $attributeLabel }}" data-parsley-required> {{ $attributevalue['values'] }}
                                    @endforeach
                                @elseif('textbox' === $attribute[0]['control_type'])
                                <input type="text" class="form-control" name="attributes[{{ $attributeLabel }}]"  
                                       placeholder="Enter {{ $attributeLabel }}" data-parsley-required> {{ $attribute[0]['values'] }}
                                @elseif(count($attribute) > 1 && 'number' === $attribute[0]['control_type'])
                                    @foreach($attribute as $attributevalue)
                                        <input type="text"  name="attributes[{{ $attributeLabel }}][{{ $attributevalue['values'] }}]"  
                                       placeholder="Enter {{ $attributeLabel }}" data-parsley-required data-parsley-type="number" data-parsley-min="0"> {{ $attributevalue['values'] }}
                                    @endforeach
                                @elseif('number' === $attribute[0]['control_type'])
                                <input type="text" class="form-control" name="attributes[{{ $attributeLabel }}]"  
                                       placeholder="Enter {{ $attributeLabel }}" data-parsley-required data-parsley-type="number" data-parsley-min="0">{{ $attribute[0]['values'] }}
                                @elseif('select' === $attribute[0]['control_type'])
                                <?php
                                $options = explode(',', $attribute[0]['values']);
                                ?>
                                <select name="attributes[{{ $attributeLabel }}]" class="select2 form-control" data-parsley-required>
                                    <option value="">Select {{ $attributeLabel }}</option>   
                                    @foreach($options as $option)
                                    <option  value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                @elseif('multiple' === $attribute[0]['control_type'])
                                <?php
                                $options = explode(',', $attribute[0]['values']);
                                ?>
                                <select multiple name="attributes[{{ $attributeLabel }}][]" class="select2 form-control" data-parsley-required>
                                    <option value="">Select {{ $attributeLabel }}</option>   
                                    @foreach($options as $option)
                                    <option   value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                @endif     
                             
                        </div>
                        @endforeach
                </div>
                  <!-- <div class="row column-seperator">
                  <div class="col-sm-6">
                    <div class="row form-row">
               
                    <div class="col-sm-6">
                      <label>Is Smoker</label>
                      <select name="is_smoker" id="is_smoker" class="select2 form-control"  data-parsley-required>
                          <option value="">Select</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option> 
                        </select>
                    </div>
                    <div class="col-sm-6 smoke-input">
                      <label>If yes, how many per week</label>
                        <input name="smoke_per_week" id="smoke_per_week" type="text" class=" form-control"  placeholder="How many per week" >
                    </div>
                    </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="row form-row">
               
                     <div class="col-sm-6">
                       <label>Is Alcoholic</label>
                      <select name="is_alcoholic" id="is_alcoholic" class="select2 form-control"  data-parsley-required>
                          <option value="">Select</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option> 
                        </select> 
                    </div> 
                    <div class="col-sm-6 ">
                      <label>Alcohol consumption per week</label>
                        <input name="units_per_week" id="units_per_week" type="text" class="form-control"  placeholder="Units per week" >
                    </div>
                    </div>
                    </div>
                     </div> -->
                
                
              
                <hr>
               <h4 class="no-margin">Medication <span class="semi-bold">Data</span></h4>
              
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
                       
                    
                   
                  
                  <button type="button" class="btn btn-link text-success add-mediaction"><i class="fa fa-plus"></i> Add Medication</button>
        
              
           <hr>
               <h4 class="no-margin">Clinic <span class="semi-bold">Visits</span></h4>
               
               
                  <div class="sortable">
                     <div class="form-row visit-data">
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
                        
                     
                  </div>
                  
                  <button type="button" class="btn btn-link text-success add-visit"><i class="fa fa-plus"></i> Add Visit</button>
        
               
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
    $('.datetimepicker').datetimepicker({
        format: 'DD-MM-YYYY hh:mm a'

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
 
    var PATIENT_ID = 0;


</script>
 
<!-- END PLACE PAGE CONTENT HERE -->
@endsection