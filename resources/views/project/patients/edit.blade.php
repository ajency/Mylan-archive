@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}" class="ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</a> </li>
        <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Edit</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div class="page-title">
        <h3><span class="semi-bold">Patients</span></h3>
      </div> 
    @include('admin.flashmessage')
     <div class="grid simple">
           <div class="grid-body">
      
      <form class="form-no-horizontal-spacing" id="patientform" name="patientform"  method="POST" action="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}" onsubmit="return validateOptionalInputs(false);">
              <div class="row form-group edit-add col-m-b-0">
                <div class="col-md-4">
                  <div class="form-row">
                     <label>Reference Code <span class="text-primary">*</span></label>
                        <input name="reference_code" id="reference_code" type="text"  class="validateRefernceCode form-control ttuc" placeholder="Reference Code" data-parsley-whitespace data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" data-parsley-type="alphanum" value="{{ $patient['reference_code'] }}" data-parsley-trigger="change" @if($patient['account_status']!='created') readonly @endif>
                  </div>
                </div>
                <div class="col-sm-4">
                 <div class="form-row">
                     <label>Age <span class="text-primary">*</span></label>
                        <input name="age" id="age" type="number" class=" form-control" placeholder="Age" data-parsley-type="number" data-parsley-required value="{{ $patient['age'] }}">
                    </div>
                    </div>

                    <div class="col-sm-4">
                      @if($patient['account_status']!='created')
                      <div class="row column-seperator">
                        
                         <label>Status</label>
                         <div class="row m-t-15">
                           <div class="col-sm-4">
                             <input name="status" id="status_active" type="radio" class=" " {{ ($patient['account_status']=='active')?'checked':'' }} value="active"> <label for="status_active" style="display: inline-block;">Active</label>
                           </div>
                           <div class="col-sm-5">
                             <input name="status" id="status_inactive" type="radio" class="" {{ ($patient['account_status']=='inactive')?'checked':'' }} value="inactive"> <label for="status_inactive" style="display: inline-block;">Inactive </label>
                           </div>
                         </div>
                         

                       

                     </div> 
                     @else 
                     <input name="status" type="hidden" value="{{ $patient['account_status'] }}">
                     @endif
                   </div>
                  
                    <div class="clearfix"></div>
                    <hr class="m-l-15 m-r-15">
                     <div class="row column-seperator p-l-15 p-r-15">
                       <div class="col-md-8">
                         <div class="row form-row">
                           <div class="col-sm-4 customMessage">
                             <label>Is Smoker <span class="text-primary">*</span></label>
                             <select name="is_smoker" id="is_smoker" class="select2 form-control"  data-parsley-required>
                              <option value="">Select</option>
                              <option {{ ($patient['patient_is_smoker']=='yes')?'selected':'' }} value="yes">Yes</option>
                              <option {{ ($patient['patient_is_smoker']=='no')?'selected':'' }} value="no">No</option> 
                            </select>
                          </div>
                          <div class="col-sm-4 smoke-input customMessage">
                           <label>If yes, how many per week</label>
                           <input name="smoke_per_week" id="smoke_per_week" type="text" class=" form-control"  placeholder="How many per week" value="{{ $patient['patient_smoker_per_week'] }}">
                         </div>
                         <div class="col-sm-4 customMessage">
                           <label>Alcohol consumption per week</label>
                           <input name="units_per_week" id="units_per_week" type="text" class="form-control"  placeholder="Units per week" value="{{ $patient['patient_alcohol_units_per_week'] }}">
                         </div>
                       </div>
                     </div>
                   </div>

                    <div class="row"><!-- parent row starts-->
                    <div class="col-sm-12 p-l-30 p-r-30">
                    @foreach($projectAttributes as $key=> $attribute)
                        
                                <?php
                                $defaults = explode(',', $attribute['values']);   
                                $defaults = array_filter($defaults);

                                $patientProjectAttributes = $patient['project_attributes'];

                                $value = (isset($patientProjectAttributes[ $attribute['label'] ])) ? $patientProjectAttributes[ $attribute['label'] ] : '';
                                ?>
                                 
                                @if('textbox' === $attribute['control_type'])
                                  @if(!empty($defaults))
                                    <?php $i=1;?>
                                    @foreach($defaults as $default)
                                     <?php
                                        $value = (isset($patientProjectAttributes[ $attribute['label'] ][$default])) ? $patientProjectAttributes[ $attribute['label'] ][$default] : '';
                                        ?>
                                        <hr>
                                        <div class="row p-l-15">
                                          <div class="add-attribute1"> 
                                            <div class="form-inline">
                                              <div class="form-group">
                                                <label class="@if($i!=1) fade-0 @endif">{{ $attribute['label'] }} 
                                                 @if(('on' == $attribute['validate']) && count($defaults) == 1)
                                                 <span class="text-primary">*</span>
                                                 @endif
                                               </label>
                                               <div class="input-group col-md-4">
                                                <input type="text" class="form-control @if('on' == $attribute['validate'] && count($defaults) > 1) optionalInputs @endif" name="attributes[{{ $attribute['label'] }}][{{ $default }}]" placeholder="{{ $default }}" data-parsley-group="block-{{ $key }}"

                                                @if(('on' == $attribute['validate']) && count($defaults) == 1)
                                                data-parsley-required 
                                                @endif

                                                value="{{ $value }}"
                                                >
                                                <div class="input-group-addon">{{ $default }}</div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    <?php $i++;?>  
                                    @endforeach
                                  @else
                                  <div class="row p-l-15">
                                    <div class="add-attribute">
                                      <label>{{ $attribute['label'] }} 
                                        @if('on' == $attribute['validate'])
                                        <span class="text-primary">*</span>
                                        @endif
                                      </label>
                                      <input type="text" class="form-control col-md-4" name="attributes[{{ $attribute['label'] }}]"  placeholder="Enter {{$attribute['label']}}" @if('on' == $attribute['validate']) data-parsley-required @endif value="{{ $value }}">
                                    </div>
                                  </div>
                                  @endif
                                @elseif('number' === $attribute['control_type'])
                                  @if(!empty($defaults))
                                  
                                    <?php $i=1;?>
                                    @foreach($defaults as $default)
                                    <?php
                                        $value = (isset($patientProjectAttributes[ $attribute['label'] ][$default])) ? $patientProjectAttributes[ $attribute['label'] ][$default] : '';
                                        ?>
                                        <hr>
                                        <div class="row p-l-15">
                                          <div class="add-attribute1"> 
                                            <div class="form-inline">
                                              <div class="form-group">
                                                <label class="@if($i!=1) fade-0 @endif">{{ $attribute['label'] }} 
                                                  @if('on' == $attribute['validate'] && count($defaults) > 1)
                                                  <span class="text-primary">*</span>
                                                  @endif
                                                </label>
                                                <div class="input-group col-md-4">
                                                  <input type="text" class="form-control  @if('on' == $attribute['validate'] && count($defaults) > 1) optionalInputs @endif" name="attributes[{{ $attribute['label'] }}][{{ $default }}]" placeholder="{{ $default }}" data-parsley-group="block-{{ $key }}" data-parsley-type="number" data-parsley-min="0"  @if(('on' == $attribute['validate']) && count($defaults) == 1) data-parsley-required @endif  value="{{ $value }}" >
                                                  <div class="input-group-addon">{{ $default }}</div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>

                                      <?php $i++;?>  
                                    @endforeach
                                     
                                  @else
                                  <hr>
                                  <div class="row">
                                    <div class="add-attribute">
                                      <label class="p-l-15">{{ $attribute['label'] }} 
                                        @if('on' == $attribute['validate'])
                                        <span class="text-primary">*</span>
                                        @endif

                                      </label>
                                      <input type="text" class="form-control col-md-4" name="attributes[{{ $attribute['label'] }}]"  placeholder="Enter {{$attribute['label']}}" @if('on' == $attribute['validate']) data-parsley-required @endif  data-parsley-type="number" data-parsley-min="0" value="{{ $value }}">
                                    </div>
                                  </div>
                                  @endif
                                
                                @elseif('select' == $attribute['control_type'])
                                <hr>
                                <div class="row">
                                  <div class="customSelect">
                                    <label class="p-l-15">{{ $attribute['label'] }} 
                                      @if('on' == $attribute['validate'])
                                      <span class="text-primary">*</span>
                                      @endif

                                    </label>
                                    <div class="col-md-4">
                                      <select name="attributes[{{ $attribute['label'] }}]" class="select2 form-control m-b-5" @if('on' == $attribute['validate'])data-parsley-required @endif>
                                        <option value="">Select {{ $attribute['label'] }}</option>   
                                        @foreach($defaults as $option)
                                        <option   @if($value ==  $option ){{'selected'}}@endif value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                      </select>
                                    </div>
                                  </div>
                                </div>
                                @elseif('multiple' == $attribute['control_type'])
                                <hr>
                                <div class="row">
                                  <div class="multiSelect">
                                    <div class="form-row">
                                      <label class="p-l-15">{{ $attribute['label'] }} 
                                        @if('on' == $attribute['validate'])
                                        <span class="text-primary">*</span>
                                        @endif
                                      </label>
                                      <div class="col-md-4">
                                        <select multiple name="attributes[{{ $attribute['label'] }}][multiple][]" class="multiselect select2 form-control m-b-5" @if('on' == $attribute['validate']) data-parsley-mincheck="1" data-parsley-required @endif>
                                          <!-- <option value="">Select {{ $attribute['label'] }}</option>    -->
                                          @foreach($defaults as $option)
                                          <option {{ (isset($value['multiple']) && !empty($value['multiple']) && in_array( $option ,$value['multiple'])) ? 'selected="selected"' : '' }} value="{{ $option }}">{{ $option }}</option>
                                          @endforeach
                                        </select>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                @elseif('weight' === $attribute['control_type'])
                                    <?php
                                    $readOnly = '';
                                    $value = (isset($patientProjectAttributes[ $attribute['label'] ]['kg'])) ? $patientProjectAttributes[ $attribute['label'] ]['kg'] : '';

                                    $stValue = (isset($patientProjectAttributes[ $attribute['label'] ]['st'])) ? $patientProjectAttributes[ $attribute['label'] ]['st'] : '';

                                    $lbValue = (isset($patientProjectAttributes[ $attribute['label'] ]['lb'])) ? $patientProjectAttributes[ $attribute['label'] ]['lb'] : '';

                                    
                                    if($value=='' && $stValue=='' && $lbValue=='')
                                    {
                                        $readOnly = '';
                                    }
                                    elseif($value=='')
                                    {
                                      $readOnly = 'readonly';
                                    }
                                    ?>
                                    <hr>
                                    <div class="row">
                                      <div class="add-attribute1"> 
                                        <div class="form-inline">
                                          <div class="form-group">
                                            <label class="p-l-15">{{ $attribute['label'] }} <i class="fa fa-exclamation-circle p-l-5" data-toggle="tooltip" data-placement="top" title="Please enter your weight in KG or ST/LB"></i></label>
                                            <div class="input-group col-md-4">
                                              <input type="text" class="form-control  weightQuestion weight-kg @if('on' == $attribute['validate']) optionalInputs @endif" name="attributes[{{ $attribute['label'] }}][kg]"  placeholder="kg" data-parsley-group="block-{{ $key }}" value="{{ $value }}" {{ $readOnly }} data-parsley-type="number" data-parsley-min="0" >
                                              <div class="input-group-addon">kg</div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  <?php
                                     $readOnly = '';
                                    if($value=='' && $stValue=='' && $lbValue=='')
                                    {
                                        $readOnly = '';
                                    }
                                    elseif($stValue=='' && $lbValue=='')
                                    {
                                      $readOnly = 'readonly';
                                    }
                                    ?>
                                    <hr>
                                    <div class="row">
                                    <div class="add-attribute1">
                                        <div class="form-inline parent clearfix">
                                          <div class="form-group">
                                            <label class="fade-0 p-l-15">{{ $attribute['label'] }} 
                                              @if('on' == $attribute['validate'])
                                              <span class="text-primary">*</span>
                                              @endif
                                            </label>
                                            <div class="input-group col-md-4">
                                             <input type="text" class="form-control weightQuestion weight-st @if('on' == $attribute['validate']) optionalInputs @endif" name="attributes[{{ $attribute['label'] }}][st]"  placeholder="st" data-parsley-group="block-{{ $key }}" value="{{ $stValue }}" {{ $readOnly }} data-parsley-type="number" data-parsley-min="0" >
                                             <div class="input-group-addon">st</div>
                                           </div>
                                         </div>
                                       </div>
                                       <div class="form-inline parent clearfix">
                                        <div class="form-group">
                                          <label class="fade-0 p-l-15">{{ $attribute['label'] }} </label>
                                          <div class="input-group col-md-4">
                                            <input type="text" class="form-control weightQuestion weight-lb @if('on' == $attribute['validate']) optionalInputs @endif" name="attributes[{{ $attribute['label'] }}][lb]"  placeholder="lb" data-parsley-group="block-{{ $key }}" value="{{ $lbValue }}" {{ $readOnly }} data-parsley-type="number" data-parsley-min="0" >
                                            <div class="input-group-addon">lb</div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                @endif            
                             
                        
                        @endforeach
                        </div>
                        </div> <!-- /parent row ends -->

                </div>
                   

       <!--                  <div class="col-sm-3">
                      <label>Weight</label>
                        <input name="weight" id="weight" type="text"  class="validateRefernceCode  form-control" placeholder="Weight" data-parsley-required >
                    </div>
                     <div class="col-sm-3">
                      <label>Height</label>
                        <input name="height" id="height" type="text"  class="validateRefernceCode form-control" placeholder="Height" data-parsley-required >
                    </div>
                     -->
                     
                   


                @if($patient['account_status']!='created' && Auth::user()->type=='mylan_admin')
                <hr>
               <h4 class="no-margin">Reset <span class="semi-bold">Password</span></h4>
              
               <div class="form-row">
                 
              <div class="row">
                 <div class="col-sm-4 text-left passGenerate">
                    <button type="button" class="btn btn-primary generate_new_password" object-id="{{ $patient['id'] }}" identify-user ="patient">Generate New Password <i class="fa"></i></button> 
                    <span id="generatePassword" class="generatePassword"></span>
                 </div>
              </div>
            </div>
              @endif

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
                          <button type="button" class="btn btn-white delete-madication"><i class="fa fa-trash"></i></button>
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
                                    <button type="button" class="btn btn-white delete-visit"><i class="fa fa-trash"></i></button>
                                 </div>
                              </div>
                          @endforeach
                          @endif
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

                  <button type="button" class="btn btn-link text-success add-visit"><i class="fa fa-plus"></i> Add Visit</button>

                  <hr>
               <h4 class="no-margin">Questionnaire <span class="semi-bold">Settings</span></h4>
               <span class="help-block m-t-5">Settings configured at project level will be accepted if not set at patient level.</span>
               <br>

      <div class="form-group quesSetting">
          <label for="frequency" class="col-sm-4 side-label">Frequency</label>
          <div class="col-sm-4">
            <div class="row">
              <div class="col-sm-9">
                <input type="text" name="frequencyDay" class="form-control" id="frequency" placeholder="Frequency" value="{{ $settings['frequency']['day'] }}" data-parsley-trigger="keyup" data-parsley-type="digits">
              </div>
              <div class="col-md-3">
                <h6 class="seconds">days</h6>
              </div>
            </div>
          </div>
          
          <div class="col-sm-4">
          <div class="row">
            <div class="col-sm-9">
              <input type="text" name="frequencyHours" class="form-control" id="frequency" placeholder="Frequency" value="{{ $settings['frequency']['hours']  }}" data-parsley-trigger="keyup" data-parsley-type="digits">
            </div>
            <div class="col-sm-3">
                <h6 class="seconds">hours</h6>
            </div>
          </div>
          </div>
      </div>

       <div class="form-group quesSetting">

          <label for="gracePeriod" class="col-sm-4 side-label">Grace Period</label>
          <div class="col-sm-4">
          <div class="row">
            <div class="col-sm-9">
              <input type="text" class="form-control input-days" id="gracePeriod" name="gracePeriodDay" placeholder="Grace Period" value="{{ $settings['gracePeriod']['day'] }}" data-parsley-trigger="keyup" data-parsley-type="digits">
            </div>
            <div class="col-sm-3">
               <h6 class="seconds">days</h6>
            </div>
          </div>
          </div>

            <div class="col-sm-4">
            <div class="row">
              <div class="col-sm-9">
                <input type="text" name="gracePeriodHours" class="form-control input-hours" id="gracePeriodHours" placeholder="Grace Period" value="{{ $settings['gracePeriod']['hours'] }}" data-parsley-trigger="keyup" data-parsley-type="digits">
              </div>
              <div class="col-sm-3">
                <h6 class="seconds">hours</h6>
              </div>
            </div>
          </div>

        </div>

       <div class="form-group quesSetting">

        <label for="reminderTime" class="col-sm-4 side-label">Reminder Time</label>
        <div class="col-sm-4">
        <div class="row">
          <div class="col-sm-9">
            <input type="text" class="form-control input-days" name="reminderTimeDay" id="reminderTime" placeholder="Reminder Time" value="{{ $settings['reminderTime']['day'] }}" data-parsley-trigger="keyup" data-parsley-type="digits">
          </div>
          <div class="col-sm-3">
            <h6 class="seconds">days</h6>
          </div>
        </div>
        </div>

          <div class="col-sm-4">
          <div class="row">
            <div class="col-sm-9">
              <input type="text" name="reminderTimeHours" class="form-control input-hours" id="reminderTimeHours" placeholder="Reminder Time" value="{{ $settings['reminderTime']['hours'] }}" data-parsley-trigger="keyup" data-parsley-type="digits">
            </div>
            <div class="col-sm-3">
              <h6 class="seconds">hours</h6>
            </div>
          </div>
        </div>
      </div>

        </div>

                
        
               
        <div class="form-actions quesSetting-actions">
          <div class="text-right">
          <input type="hidden" name="_method" value="PUT">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
          @if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
            <button class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
             @endif
            <!-- <button class="btn btn-danger btn-cons-md" type="submit"><i class="icon-ok"></i> Save and Add Another</button> -->
            <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients') }}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
          </div>
          </div>
      </form>
            </div>
          </div>

