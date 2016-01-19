@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li>
          <a href="projects.html">Patients</a>
        </li>
        <li><a href="#" class="active">{{ $patient['reference_code']}}</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div class="page-title">
   <h3>Patient <span class="semi-bold">{{ $patient['reference_code']}}</span></h3>
</div>
<div class="tabbable tabs-left">
                        @include('project.patients.side-menu')
                     <div class="tab-content">
                     <div class="tab-pane table-data active" id="Patients">
                        <div class="row">
                              <div class="col-sm-8">
                                  <dl class="dl-horizontal">
                                 <dt>Reference Code</dt>
                                 <dd>{{ $patient['reference_code']}}</dd>
                                 <dt>Age</dt>
                                 <dd>{{ $patient['age'] }}</dd>
                                 <dt>Weight</dt>
                                 <dd>{{ $patient['patient_weight'] }}</dd>
                                 <dt>Height</dt>
                                 <dd>{{ $patient['patient_height'] }}</dd>
                                 <dt>Smoker</dt>
                                 <dd>{{ $patient['patient_is_smoker'] }}</dd>
                                 @if($patient['patient_is_smoker']=='yes')
                                 <dt>If yes, how many per week</dt>
                                 <dd>{{ $patient['patient_smoker_per_week'] }}</dd>
                                 @endif
                                 <dt>Alcoholic</dt>
                                 <dd>{{ $patient['patient_is_alcoholic'] }}</dd>
                                 @if($patient['patient_is_alcoholic']=='yes')
                                 <dt>Alcohol(units per week)</dt>
                                 <dd>{{ $patient['patient_alcohol_units_per_week'] }}</dd>
                                 @endif
                              </dl>
                              </div>
                              <div class="col-sm-4">
                                    <div class="text-right">
                                       <a href="{{ url($hospital['url_slug'].'/patients/'.$patient['id'].'/edit' ) }}" class="btn btn-white text-success"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                       <!-- <a href="#" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a> -->
                                    </div>
                              </div>
                        </div>
                        <br>
                         <div class="grid simple ">
                        <div class="grid simple grid-table">
                            <div class="grid-title no-border">
                              <a href="patient-submissions.html"> <h4>Response <span class="semi-bold">Rate</span></h4></a>
                            </div>
                        </div>
                   </div>
                           <div class="row">
                              <div class="col-sm-5">
                              <div class="alert alert-info">
                                 <div id="submissionschart"></div>  
                                      <div class="row p-t-20">                                     
                                          
                                             <div class="col-md-6 text-center">
                                                
                                                <h1 class="no-margin">{{  $responseRate['completedRatio'] }}%</h1>
                                                <p class=" text-underline">{{  $responseRate['completed'] }} Submissions Done</p> 
                                                                              
                                             </div>
                                             <div class="col-md-6 text-center">
                                                
                                                                                        
                                                <h1 class="no-margin">{{  $responseRate['missedRatio'] }} %</h1>
                                                <p class="">{{  $responseRate['missed'] }} Submissions Missed</p>                                                          
                                             </div>
                                          </div> 
                              </div>
                              </div>
                              <div class="col-sm-7">
                               <select name="generateChart">
                                      <option value="baseline">Base line flags</option>
                                      <option value="previous">Previous flags</option>
                                       
                                  </select>
                    
                                           <div id="chartdiv"></div>
                              </div>
                           </div>
                           <div>
                             <br><br> 
                                       <div class="grid simple ">
                        <div class="grid simple grid-table">
                            <div class="grid-title no-border">
                               <a href="patient-flag.html">
                               <h4>Open <span class="semi-bold">Red Flags</span></h4></a>
                            </div>
                        </div>
                   </div>
                   <div class="row">
                     <div class="col-sm-8">
                           <table class="table table-flip-scroll cf table-hover">
                                          <thead class="cf">
                                             <tr>
                                                <th>Date</th>
                                                <th>Type of Question</th>
                                                <th>Reason</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             <tr>
                                                <td>8/12/2015</td>
                                                <td>Weight</td>
                                                <td>Current score is worse than previous by +1</td>
                                             </tr>
                                             <tr>
                                                <td>8/12/2015</td>
                                                <td>Diabetes</td>
                                                <td>Current score is worse than baseline by +2</td>
                                             </tr>
                                             <tr>
                                                <td>5/11/2015</td>
                                                <td>Pain</td>
                                                <td>Current score is worse than baseline by +2</td>
                                             </tr>
                                             <tr>
                                                <td>8/10/2015</td>
                                                <td>Diabetes</td>
                                                <td>Current score is worse than baseline by +1</td>
                                             </tr>
                                             <tr>
                                                <td>10/9/2015</td>
                                                <td>Pain</td>
                                                <td>Current score is worse than baseline by +2</td>
                                             </tr>
                                          </tbody>
                                       </table>
                     </div>
                      <div class="col-sm-4">
                       <div class="tiles white added-margin " style="zoom: 1;">
                                            <div class="tiles-body">
                                                <div class="tiles-title"> Recently Generated Flags </div>
                                                <div class="__web-inspector-hide-shortcut__"> <i class="fa fa-sort-asc fa-2x text-error inline p-b-10" style="vertical-align: super;"></i> &nbsp;
                                                    <h1 class="text-error bold inline no-margin"> 5 <i class="fa fa-flag text-error" ></i></h1>
                                                </div>
                                                <br>
                                                <p class="text-black bold">Lorem ipsum dolor sit amet</p>
                                                <hr>
                                                <div class=" p-r-20 p-b-10 p-t-10 b-b b-grey">
                                                   <div class="pull-left">
                                                   <p class="text-success">Open</p>
                                                   <p class="text-black">16/12/2015</p>
                                                   </div>
                                                   <div class="pull-right">
                                                   <p class="text-success">Day Range</p>
                                                   <p class="text-black">15,568.11 - 16,203.25</p>
                                                   </div>
                                                   <div class="clearfix"></div>
                                                   </div>
                                            </div>
                                        </div>
                     </div>
                   </div>
               <!-- <a href="patient-flag.html">    Open Red Flags</a></h4>
                                       <span>( 5 recently generated red flags )</span> -->
                               
                                      
                                    
                                       <br><br>
                    <div class="grid simple ">
                        <div class="grid simple grid-table">
                            <div class="grid-title no-border">
                              <a href="patient-submissions.html"> <h4>Submissions <span class="semi-bold">(5 recent submissions)</span></h4></a>
                            </div>
                        </div>
                   </div>
                                       <!-- submission -->
                              
                                       
                                       <!-- <h6>This patient has missed 5 consecutive submissions</h6> -->
                 
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
                                <?php 
                                $i++;
                                ?>
                            @endforeach
                                  
                              </tbody>
                     </table>

                                       
                                       
                                       <!-- <h6>This patient has missed 5 consecutive submissions</h6> -->
                                      
                                   
                           </div>
                           <br><br>
                           </div>
                        <div class="tab-pane" id="Submissions">
             
                        </div>
                        <div class="tab-pane" id="Reports">
                        </div>
                     </div>
                  </div>
 
    <script type="text/javascript">
     

   $(document).ready(function() {

    patientFlagsChart(<?php echo $flagsCount['baslineFlags'];?>);

    var chart = AmCharts.makeChart( "submissionschart", {
           "type": "pie",
           "theme": "light",
           "dataProvider": [ {
             "title": "# Missed",
             "value": {{ $responseRate['missed'] }}
           }, {
             "title": "# Done",
             "value": {{ $responseRate['completed'] }}
           } ],
           "titleField": "title",
           "valueField": "value",
           "labelRadius": 5,

           "radius": "42%",
           "innerRadius": "60%",
           "labelText": "[[title]]",
           "export": {
             "enabled": true
           }
         } );// Pie Chart

    $('select[name="generateChart"]').change(function (event) { 
      if($(this).val()=='previous')
      { 
        patientFlagsChart(<?php echo $flagsCount['previousFlags'];?>);
      }
      else if($(this).val()=='baseline')
      {
        patientFlagsChart(<?php echo $flagsCount['baslineFlags'];?>);

      }
       

    });

});
 

    
</script>
<!-- END PLACE PAGE CONTENT HERE -->
@endsection