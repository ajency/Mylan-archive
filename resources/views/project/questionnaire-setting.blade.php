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
         <form class="form-horizontal col-sm-6 mri-form" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}">
  <div class="form-group">
    <label for="frequency" class="col-sm-4 side-label">Frequency</label>
    <div class="col-sm-6">
      <input type="text" name="frequency" class="form-control" id="frequency" placeholder="Frequency" value="{{ $settings['frequency'] }}">
    </div>
    <div class="col-sm-2"><h6 class="seconds">seconds</h6></div>
  </div>
 <div class="form-group">
    <label for="gracePeriod" class="col-sm-4 side-label">Grace Period</label>
    <div class="col-sm-6">
      <input type="text" class="form-control" id="gracePeriod" name="gracePeriod" placeholder="Grace Period" value="{{ $settings['gracePeriod'] }}">
    </div>
    <div class="col-sm-2"><h6 class="seconds">seconds</h6></div>
  </div>
   <div class="form-group">
    <label for="reminderTime" class="col-sm-4 side-label">Reminder Time</label>
    <div class="col-sm-6">
      <input type="text" class="form-control" name="reminderTime" id="reminderTime" placeholder="Reminder Time" value="{{ $settings['reminderTime'] }}">
    </div>
    <div class="col-sm-2"><h6 class="seconds">seconds</h6></div>
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
<select id="type" name="type" style="width:100%" class="">
<option value="random" {{ ($settings['type']=='random')?'selected':'' }}>Random</option>
<option value="sequence" {{ ($settings['type']=='sequence')?'selected':'' }}>Sequence</option>
</select>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-10 text-center mri-submit">
    <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
      <button type="submit" class="btn btn-success">Save</button>
    </div>
  </div>
</form>
                       
                  
                                   
                     </div>
                  </div>
 
<!-- END PLACE PAGE CONTENT HERE -->
@endsection