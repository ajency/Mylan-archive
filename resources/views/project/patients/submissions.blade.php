@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
 
          <li><a href="#">Patients</a></li>
        <li><a href="#">{{ $patient['reference_code']}}</a> </li>
        <li><a href="#" class="active">Submissions</a> </li>
 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<div class="pull-right">
  <a href="add-patient.html" class="hidden btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Patient</a>
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
<div class="page-title">
     <h3>Patient Id<span class="semi-bold"> #{{ $patient['reference_code']}}</span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('project.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data active" id="Submissions">
                        <div class="grid simple grid-table">
                    <div class="grid-title no-border">
                       <h4>
                          Submissions <span class="semi-bold">Summary</span> 
                          <sm class="light">(These are scores & flags for current submissions)</sm>
                       </h4>
                       <div class="tools">
                       <label class="filter-label">Filter</label>
                     <form method="get">  
                     <select name="submissionStatus" id="submissionStatus" class=" select2  form-control inline filterby pull-left -m-5">
                        <option value="all">All</option>
                        <option {{ ($submissionStatus=='completed')?'selected':'' }} value="completed">Completed</option>
                        <option {{ ($submissionStatus=='late')?'selected':'' }} value="late">Late</option>
                        <option {{ ($submissionStatus=='missed')?'selected':'' }} value="missed">Missed</option>
                     </select>
                     <span class="cf-loader hidden m-t-3 submissionFilter"></span>
                     </form>
                     
                  </div>
                    </div>
                    <div class="grid-body no-border" style="display: block;">
                       <table class="table table-flip-scroll table-hover dashboard-tbl">
                          <thead class="cf">
                             <tr>
                                <th class="sorting sortSubmission" sort="sequenceNumber" sort-type="asc"  style="cursor:pointer;"># Submission <i class="fa fa-angle-down sortCol"></i><br><br></th>
                                <th colspan="3" class="sorting">
                                   Total Score
                                   <br> 
                                   <sm class="sortSubmission" sort="baseLineScore" sort-type="asc">Base <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="sortSubmission" sort="previousScore" sort-type="asc">Prev <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="sortSubmission" sort="totalScore" sort-type="asc">Current <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Change
                                   <br> 
                                   <sm class="sortSubmission" sort="comparedToBaseLine" sort-type="asc">δ Base  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="sortSubmission" sort="comparedToPrevious" sort-type="asc">δ Prev  <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Previous
                                   <br> 
                                   <sm class="pull-left sortSubmission" sort="previousTotalRedFlags" sort-type="asc" style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm style="position: relative; bottom: 2px;" class="sortSubmission" sort="previousTotalAmberFlags" sort-type="asc"><i class="fa fa-flag text-warning"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="pull-right sortSubmission" sort="previousTotalGreenFlags" sort-type="asc" style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th colspan="3" class="sorting">
                                   Baseline
                                   <br> 
                                   <sm class="pull-left sortSubmission" sort="baseLineTotalRedFlags" sort-type="asc" style="margin-left: 20px"><i class="fa fa-flag text-error"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm style="position: relative; bottom: 2px;"  class="sortSubmission" sort="baseLineTotalAmberFlags" sort-type="asc"><i class="fa fa-flag text-warning"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                   <sm class="pull-right sortSubmission" sort="baseLineTotalGreenFlags" sort-type="asc" style="margin-right: 20px"><i class="fa fa-flag text-success"></i>  <i class="fa fa-angle-down sortCol"></i></sm>
                                </th>
                                <th class="sorting">Status<br><br>
                                </th>
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody id="submissionData" limit="" object-type="patient-submission" object-id="{{ $patient['reference_code']}}">
                          <div class="loader-outer hidden">
                            <span class="cf-loader"></span>
                         </div>
                            @if(!empty($submissionsSummary))   
                              @foreach($submissionsSummary as $responseId=> $submission)
                                 @if($submission['status']=='missed')
                                    <tr>
                                       <td>
                                         <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                         <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                      </td>
                                    
                                       <td class="text-right sorting">0</td>
                                       <td class="text-center sorting">0</td>
                                       <td class="text-left sorting">0</td>
                                      
                                      <td class="text-right semi-bold margin-none flagcount p-h-0">
                                         <h4>
                                            -
                                         </h4>
                                      </td>
                                      <td class="text-center semi-bold margin-none flagcount p-h-0">
                                         <h4>
                                            <b>/</b>
                                         </h4>
                                      </td>
                                      <td class="text-left semi-bold margin-none flagcount p-h-0">
                                         <h4>
                                            -
                                         </h4>
                                      </td>
                                      
                                      <td class="text-right sorting text-error">0</td>
                                      <td class="text-center sorting text-warning">0</td>
                                      <td class="text-left sorting  text-success">0</td>
                                   
                                      <td class="text-right sorting text-error">0</td>
                                      <td class="text-center sorting text-warning">0</td>
                                      <td class="text-left sorting  text-success">0</td>

                                      <td class="text-center text-success">-</td>
                                      <td class="text-center text-success">-</td>
                                   </tr>
                                 @else 

                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$responseId}}';">
                                    <td>
                                      <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                   </td>
                               
                                    <td class="text-right sorting">{{ $submission['baseLineScore'] }}</td>
                                    <td class="text-center sorting">{{ $submission['previousScore'] }}</td>
                                    <td class="text-left sorting">{{ $submission['totalScore'] }}</td>
                                 
                                    <td class="text-right semi-bold margin-none flagcount p-h-0">
                                      <h4><b class="text-{{ $submission['totalBaseLineFlag'] }}">{{ $submission['comparedToBaslineScore'] }}</b></h4>
                                    </td>  
                                    <td class="text-center semi-bold margin-none flagcount p-h-0">
                                       <h4><b>/</b></h4>                                      
                                     </td>
                                     <td class="text-left semi-bold margin-none flagcount p-h-0">
                                        <h4> <b class="f-w text-{{ $submission['totalPreviousFlag'] }}">{{ $submission['comparedToPrevious'] }}</b></h4>
                                     </td>
                                  
                                     <td class="text-right sorting text-error">{{ $submission['previousFlag']['red'] }}</td>
                                     <td class="text-center sorting text-warning">{{ $submission['previousFlag']['amber'] }}</td>
                                     <td class="text-left sorting text-success">{{ $submission['previousFlag']['green'] }}</td>
                                  
                                     <td class="text-right sorting text-error">{{ $submission['baseLineFlag']['red'] }}</td>
                                     <td class="text-center sorting text-warning">{{ $submission['baseLineFlag']['amber'] }}</td>
                                     <td class="text-left sorting text-success">{{ $submission['baseLineFlag']['green'] }}</td>
                                   
                                   <td class="text-center text-success">{{ ucfirst($submission['status']) }}</td>
                                   <td class="text-center text-success">{{ ucfirst($submission['reviewed']) }}</td>
                                </tr>
                                @endif
                        
                            @endforeach
                          @else 
                        <tr><td class="text-center no-data-found" colspan="15"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif      
                                
                          </tbody>
                       </table>
                     
                    </div>
                 </div>
 
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>

<script type="text/javascript">
  var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
  var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 


   $(document).ready(function() {

      $('select[name="submissionStatus"]').change(function (event) { 
        $(".submissionFilter").removeClass('hidden');
         $('form').submit();
      });

   });
  </script>
 
@endsection