<script type="text/javascript">

$(document).ready(function() {

 
     $('.input-days').change(function (event) {  
      if($(this).val() >= 1)
      { 
         $(this).closest(".form-group").find('.input-hours').removeAttr("min"); 
         $(this).closest(".form-group").find('.input-hours').removeAttr("data-parsley-validation-threshold");
      }
      else
      {
        $(this).closest(".form-group").find('.input-hours').attr("min","1"); 
        $(this).closest(".form-group").find('.input-hours').attr("data-parsley-validation-threshold","1");
      }
    });
});

</script>

<script type="text/javascript">
  function validateOptionalInputs()
  {
    optionalInputArr = {};
    var str ='';
    var valueArr = {};
    var controlObj = {};
    $('.optionalInputs').each(function () {
      var group = $(this).attr('data-parsley-group');
      var value = $(this).val(); 
      
      if(str!=group)
      {
        str = group;
        valueArr = []; 
      }
       
      valueArr.push(value);
      optionalInputArr[group]=valueArr; 
      controlObj[group]=$(this); 
    });

    $.each(optionalInputArr, function (index, value) { 
        
        var err = 0; 
        $.each(value, function (key, val) { 
           if(val!='')
           {
              err++;
           }
             
        });
       
        if(err==0)
          controlObj[index].closest('div').find('.parsley-errors-list').html('<li class="parsley-required">Enter atleast 1 field</li>');
        else
           controlObj[index].closest('div').find('.parsley-errors-list').html('');

    }); 
       
    var settings = validatefrequencySettings();

    return settings;
  }

  $('.optionalInputs').change(function (event) { 
    validateOptionalInputs();
  });


  $(document).ready(function() {

    $(".multiselect").multiselect();
    
    $('.datetimepicker').datetimepicker({
        format: 'DD-MM-YYYY hh:mm a'

      });

     $('select[name="is_smoker"]').change(function (event) { 
      if($(this).val()=='yes')
      { 
        $('input[name="smoke_per_week"]').attr('data-parsley-required','');
		 $('input[name="smoke_per_week"]').removeAttr('readonly');
      }
      else
      {
        $('input[name="smoke_per_week"]').removeAttr('data-parsley-required');
		$('input[name="smoke_per_week"]').attr('readonly',true);
      }
    });

     $('select[name="is_alcoholic"]').change(function (event) { 
      if($(this).val()=='yes')
      { 
        $('input[name="units_per_week"]').attr('data-parsley-required','');
		$('input[name="units_per_week"]').removeAttr('readonly');
      }
      else
      {
        $('input[name="units_per_week"]').removeAttr('data-parsley-required');
		$('input[name="units_per_week"]').attr('readonly',true);
      }
    });



$("#patientform").find("button[type='submit']").on('click', function() {
  
        validateInput();
        //return false;
    });
    
    function validateInput() {
        $("#patientform").find(".target").parsley({
            successClass: "has-success",
            errorClass: "has-error",
            classHandler: function (el) {
                return el.$element.closest('.form-group'); //working
            },
            errorsWrapper: "<span class='help-block'></span>",
            errorTemplate: "<span></span>",
            
        });
        

        // validate field and affects UI
       $("#patientform").parsley().validate();
    }
	
	//check default value and add disabled
	 if($('select[name="is_smoker"]').val() == 'yes'){
		 $('input[name="smoke_per_week"]').removeAttr('readonly');
	 }else{
		$('input[name="smoke_per_week"]').attr('readonly',true);
	 }
	 
	 if($('select[name="is_alcoholic"]').val() == 'yes'){
		 $('input[name="units_per_week"]').removeAttr('readonly');
	 }else{
		$('input[name="units_per_week"]').attr('readonly',true);
	 }

  });
 
    var PATIENT_ID = {{ $patient['id'] }};


</script>
<!-- END PLACE PAGE CONTENT HERE -->
@endsection