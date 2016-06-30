@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > Home</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


      <div class="pull-right m-t-25">
                      <a href="{{ url($hospital['url_slug'].'/patients' ) }}" class="btn btn-white"><span class="text-success"><i class="fa fa-plus"></i> View/Edit Patient</span></a>
                     <a href="{{ url($hospital['url_slug'].'/patients/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Patient</a>
                    
                  </div>
                  <div class="page-title">
                     <h3><span class="semi-bold">Dashboard</span></h3>
                     <p>(Showing intuitive observations at a glance)</p>
                 </div>
                  
                     <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
    
                    
                     <form method="GET"> 
                     <div class="row">
 
                     <div class="col-sm-4">
                     <h3 class="margin-none"><span class="bold">{{ (isset($project['name']))?$project['name']:'' }}</span></h3>
                     </div>
                     <div class="col-sm-3"> <!-- <select name="projectId" >
                        <option>-Select Project-</option>
                        @foreach($allProjects as $allproject)
                        <option {{ ($project['id']==$allproject['id'])?'selected' : '' }}  value="{{ $allproject['id'] }}">{{ $allproject['name'] }}</option>
                        @endforeach
                     </select> --></div>
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
                  <br>
                        <div class="row ">
                           <div class="col-md-2 ">
                              <h1 class="bold num-data">{{ $projectResponseCount['baseLineOpenFlagsCount'] }}  
                                 
                               </h1>
                              <h5>Base Line Open Flags </h5>
                              <em class="line"></em>
                           </div>
                           <div class="col-md-2 ">
                              <h1 class="bold num-data">{{ $projectResponseCount['previousOpenFlagsCount'] }}  
                                 
                               </h1>
                              <h5>Previous Open Flags </h5>
                              <em class="line"></em>
                           </div>
                           <div class="col-md-2 ">
                                <h1 class="bold num-data">{{ $projectResponseCount['totalFlagsCount'] }}  
                                  
                               </h1>
                              <h5>Total Flags </h5>
                              <em class="line"></em>
                           </div>
                           <div class="col-md-3 ">
                                <h1 class="bold num-data">{{ $projectResponseCount['submissionCount'] }} 
                                  
                               </h1>
                              <h5>Total Submissions</h5>
                              <em class="line"></em>
                           </div>
                           <div class="col-md-3">
                               <h1 class="bold num-data">{{ $projectResponseCount['patientsCount'] }}
                                                              
                               </h1>
                              <h5>Total Patients </h5>
                              <em class="line"></em>
                           </div>
                        </div>
                        <br>
                        <!-- Chart - Added -->
                        <br>
                        <div class="row">
                           <div class="col-sm-6"></div>
                           <div class="col-sm-3">
                           </div>
                           <div class="col-sm-3">
                                   <select>
                                      <option value="volvo">No of open flags</option>
                                      <option value="saab">No of total flags</option>
                                      <option value="mercedes">No of submissions</option>
                                      <option value="audi">No of new setups</option>
                                  </select>
                           </div>
                        </div>
                             <div id="chartdiv"></div>  
                        <!-- Chart - Added -->
                        <br>
                        <div class="row">
                           <div class="col-sm-5">
                               <div class="alert alert-info alert-black">
                                 Flags Summary
                              </div>
                     <table class="table table-flip-scroll dashboard-tbl">
                              <thead class="cf">
                                 <tr>
                                    <th>Patient ID</th>
                                    <th>When</th>
                                    <th>Base Line Flag</th>
                                    <th>Previous Flag</th>
                                 </tr>
                              </thead>
                              <tbody>
                              @foreach($patientFlagSummary as $flagSummary)
                                 <tr>
                                    <td class="patient-refer{{ $flagSummary['patient'] }}">{{ $flagSummary['patient'] }}</td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h4 class="text-muted no-margin bold"> {{ $flagSummary['sequenceNumber'] }}<span class="sm text-muted"> On {{ $flagSummary['occurrenceDate'] }} </span></h4>                                        
                                       </div>
                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                       @if($flagSummary['baseLineFlag']=='red')
                                          <i class="fa fa-flag text-error"></i>
                                       @elseif($flagSummary['baseLineFlag']=='green')
                                          <i class="fa fa-flag text-success"></i>
                                       @elseif($flagSummary['baseLineFlag']=='amber')
                                          <i class="fa fa-flag text-warning"></i>
                                       @endif                                          
                                         {{ $flagSummary['baseLineStatus'] }}
                                       </div>
                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20"> 
                                       @if($flagSummary['previousFlag']=='red')
                                          <i class="fa fa-flag text-error"></i>
                                       @elseif($flagSummary['previousFlag']=='green')
                                          <i class="fa fa-flag text-success"></i>
                                       @elseif($flagSummary['previousFlag']=='amber')
                                          <i class="fa fa-flag text-warning"></i>
                                       @endif           
                                         {{ $flagSummary['previousStatus'] }}
                                       </div>
                                    </td>
                                    
                                </tr>
                                @endforeach
                                  
                              </tbody>
                     </table>
                     <hr style="    margin: 0px 0px 10px 0px;">
                       <div class="text-right">
                              <a href="{{ url( $hospital['url_slug'].'/submissions/' ) }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i></a>
                           </div>
                           </div>
                           <div class="col-sm-7">
                                <div class="alert alert-info alert-black">
                                 Submission Summary
                              </div>
                                <table class="table table-flip-scroll dashboard-tbl">
                              <thead class="cf">
                                 <tr>
                                    <th>Patient ID</th>
                                    <th>When</th>
                                    <th>Compared To Previous</th>
                                    <th>Compared To Baseline</th>
                                    <th>Previous Flag Status</th>
                                    <th>Baseline Flag Status</th>
                                 </tr>
                              </thead>
                              <tbody>
                              @foreach($submissionFlags as $submissionFlag)
                                 <tr>
                                    <td class="patient-refer{{ $submissionFlag['patient'] }}">{{ $submissionFlag['patient'] }}</td>
                                    <td width="110px">
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                            <h4 class="text-muted no-margin bold"> {{ $submissionFlag['sequenceNumber'] }} <span class="sm text-muted"> On {{ $submissionFlag['occurrenceDate'] }} </span></h4>
                                       </div>
                                    </td>
                                    <td>
                                      <div class="lst-sub">
                                          <h3 class="bold pull-left">{{ $submissionFlag['baselineScore'] }}</h3>
                                          <div class="pull-left m-t-5">
                                             <span class="sm-font">  {{ count($submissionFlag['baseLineFlag']['green']) }} &nbsp;<i class="fa fa-flag text-success"></i> </span>
                                              <span class="sm-font">{{ count($submissionFlag['baseLineFlag']['red']) }} &nbsp;<i class="fa fa-flag text-error"></i>  </span>
                                               <span class="sm-font">{{ count($submissionFlag['baseLineFlag']['amber']) }} &nbsp;<i class="fa fa-flag text-warning"></i></span>
                                          </div>

                                       </div>
                                    </td>
                                    <td>
                                      <div class="lst-sub">
                                          <h3 class="bold pull-left">{{ $submissionFlag['previousScore'] }}</h3>
                                          <div class="pull-left m-t-5">
                                            <span class="sm-font">  {{ count($submissionFlag['previosFlag']['green']) }} &nbsp;<i class="fa fa-flag text-success"></i> </span>
                                              <span class="sm-font">{{ count($submissionFlag['previosFlag']['red']) }}  &nbsp;<i class="fa fa-flag text-error"></i>  </span>
                                               <span class="sm-font">{{ count($submissionFlag['previosFlag']['amber']) }}  &nbsp;<i class="fa fa-flag text-warning"></i></span>
                                          </div>

                                       </div>
                                    </td>
                                    <td><span class=" text-warning">{{ $submissionFlag['previousFlagStatus'] }}</span></td>
                                    <td><span class=" text-warning">{{ $submissionFlag['baseLineFlagStatus'] }}</span></td>
                                    
                                </tr>
                                @endforeach
                                        
                              </tbody>
                     </table>
                     <hr style="margin: 0px 0px 10px 0px;">
                       <div class="text-right">
                              <a href="{{ url( $hospital['url_slug'].'/submissions/' ) }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i></a>
                           </div>
                           </div>
                        </div>
                        <br>
                          <div class="alert alert-info alert-black">
                              Patients Summary
                          </div>
                         <table class="table table-flip-scroll ">
                              <thead class="cf">
                                 <tr>
                                    <th>Patient ID</th>
                                    <th>Total Submissions</th>
                                    <th>Base Line Green flags</th>
                                    <th>Previous Green flags</th>
                                    <th>Base Line Red Flags</th>
                                    <th>Previous Red Flags</th>
                                    <th>Base Line amber flags</th>
                                    <th>Previous amber flags</th>
                                 </tr>
                              </thead>
                              <tbody>
                              @foreach($patientsSummary as $patientId => $patientSummary)
                                 <tr>
                                    <td class="patient-refer{{ $patientId }}">{{ $patientId }}</td>
                                    <td>
                                       <div class="lst-sub">
                                          <h2 class="bold pull-left">{{ count($patientSummary['count']) }}</h2>
                                          <div class="pull-left m-t-5">
                                             <span class="sm-font">Last Submission  <b>{{ $patientSummary['lastSubmission'] }}</b></span>
                                              <span class="sm-font">Next Submission  <b>{{ $patientSummary['nextSubmission'] }}</b></span>
                                               <span class="sm-font">Total Missed  <b>{{ count($patientSummary['missed']) }}</b></span>
                                          </div>

                                       </div>

                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold">
                                           @if(isset($patientSummary['baseLineFlag']['green']))
                                            {{ count($patientSummary['baseLineFlag']['green']) }}
                                            @else
                                            0
                                           @endif
                                             </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold">
                                            @if(isset($patientSummary['previousFlag']['green']))
                                            {{ count($patientSummary['previousFlag']['green']) }}
                                            @else
                                            0
                                           @endif
                                            </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold">
                                           @if(isset($patientSummary['baseLineFlag']['red']))
                                            {{ count($patientSummary['baseLineFlag']['red']) }}
                                            @else
                                            0
                                           @endif
                                             </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                       <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold">
                                            @if(isset($patientSummary['previousFlag']['red']))
                                            {{ count($patientSummary['previousFlag']['red']) }}
                                            @else
                                            0
                                           @endif
                                            </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                        <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold">
                                            @if(isset($patientSummary['baseLineFlag']['amber']))
                                            {{ count($patientSummary['baseLineFlag']['amber']) }}
                                            @else
                                            0
                                            @endif
                                            </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                    <td>
                                        <div class="p-t-20 p-l-20 p-r-20 p-b-20">
                                           <h3 class="text-muted no-margin bold">
                                            @if(isset($patientSummary['previousFlag']['amber']))
                                            {{ count($patientSummary['previousFlag']['amber']) }}
                                            @else
                                            0
                                            @endif
                                            </h3>
                                          Total Flags {{ count($patientSummary['totalFlags']) }}
                                       </div>
                                    </td>
                                 </tr>
                                 @endforeach
                                    
                              </tbody>
                           </table>
                           <div class="text-right">
                              <a href="{{ url( $hospital['url_slug'].'/submissions/' ) }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i></a>
                           </div>
                      
                         
                      </div>
                     </div>
        <style type="text/css">
        #chartdiv {
            width : 106%;
            height   : 300px;
         }                                                  
      .demo { position: relative; }
      .demo i {
        position: absolute; bottom: 10px; right: 24px; top: auto; cursor: pointer;
      }
      </style>

      <script type="text/javascript">
      $(document).ready(function() {
         $('.input-daterange input').datepicker({
             format: 'dd-mm-yyyy'
         }); 


var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "light",
    "marginRight":80,
    "autoMarginOffset":20,
    "dataDateFormat": "YYYY-MM-DD HH:NN",
    "dataProvider": [ 
    <?php
        
        foreach($projectOpenFlags as $date => $value)
        { 
          ?>
          {
              "date": "<?php echo date('Y-m-d',$date) ?>",
              "base_line":<?php echo $value['baseLine']?>,
              "previous":<?php echo $value['previous'] ?>,
          },
           
          <?php 
            
        }
 
    ?>
 

    ],
    "valueAxes": [{
        "axisAlpha": 0,
        "guides": [{
            "fillAlpha": 0.1,
            "fillColor": "#888888",
            "lineAlpha": 0,
            "toValue": 16,
            "value": 10
        }],
        "position": "left",
        "tickLength": 0
    }],
    "graphs": [{
        "balloonText": "[[category]]<br><b><span style='font-size:14px;'>Base Line:[[base_line]]</span></b>",
        "bullet": "round",
        "dashLength": 3,
        "colorField":"color",
        "valueField": "base_line"
    },
    {
        "balloonText": "[[category]]<br><b><span style='font-size:14px;'>Previous:[[previous]]</span></b>",
        "bullet": "round",
        "dashLength": 3,
        "colorField":"color",
        "valueField": "previous"
    }
    ],
    // "trendLines": [{
    //     "finalDate": "2012-01-11 12",
    //     "finalValue": 19,
    //     "initialDate": "2012-01-02 12",
    //     "initialValue": 10,
    //     "lineColor": "#CC0000"
    // }, {
    //     "finalDate": "2012-01-22 12",
    //     "finalValue": 10,
    //     "initialDate": "2012-01-17 12",
    //     "initialValue": 16,
    //     "lineColor": "#CC0000"
    // }],
    "chartScrollbar": {
        "scrollbarHeight":2,
        "offset":-1,
        "backgroundAlpha":0.1,
        "backgroundColor":"#888888",
        "selectedBackgroundColor":"#67b7dc",
        "selectedBackgroundAlpha":1
    },
    "chartCursor": {
        "fullWidth":true,
        "valueLineEabled":true,
        "valueLineBalloonEnabled":true,
        "valueLineAlpha":0.5,
        "cursorAlpha":0
    },
    "categoryField": "date",
    "categoryAxis": {
        "parseDates": true,
        "axisAlpha": 0,
        "gridAlpha": 0.1,
        "minorGridAlpha": 0.1,
        "minorGridEnabled": true
    },
    "export": {
        "enabled": true
     }
});


      });
      </script>
@endsection