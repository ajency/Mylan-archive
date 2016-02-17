@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
         <li>
            <a href="#"> Patients</a>
         </li>
         <li>
            <a href="#"> Submissions</a>
         </li> 
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
                          <sm class="light">( This are scores & flags for current submissions )</sm>
                       </h4>
                       <div class="tools">
                     <form method="get">  
                     <select name="submissionStatus" id="submissionStatus" class=" select2  form-control inline filterby pull-left -m-5">
                        <option value="">Filter By</option>
                        <option {{ ($submissionStatus=='completed')?'selected':'' }} value="completed">Completed</option>
                        <option {{ ($submissionStatus=='late')?'selected':'' }} value="late">Late</option>
                        <option {{ ($submissionStatus=='missed')?'selected':'' }} value="missed">Missed</option>
                     </select>
                     </form>
                     
                  </div>
                    </div>
                    <div class="grid-body no-border" style="display: block;">
                       <table class="table table-flip-scroll table-hover dashboard-tbl">
                          <thead class="cf">
                             <tr>
                                <th class="sorting"># Submission <i class="fa fa-angle-down" style="cursor:pointer;"></i><br><br></th>
                                <th class="sorting">
                                   Total Score
                                   <br> 
                                   <sm>Base <i class="iconset top-down-arrow"></i></sm>
                                   <sm>Prev <i class="iconset top-down-arrow"></i></sm>
                                   <sm>Current <i class="iconset top-down-arrow"></i></sm>
                                </th>
                                <th class="sorting">
                                   Change
                                   <br> 
                                   <sm>δ Base  <i class="iconset top-down-arrow"></i></sm>
                                   <sm>δ Prev  <i class="iconset top-down-arrow"></i></sm>
                                </th>
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
                                <th class="sorting">Status<br><br>
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody>
                            @if(!empty($submissionsSummary))   
                              @foreach($submissionsSummary as $responseId=> $submission)
                                 @if($submission['status']=='missed')
                                    <tr>
                                       <td>
                                         <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                         <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                      </td>
                                      <td class="text-center sorting">
                                         <span>-</span>
                                         <span>-</span>
                                         <span>-</span>
                                      </td>
                                      <td class="text-center">
                                         <h4 class="semi-bold margin-none flagcount">
                                            -
                                         </h4>
                                      </td>
                                      <td class="text-center sorting">
                                          <span class="text-error">-</span>
                                          <span class="text-warning">-</span>
                                          <span class=" text-success">-</span>
                                       </td>
                                       <td class="text-center sorting">
                                          <span class="text-error">-</span>
                                          <span class="text-warning">-</span>
                                          <span class=" text-success">-</span>
                                       </td>
                                      <td class="text-center text-success">-</td>
                                   </tr>
                                 @else 

                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$responseId}}';">
                                    <td>
                                      <h4 class="semi-bold m-0 flagcount">{{ $submission['occurrenceDate'] }}</h4>
                                      <sm><b>#{{ $submission['sequenceNumber'] }}</b></sm>
                                   </td>
                                   <td class="text-center sorting">
                                      <span>{{ $submission['baseLineScore'] }}</span>
                                      <span>{{ $submission['previousScore'] }}</span>
                                      <span>{{ $submission['totalScore'] }}</span>
                                   </td>
                                   <td class="text-center">
                                      <h4 class="semi-bold margin-none flagcount">
                                         <b class="text-{{ $submission['totalBaseLineFlag'] }}">{{ $submission['comparedToBaslineScore'] }}</b> / <b class="f-w text-{{ $submission['totalPreviousFlag'] }}">{{ $submission['comparedToPrevious'] }}</b>
                                      </h4>
                                   </td>
                                   <td class="text-center sorting">
                                       <span class="text-error">{{ $submission['previousFlag']['red'] }}</span>
                                       <span class="text-warning">{{ $submission['previousFlag']['amber'] }}</span>
                                       <span class=" text-success">{{ $submission['previousFlag']['green'] }}</span>
                                    </td>
                                    <td class="text-center sorting">
                                       <span class="text-error">{{ $submission['baseLineFlag']['red'] }}</span>
                                       <span class="text-warning">{{ $submission['baseLineFlag']['amber'] }}</span>
                                       <span class=" text-success">{{ $submission['baseLineFlag']['green'] }}</span>
                                    </td>
                                   <td class="text-center text-success">{{ ucfirst($submission['status']) }}</td>
                                   <td class="text-center text-success">{{ ucfirst($submission['reviewed']) }}</td>
                                </tr>
                                @endif
                        
                            @endforeach
                          @else 
                        <tr><td class="text-center no-data-found" colspan="12"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
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
         $('form').submit();
      });

   });
  </script>
 
@endsection
