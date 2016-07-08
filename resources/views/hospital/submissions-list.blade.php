@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Submissions</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


<div class="page-title">
   <h3><span class="semi-bold">Submissions</span> </h3>
</div>

<div class="grid simple">
   <div class="grid-body no-border table-data">
       <br>
       <form method="GET"> 
       <div class="row">
           <!-- <div class="col-sm-6"> <h3 class="bold margin-none">Submissions</h3></div> -->
           <div class="col-sm-7">
           </div>
           <div class="col-sm-5">
           <div class="row">
           <div class="col-sm-9">
               <div class="input-group input-daterange">
                   <input type="text" class="form-control" name="startDate" value="{{ $startDate }}">
                   <span class="input-group-addon">to</span>
                   <input type="text" class="form-control" name="endDate" value="{{ $endDate }}">
               </div>
               </div>
               <div class="col-sm-3">
               <button class="btn btn-default">Submit</button>
               </div>
               </div>
           </div>
       </div>
       </form>
       <hr>
         <div class="row ">
           <div class="col-md-4 ">
               <div class="tiles white added-margin light-gray" style="zoom: 1;">
                   <div class="tiles-body">
                       <div class="tiles-title"> Submissions </div>
                       <div class="heading"> <span class="animate-number" data-value="{{ $completedSubmissionCount }}" data-animation-duration="1200">{{ $completedSubmissionCount }}</span> </div>
                       <div class="progress transparent progress-small no-radius">
                           <div class="progress-bar progress-bar-black animate-progress-bar" data-percentage="{{ $completedSubmissionCount }}%" style="width: {{ $completedSubmissionCount }}%;"></div>
                       </div>
                       <h5 class="text-black"><b>{{ $openStatus }}</b> Open / <b>{{ $closedStatus }}</b> Closed</span></h5>
                   </div>
               </div>
           </div>
           <div class="col-md-4 ">
               <div class="tiles white added-margin light-gray" style="zoom: 1;">
                   <div class="tiles-body">
                       <div class="tiles-title"> Response Rate </div>
                       <div class="heading"> <span class="animate-number" data-value="{{ $responseRate }}%" data-animation-duration="1200">{{ $responseRate }} %</div>
         <div class="progress transparent progress-small no-radius">
         <div class="progress-bar progress-bar-black animate-progress-bar" data-percentage="{{ $responseRate }}%" style="width: {{ $responseRate }}%;"></div>
         </div>
         <h5 class="text-black"><b>{{ $completedSubmissionCount }}</b> Submitted / <b>{{ $missedResponses }}</b> Missed</span></h5>
                       </div>
                   </div>
               </div>
               <div class="col-md-4 ">
                   <div class="tiles white added-margin " style="zoom: 1;">
                       <div class="tiles-body">
                           <div class="tiles-title"> Avg Review Time </div>
                           <div class="__web-inspector-hide-shortcut__"> <i class="fa fa-sort-asc fa-2x text-error inline p-b-10" style="vertical-align: super;"></i> &nbsp;
                               <h1 class="text-error bold inline no-margin"> {{ $avgReviewTime }} hrs</h1>
                           </div>
                           <p class="text-black">Lorem ipsum dolor sit amet</p>
                           <br>
                       </div>
                   </div>
               </div>
           </div>  
           <br>
           <!-- Chart - Added -->
           <br>
           <div class="alert alert-info alert-black">
               Patients Summary
           </div>
           <table class="table table-flip-scroll tbl-summary table-hover">
               <thead class="cf">
                   <tr>
                       <th>Patient ID</th>
                       <th><span class="p-l-13 p-r-13">When</span></th>
                       <th>Compared To Previous</th>
                       <th>Compared To Baseline</th>
                       <th>Previous Flag Status</th>
                       <th>Baseline Flag Status</th>
                   </tr>
               </thead>
               <tbody>
                @foreach($submissionFlags as $responseId => $submissionFlag)
                   <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/submissions/{{$responseId}}';">
                       <td class="ttuc patient-refer{{ $submissionFlag['patient'] }}">{{ $submissionFlag['patient'] }}</td>
                       <td width="110px">
                           <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                               <h4 class="text-muted no-margin bold"> {{ $submissionFlag['sequenceNumber'] }} <span class="sm text-muted"> On {{ $submissionFlag['occurrenceDate'] }} </span></h4>
                           </div>
                       </td>
                       <td>
                           <div class="lst-sub">
                               <h3 class="bold ">
                     {{ $submissionFlag['baselineScore'] }}
                     <span class="sm">  
                      {{ count($submissionFlag['baseLineFlag']['green']) }} &nbsp;<i class="fa fa-flag text-success"></i> &nbsp;/&nbsp;
                      {{ count($submissionFlag['baseLineFlag']['red']) }}  &nbsp;<i class="fa fa-flag text-warning"></i> &nbsp;/&nbsp;
                       {{ count($submissionFlag['baseLineFlag']['amber']) }} &nbsp;<i class="fa fa-flag text-error"></i> 
                     </span>
                     </h3>
                           </div>
                       </td>
                       <td>
                           <div class="lst-sub">
                               <h3 class="bold ">
                    {{ $submissionFlag['previousScore'] }} 
                     <span class="sm">  
                     {{ count($submissionFlag['previosFlag']['green']) }} &nbsp;<i class="fa fa-flag text-success"></i> &nbsp;/&nbsp;
                      {{ count($submissionFlag['previosFlag']['red']) }} &nbsp;<i class="fa fa-flag text-warning"></i> &nbsp;/&nbsp;
                       {{ count($submissionFlag['previosFlag']['amber']) }} &nbsp;<i class="fa fa-flag text-error"></i> 
                     </span>
                     </h3>
                           </div>
                       </td>
                       <td><span class=" text-warning">{{ $submissionFlag['previousFlagStatus'] }}</span></td>
                       <td><span class=" text-warning">{{ $submissionFlag['baseLineFlagStatus'] }}</span></td>
                   </tr>
                   @endforeach
                    
               </tbody>
           </table>
           <hr style="    margin: 0px 0px 10px 0px;">
       </div>
   </div>
</div>
                  
         <script type="text/javascript">
      $(document).ready(function() {
         $('.input-daterange input').datepicker({
             format: 'dd-mm-yyyy'
         }); 
         }); 
         </script>   
 

@endsection

