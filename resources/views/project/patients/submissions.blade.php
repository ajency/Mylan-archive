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

<div class="page-title">
     <h3>Patient Id<span class="semi-bold"> #{{ $patient['reference_code']}}</span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('project.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data active" id="Submissions">
                     <div class="row">
                        <div class="col-sm-8">
                           <h4><span class="semi-bold">Submission Details</span><!--  (Showing 10 recent submissions) --></h4>
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
                        
                                 <br>
                           <table class="table table-flip-scroll table-hover dashboard-tbl">
               <thead class="cf">
                  <tr>
                     <th class="sorting" width="16%">Patient ID <br><br></th>
                     <th class="sorting"># Submission <i class="fa fa-angle-down" style="cursor:pointer;"></i><br><br></th>
                     <th class="sorting">Total Score <br><br></th>
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
                        <h4 class="semi-bold margin-none flagcount">{{ $responseData['occurrenceDate'] }}</h4>
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
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>

<script type="text/javascript">
  var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
  var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 
  </script>
 
@endsection
