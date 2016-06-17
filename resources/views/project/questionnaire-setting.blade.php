@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="#" class="active">Questionnaire Settings</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div>
                    
                     <div class="page-title">
                        <h3><span class="semi-bold">Questionnaire Settings</span></h3>
                     </div>
                  </div>
                                   
                   
                          
                           <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                       <h3 class="">{{ $project['name'] }}</h3>
                      
                      <hr>
          @include('admin.flashmessage')

            <form class="col-sm-12 mri-form setQuestion" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/'.$action.'/' ) }}" data-parsley-validate onsubmit="return validatefrequencySettings(true);">

            
            <div class="row">
              <div class="col-sm-5">
                <div class="form-group">
                  <label for="editable" class="side-label bold">Name</label>
                  
                  <input type="text" name="name" id="name" class="nameField"  value="{{ $settings['name'] }}" data-parsley-required>
                  
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="editable" class="side-label bold">Editable</label>
                  
                  <div class="radio p-t-10">
                  <input id="YES" type="radio" name="editable" value="yes" checked="checked">
                  <label for="YES">Yes</label>
                  <input id="NO" type="radio" name="editable" value="no" {{ ($settings['editable']==false)?'checked':'' }}>
                  <label for="NO">No</label>
                  </div>
                  
                </div>
              </div>
            </div>
            <hr class="m-t-0">
            

                  
                  

                   <!-- <div class="form-group">
                    <label for="Frequency" class="col-sm-4 side-label">Questionnaire Type</label>
                    <div class="col-sm-6">
                    <select id="type" name="type" style="width:100%" class="" data-parsley-required> -->
                    <!-- <option value="random" {{ ($settings['type']=='random')?'selected':'' }}>Random</option> -->
                    <!-- <option value="sequence" {{ ($settings['type']=='sequence')?'selected':'' }}>Sequence</option> -->
                    <!-- </select>
                    </div>
                  </div> -->
                  @if($settings['status'] =="published")
                  <div class="form-group">
                    <label for="Frequency" class="col-sm-2 side-label">Pause Project</label>
                    <div class="col-sm-3" style="padding-right:0;">
                      <select id="pauseProject" name="pauseProject" style="width:100%" class="">
                      <option value="yes" {{ ($settings['pauseProject']=='yes')?'selected':'' }}>Yes</option>
                      <option value="no" {{ ($settings['pauseProject']=='no')?'selected':'' }}>No</option>
                      </select>
                    </div>
                  </div>  
                  @endif

                  <div class="questionnaire-setting">
                    <div class="form-group clearfix">
 
                      <label for="frequency" class="side-label bold m-b-0">Frequency</label>
                      <span class="help-block m-t-5">Specify the interval after which the patient should be able to answer the questionnaire again.</span>
                      <div class="col-sm-5 p-l-0">
                        <input type="text" name="frequencyDay" class="form-control input-days" id="frequency" placeholder="No. of" value="{{ $settings['frequency']['day'] }}"  data-parsley-validation-threshold="1" data-parsley-trigger="keyup" 
                      data-parsley-type="digits"> <h6 class="seconds">days</h6>
                      </div>
                      
                      <div class="col-sm-5">
                        <input type="text" name="frequencyHours" class="form-control input-hours" id="frequency" placeholder="No. of" value="{{ $settings['frequency']['hours']  }}" @if($settings['frequency']['day'] <= 0) min="1" data-parsley-validation-threshold="1" @endif data-parsley-trigger="keyup" 
                      data-parsley-type="digits"><h6 class="seconds">hours</h6>
                      </div>
                    </div>
                    


                   <div class="form-group clearfix">
                      <label for="gracePeriod" class="side-label bold m-b-0">Grace Period</label>
                      <span class="help-block m-t-5">The time duration in which a patient is allowed to answer a questionnaire after it is Due.</span>
                      <div class="col-sm-5 p-l-0">
                        <input type="text" class="form-control input-days" id="gracePeriod" name="gracePeriodDay" placeholder="No .of" value="{{ $settings['gracePeriod']['day'] }}"  data-parsley-validation-threshold="1" data-parsley-trigger="keyup" 
                      data-parsley-type="digits">
                        <h6 class="seconds">days</h6>
                      </div>
                        <div class="col-sm-5">
                        <input type="text" name="gracePeriodHours" class="form-control input-hours" id="gracePeriodHours" placeholder="No. of" value="{{ $settings['gracePeriod']['hours'] }}" @if($settings['gracePeriod']['day'] <= 0) min="1" data-parsley-validation-threshold="1" @endif data-parsley-trigger="keyup" 
                      data-parsley-type="digits"><h6 class="seconds">hours</h6>
                      </div>
                    </div>
                     

                   <div class="form-group clearfix">
                    <label for="reminderTime" class="side-label bold m-b-0">Reminder Time</label>
                    <span class="help-block m-t-5">Mention the time period before the next occurrence when the reminder notification should be triggered.</span>
                    <div class="col-sm-5 p-l-0">
                      <input type="text" class="form-control input-days" name="reminderTimeDay" id="reminderTime" placeholder="No. of" value="{{ $settings['reminderTime']['day'] }}"  data-parsley-validation-threshold="1" data-parsley-trigger="keyup" 
                    data-parsley-type="digits">
                      <h6 class="seconds">days</h6>
                    </div>
                      <div class="col-sm-5">
                      <input type="text" name="reminderTimeHours" class="form-control input-hours" id="reminderTimeHours" placeholder="No. of" value="{{ $settings['reminderTime']['hours'] }}" @if($settings['reminderTime']['day'] <= 0) min="1" data-parsley-validation-threshold="1" @endif data-parsley-trigger="keyup" 
                    data-parsley-type="digits"><h6 class="seconds">hours</h6>
                    </div>
                  </div>

                  </div>

                  


                  <div class="form-group questActions">
                    <div class="col-sm-12 text-center mri-submit p-t-25">
                    <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
                      <button type="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Save </button>

                      @if($settings['status'] =="published")
                      <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questions-summary/'.$questionnaireId ) }}"><button type="button" class="btn btn-default"> Questions <i class="fa fa-forward" aria-hidden="true"></i></button></a>
                      @elseif($action =="update-questionnaire-setting")
                      <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/configure-questions/'.$questionnaireId ) }}"><button type="button" class="btn btn-default"> Configure Questions <i class="fa fa-forward" aria-hidden="true"></i></button></a>
                      @endif
                    </div>
                  </div>

          </form>
                     
                     </div>
                  </div>
 
<!-- END PLACE PAGE CONTENT HERE -->
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

    $(".questionnaire-setting").find('input').change(function (event) { 
      validatefrequencySettings(true);
    });
});



</script>

@endsection