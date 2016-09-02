
@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
$currUrl = $_SERVER['REQUEST_URI'];
?>
<p>
  <ul class="breadcrumb">
    <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
    <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Review Status Mapping</a> </li>
  </ul>
</p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<!-- BEGIN PAGE TITLE -->
<div>            
  <div class="page-title">
    <h3><span class="semi-bold">Mapping</span></h3>
  </div>
</div>
<div class="grid simple">
  <div class="grid-body no-border table-data">
    <br>
    <h3 class="">{{ ucfirst($project['name']) }}</h3>
    <form method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/review-mapping/' ) }}">
      <div class="row">
        <div class="col-xs-4"><input type="text" value="Reviewed - No action" name="reviewed_no_action" readonly="" /></div>
        <div class="col-xs-8"><textarea name="reviewed_no_action-Extra">{{ ($project['reviewed_no_action'])?$project['reviewed_no_action']:'' }}</textarea></div>
      </div>
      <div class="row">  
        <div class="col-xs-4"><input type="text" value="Reviewed - Call done" name="reviewed_call_done" readonly="" /></div>
        <div class="col-xs-8"><textarea name="reviewed_call_done-Extra">{{ ($project['reviewed_call_done'])?$project['reviewed_call_done']:'' }}</textarea></div>
      </div>
       <div class="row">
        <div class="col-xs-4"><input type="text" value="Reviewed - Appointment fixed" name="reviewed_appointment_fixed" readonly="" /></div>
        <div class="col-xs-8"><textarea name="reviewed_appointment_fixed-Extra">{{ ($project['reviewed_appointment_fixed'])?$project['reviewed_appointment_fixed']:'' }}</textarea></div>
      </div>
      <div class="row">
        <div class="col-xs-4"><input type="text" value="Unreviewed" name="unreviewed" readonly="" /></div>
        <div class="col-xs-8"><textarea name="unreviewed-Extra">{{ ($project['unreviewed'])?$project['unreviewed']:'' }}</textarea></div>
      </div> 
       <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
        <button type="submit" class="btn btn-success m-r-15 m-t-10">Save</button>
     </form> 
  </div>
</div>
<!-- END PLACE PAGE CONTENT HERE -->
@endsection