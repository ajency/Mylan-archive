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
                        <h3><span class="semi-bold">Settings</span></h3>
                     </div>
                  </div>
                                   
                   
                          
                           <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                       <h3 class="">{{ $project['name'] }}</h3>
                      
                      <hr>
          @include('admin.flashmessage')

            <form class="form-horizontal col-sm-9 mri-form setQuestion" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/'.$action.'/' ) }}" data-parsley-validate onsubmit="return validatefrequencySettings(true);">

                  <div class="form-group">
                    <label for="editable" class="col-sm-4 side-label">Name</label>
                    <div class="col-sm-6">
                    <input type="text" name="name" id="name" class="nameField" value="{{ $settings['name'] }}" data-parsley-required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="editable" class="col-sm-4 side-label">Editable</label>
                    <div class="col-sm-6">
                    <div class="radio">
                    <input id="YES" type="radio" name="editable" value="yes" checked="checked">
                    <label for="YES">Yes</label>
                    <input id="NO" type="radio" name="editable" value="no" {{ ($settings['editable']==false)?'checked':'' }}>
                    <label for="NO">No</label>
                    </div>
                    </div>
                  </div>

                   <div class="form-group">
                    <label for="Frequency" class="col-sm-4 side-label">Questionnaire Type</label>
                    <div class="col-sm-6">
                    <select id="type" name="type" style="width:100%" class="" data-parsley-required>
                    <!-- <option value="random" {{ ($settings['type']=='random')?'selected':'' }}>Random</option> -->
                    <option value="sequence" {{ ($settings['type']=='sequence')?'selected':'' }}>Sequence</option>
                    </select>
                    </div>
                  </div>
                  @if($settings['status'] =="published")
                  <div class="form-group">
                    <label for="Frequency" class="col-sm-4 side-label">Pause Project</label>
                    <div class="col-sm-6">
                      <select id="pauseProject" name="pauseProject" style="width:100%" class="">
                      <option value="yes" {{ ($settings['pauseProject']=='yes')?'selected':'' }}>Yes</option>
                      <option value="no" {{ ($settings['pauseProject']=='no')?'selected':'' }}>No</option>
                      </select>
                    </div>
                  </div>  
                  @endif
                    <div class="form-group">
                      <label for="frequency" class="col-sm-4 side-label">Frequency</label>
                      <div class="col-sm-3">
                        <input type="text" name="frequencyDay" class="form-control input-days" id="frequency" placeholder="Frequency" value="{{ $settings['frequency']['day'] }}"  data-parsley-validation-threshold="1" data-parsley-trigger="keyup" 
                      data-parsley-type="digits"> <h6 class="seconds">days</h6>
                      </div>
                      
                      <div class="col-sm-3">
                        <input type="text" name="frequencyHours" class="form-control input-hours" id="frequency" placeholder="Frequency" value="{{ $settings['frequency']['hours']  }}" @if($settings['frequency']['day'] <= 0) min="1" data-parsley-validation-threshold="1" @endif data-parsley-trigger="keyup" 
                      data-parsley-type="digits"><h6 class="seconds">hours</h6>
                      </div>
                    </div>


                   <div class="form-group">
                      <label for="gracePeriod" class="col-sm-4 side-label">Grace Period</label>
                      <div class="col-sm-3">
                        <input type="text" class="form-control input-days" id="gracePeriod" name="gracePeriodDay" placeholder="Grace Period" value="{{ $settings['gracePeriod']['day'] }}"  data-parsley-validation-threshold="1" data-parsley-trigger="keyup" 
                      data-parsley-type="digits">
                        <h6 class="seconds">days</h6>
                      </div>
                        <div class="col-sm-3">
                        <input type="text" name="gracePeriodHours" class="form-control input-hours" id="gracePeriodHours" placeholder="Grace Period" value="{{ $settings['gracePeriod']['hours'] }}" @if($settings['gracePeriod']['day'] <= 0) min="1" data-parsley-validation-threshold="1" @endif data-parsley-trigger="keyup" 
                      data-parsley-type="digits"><h6 class="seconds">hours</h6>
                      </div>
                    </div>

                   <div class="form-group">
                    <label for="reminderTime" class="col-sm-4 side-label">Reminder Time</label>
                    <div class="col-sm-3">
                      <input type="text" class="form-control input-days" name="reminderTimeDay" id="reminderTime" placeholder="Reminder Time" value="{{ $settings['reminderTime']['day'] }}"  data-parsley-validation-threshold="1" data-parsley-trigger="keyup" 
                    data-parsley-type="digits">
                      <h6 class="seconds">days</h6>
                    </div>
                      <div class="col-sm-3">
                      <input type="text" name="reminderTimeHours" class="form-control input-hours" id="reminderTimeHours" placeholder="Reminder Time" value="{{ $settings['reminderTime']['hours'] }}" @if($settings['reminderTime']['day'] <= 0) min="1" data-parsley-validation-threshold="1" @endif data-parsley-trigger="keyup" 
                    data-parsley-type="digits"><h6 class="seconds">hours</h6>
                    </div>
                  </div>

                  <div class="form-group questActions">
                    <div class="col-sm-10 text-center mri-submit p-t-25">
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
});

</script>

@endsection