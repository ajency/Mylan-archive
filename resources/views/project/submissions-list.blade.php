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
               Submission Summary
           </div>
           <table class="table table-flip-scroll dashboard-tbl">
                              <thead class="cf">
                                 <tr> 
                                    <th class="sorting" width="16%">Patient ID</th>
                                    <th class="sorting">Submission#</th>
                                    <th class="sorting" width="22%">Total Score</th>
 
                                    <th class="sorting">Compared To Previous
                                     <br> <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                                      <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                                      <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                                    </th>
                                    <th class="sorting">Compared To Baseline
                                    <br> <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
                                      <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
                                      <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
                                    </th>
                                    
                                 </tr>
                              </thead>
                              <tbody>
                              <?php 
                                $i=1;
                              ?>
                              @foreach($submissionsSummary as $responseId=>$responseData)
                                <?php 
                                  if($i==6)
                                    break;
                                ?>
                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}.'/'.$project['project_slug'].'/submissions/{{$responseId}}';">
                                    <td class="text-center">{{ $responseData['patient'] }}</td>
                                    <td class="text-center">
                                      
                                        <h4 class="semi-bold margin-none flagcount">{{ $responseData['sequenceNumber'] }} on</h4>
                                        <sm>{{ $responseData['occurrenceDate'] }}</sm>
                                    
                                    </td>
                                     <td class="text-center">
                                     <h3 class="bold margin-none pull-left p-l-10">{{ $responseData['totalScore'] }}</h3>
                                     <sm class="text-muted sm-font">Prev - {{ $responseData['previousScore'] }} <i class="fa fa-flag "></i> </sm><br>
                                      <sm class="text-muted sm-font">Base - {{ $responseData['baseLineScore'] }} <i class="fa fa-flag "></i> </sm>
                                    </td>  
                                  
                                     <td class="text-center sorting">
                                     <span class="badge badge-important">{{ count($responseData['previousFlag']['red']) }}</span>
                                      <span class="badge badge-warning">{{ count($responseData['previousFlag']['amber']) }}</span>
                                     <span class="badge badge-success">{{ count($responseData['previousFlag']['green']) }}</span>
                                    </td>   
                                         <td class="text-center sorting">
                                     <span class="badge badge-important">{{ count($responseData['baseLineFlag']['red']) }}</span>
                                      <span class="badge badge-warning">{{ count($responseData['baseLineFlag']['amber']) }}</span>
                                     <span class="badge badge-success">{{ count($responseData['baseLineFlag']['green']) }}</span>
                                    </td>  

                                </tr>
                                <?php 
                                $i++;
                                ?>
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

