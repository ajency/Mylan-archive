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
 
                              @foreach($submissionsSummary as $responseId=>$responseData)
                 
                                 <tr onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{$responseId}}';">
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
                                 
                            @endforeach
                                  
                              </tbody>
                     </table>
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>


 
@endsection
