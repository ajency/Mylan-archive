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
                 
                                       <table class="table table-hover table-flip-scroll cf">
                                          <thead class="cf">
                                             <tr>
                                                <th>Submission Date</th>
                                                <th>Sequence Number</th>
                                                <th>Number of Flags</th>
                                                <th>Open Flags</th>
                                                <th>Status</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                       <tr onclick="window.document.location='p1-submission6.html';">
                                          <td>6/12/2015</td>
                                          <td>12</td>
                                          <td>0</td>
                                          <td>0</td>
                                          <td><span class="label label-warning">Pending Review</span></td>
                                       </tr>                                       
                                       <tr onclick="window.document.location='p1-submission5.html';">
                                          <td>6/7/2015</td>
                                          <td>7</td>
                                          <td>0</td>
                                          <td>0</td>
                                          <td><span class="label label-success">Reviewed</span></td>
                                       </tr>

                                       <tr onclick="window.document.location='p1-submission4.html';">
                                          <td>6/6/2015</td>
                                          <td>6</td>
                                          <td>6</td>
                                          <td>5</td>
                                          <td><span class="label label-success">Reviewed</span></td>
                                       </tr>
                                       <tr onclick="window.document.location='p1-submission3.html';">
                                          <td>6/5/2015</td>
                                          <td>5</td>
                                          <td>4</td>
                                          <td>3</td>
                                          <td><span class="label label-success">Reviewed</span></td>
                                       </tr>                                       
                                       <tr onclick="window.document.location='p1-submission2.html';">
                                          <td>6/2/2015</td>
                                          <td>2</td>
                                          <td>3</td>
                                          <td>2</td>
                                          <td><span class="label label-success">Reviewed</span></td>
                                       </tr>
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