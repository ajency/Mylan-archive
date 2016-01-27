@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="#" class="active">Submissions</a>
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
       <div class="row">
         <div class="col-sm-8">
           
       </div>
       <div class="col-sm-4 m-t-10">
         <form name="searchData" method="GET"> 
         <input type="hidden" class="form-control" name="startDate"  >
         <input type="hidden" class="form-control" name="endDate"  >
            <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
               <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
               <span></span> <b class="caret"></b>
            </div>

         </form>
         <input type="hidden" name="flag" value="0">
          </div>
      </div>
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
               Submission Summary
           </div>
           <table class="table table-flip-scroll table-hover dashboard-tbl">
               <thead class="cf">
                  <tr>
                     <th class="sorting" width="16%">Patient ID <br><br></th>
                     <th class="sorting"># Submission <i class="fa fa-angle-down" style="cursor:pointer;"></i><br><br></th>
                     <th class="sorting" width="16%">Total Score <br><br></th>
                     <th class="sorting">
                        Previous
                        <br> 
                        <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                     </th>
                     <th class="sorting">
                        Baseline
                        <br> 
                        <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                        <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                     </th>
                  </tr>
               </thead>
               <tbody>
        
                @foreach($submissionsSummary as $responseId=>$responseData)
         
             
                  <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$responseId}}';">
                     <td class="text-center">{{ $responseData['patient'] }}</td>
                     <td class="text-center">
                        <h4 class="semi-bold m-0 flagcount">{{ $responseData['occurrenceDate'] }}</h4>
                        <sm>Seq - {{ $responseData['sequenceNumber'] }}</sm>
                     </td>
                     <td class="text-center">
                        <h3 class="bold margin-none pull-left p-l-10">{{ $responseData['totalScore'] }}</h3>
                        <sm class="text-muted sm-font m-t-10">Prev - {{ $responseData['previousScore'] }}  <i class="fa fa-flag "></i> </sm>
                        <br>
                        <sm class="text-muted sm-font">Base - {{ $responseData['baseLineScore'] }} <i class="fa fa-flag "></i> </sm>
                     </td>
                     <td class="text-center sorting">
                        <span class="text-error">{{ count($responseData['previousFlag']['red']) }}</span>
                        <span class="text-warning">{{ count($responseData['previousFlag']['amber']) }}</span>
                        <span class=" text-success">{{ count($responseData['previousFlag']['green']) }}</span>
                     </td>
                     <td class="text-center sorting">
                        <span class="text-error">{{ count($responseData['baseLineFlag']['red']) }}</span>
                        <span class="text-warning">{{ count($responseData['baseLineFlag']['amber']) }}</span>
                        <span class=" text-success">{{ count($responseData['baseLineFlag']['green']) }}</span>
                     </td>
                  </tr>
            
                @endforeach
               </tbody>
            </table>
            
           <hr style="    margin: 0px 0px 10px 0px;">
       </div>
   </div>
</div>
                  
<script type="text/javascript">
var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 
</script>   


@endsection

