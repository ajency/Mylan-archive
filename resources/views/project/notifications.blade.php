@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Notification</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="row">
                  <div class="col-sm-8">
                     <h1>Notifications</h1>
                  </div>
                  <div class="col-sm-4">
                   <form name="searchData" method="GET"> 
                               <input type="hidden" class="form-control" name="startDate"  >
                               <input type="hidden" class="form-control" name="endDate"  >
                                  <div id="reportrange" class="pull-right m-t-10" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
                                     <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                     <span></span> <b class="caret"></b>
                                  </div>

                               </form>
                               <input type="hidden" name="flag" value="0">
                  </div>
             </div>
             <hr class="margin-none">

 @if(!empty($prejectAlerts['alertMsg']))
  @foreach($prejectAlerts['alertMsg'] as $prejectAlert)
 
    <div class="tiles white m-t-10" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/{{ $prejectAlert['URL'] }}';">
   <div class="p-t-20 p-b-15 ">
      <div class="post overlap-left-10">
         <div class="user-profile-pic-wrapper">
            <div class="user-profile-pic-2x tiles grey white-border">
               <div class="text-grey inherit-size p-t-10 p-l-13"> <i class="fa fa-clock-o fa-lg"></i> </div>
            </div>
         </div>
         <div class="info-wrapper small-width">
            <div class="info text-black ">
               <p>Patient <b class="ttuc patient-refer{{ $prejectAlert['patient'] }}">ID  {{ $prejectAlert['patient'] }}</b>
               </p>
               <p class="muted small-text">  {{ $prejectAlert['msg'] }} </p>
            </div>
            <div class="clearfix"></div>
         </div>
       
         <div class="clearfix"></div>
      </div>
   </div>
</div>
  @endforeach
<!--   <div class="tiles grey p-t-10 p-b-10 m-t-20">
      <p class="text-center"> <a href="javascript:;" class="text-black semi-bold  small-text">Load More</a></p>
  </div> -->
@else 
 <div class="text-center text-muted"> <i class="fa fa-bell"></i> No New Notification</div>
@endif

 

                  
<script type="text/javascript">
var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 

</script>   


@endsection

