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
<div class="pull-right m-t-10">
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
                    <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/edit' ) }}" class="btn btn-white text-success"><i class="fa fa-pencil-square-o"></i> Edit</a>
                    <!-- <a href="#" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a> -->
                 </div>
              </div>
           </div>
           <br>
           <div class="grid simple ">
              <div class="grid simple grid-table">
                 <div class="grid-title no-border">
                    <a href="patient-submissions.html">
                       <h4>Response <span class="semi-bold">Rate</span></h4>
                    </a>
                 </div>
              </div>
           </div>
           <div class="row">
              <div class="col-sm-5 b-r">
                 <div class="">
                    <div id="submissionschart"></div>
                    <div class="row p-t-20">
                       <div class="col-md-4 text-center">
                          <h3 class="no-margin bold">{{  $responseRate['completed'] }}%</h3>
                          <p class=" text-underline">{{  $responseRate['completedCount'] }} Submissions Completed</p>
                       </div>
                       <div class="col-md-4 text-center">
                          <h3 class="no-margin bold">{{  $responseRate['late'] }}%</h3>
                          <p class="">{{  $responseRate['lateCount'] }} Submissions Late</p>
                       </div>
                       <div class="col-md-4 text-center">
                          <h3 class="no-margin bold">{{  $responseRate['missed'] }}%</h3>
                          <p class="">{{  $responseRate['missedCount'] }} Submissions Missed</p>
                       </div>
                    </div>
                 </div>
              </div>
              <!--      <div class="col-sm-1 ">
                 </div> -->
              <div class="col-sm-7">
                 <select class="pull-right" name="generateChart">
                    <option value="total_score" selected>Total Score</option>
                    <option value="red_flags" > Red Flags</option>
                    <option value="amber_flags">  Amber Flags</option>
                    <option value="green_flags">Green Flags</option>
                 </select>
                 <div id="chartdiv"></div>
              </div>
           </div>
           <h4>Patient health chart</h4>
           <p>Patient health chart shows the comparison between
              1.Patients current score to baseline score- indicated by highlighted cell
              2.Patients current score to previous score indicated by flag
              Red-indicates current score is worse then previous/baseline score by 2 points
              Amber-indicates current score is worse then previous/baseline score by 1 point
              Green-indicates current score is better then previous/baseline score
              White-Indicates current score is same as baseline score.
              Flag is not displayed if the current score is same as previous score
           </p>
           <br><br>
            <div class="tableOuter">
            <div class="x-axis-text">Submissions</div>
           <div class="y-axis-text">Questions</div>
           <div class="table-responsive sticky-table-outer-div {{(count($responseArr)>10)?'sticky-tableWidth':''}}"> 
           
         <table class="table">
                        <thead class="cf">
                           <tr>
                              <th class="headcol th-headcol"></th>
                              @foreach($responseArr as $response)
                              <th>{{ $response['DATE'] }} ({{ $response['SUBMISSIONNO'] }})</th>
                              @endforeach
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($flagsQuestions as $questionId => $question)
                           <tr>
                              <td class="headcol">{{ $question }}</td>
                              @foreach($responseArr as $responseId => $response)
                              <?php
                              if(isset($submissionFlags[$responseId][$questionId]))
                              {
                                $class='bg-'.$submissionFlags[$responseId][$questionId]['baslineFlag'];
                                $flag= ($submissionFlags[$responseId][$questionId]['previousFlag']=='no_colour' || $submissionFlags[$responseId][$questionId]['previousFlag']=='')?'hidden':'text-'.$submissionFlags[$responseId][$questionId]['previousFlag'];
                              }
                              else
                              {
                                $class='bg-gray';
                                $flag = 'hidden';
                              }
                              ?>
                              <td class="{{ $class }}"><i class="fa fa-flag {{ $flag }}" ></i></td>
                   
                              @endforeach
                              
                           </tr>
                        @endforeach
                           
                        </tbody>
                     </table>
                     </div>
           </div>             
           <div>
               
              <hr>
              <br>
              <div class="pull-left">
                 <h4 class="bold">Questionnaire Graph</h4>
              </div>
              <select class="pull-right" name="generateQuestionChart">
                    @foreach($questionLabels as $questionId =>$question)
                    <option value="{{ $questionId }}" >{{ $question }}</option>
                    @endforeach
                 </select>
              <div id="questionChart" class="p-t-20" style="width:100%; height:400px;"></div>
              <br><br> 
              <div>
                 <div class="grid simple grid-table">
                    <div class="grid-title no-border">
                       <h4>
                          Submissions <span class="semi-bold">Summary</span> 
                          <sm class="light">( This are scores & flags for current submissions )</sm>
                       </h4>
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
                       <hr style="margin: 0px 0px 10px 0px;">
                       <div class="text-right {{ (empty($submissionsSummary))?'hidden':'' }}">
                          <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/submissions') }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
           <!-- <a href="patient-flag.html">    Open Red Flags</a></h4>
              <span>( 5 recently generated red flags )</span> -->
           <!--    <div class="grid simple ">
              <div class="grid simple grid-table">
                  <div class="grid-title no-border">
                    <a href="patient-submissions.html"> <h4>Submissions <span class="semi-bold">(5 recent submissions)</span></h4></a>
                  </div>
              </div>
              </div> -->
           <!-- submission -->
           <div class="grid simple grid-table">
              <div class="grid-title no-border">
                 <h4><span class="semi-bold">FLAGS</span></h4>
              </div>
              <div class="row">
                 <div class="col-sm-12">
                    <table class="table table-hover dashboard-tbl">
                       <thead>
                          <tr>
                             <th width="30%"># Submission</th>
                             <th>Reason for Flag</th>
                             <th>Type</th>
                          </tr>
                       </thead>
                       <tbody>
                       <?php 
                          $i=1;
                        ?>
                        @if(!empty($patientFlags['all']))      
                           @foreach($patientFlags['all'] as $allSubmissionFlag)
                         <?php 
                          if($allSubmissionFlag['flag']=='no_colour' || $allSubmissionFlag['flag']=='')
                               continue;
                           
                            if($i==6)
                              break;
                          ?>
                         <tr class="odd gradeX" onclick="window.document.location='/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/{{ $allSubmissionFlag['responseId'] }}';">
                            <td width="110px">
                               <div class="p-l-10 p-r-20">
                                  <h4 class="semi-bold m-0 flagcount">{{ $allSubmissionFlag['date'] }}</h4>
                                  <sm>#{{ $allSubmissionFlag['sequenceNumber'] }}</sm>
                               </div>
                            </td>
                            <td>{{ $allSubmissionFlag['reason'] }}</td>
                            <td><i class="fa fa-flag text-{{ $allSubmissionFlag['flag'] }}"></i></td>
                         </tr>
                         <?php 
                          $i++;
                          ?>
                        @endforeach 
                       @else 
                        <tr><td class="text-center no-data-found" colspan="12"><i class="fa fa-2x fa-frown-o"></i><br>No data found</td></tr>
                        @endif 
                    </table>
                    <hr style="margin: 0px 0px 10px 0px;">
                    <div class="text-right {{ (empty($patientFlags['all']))?'hidden':'' }}">
                       <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/flags') }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
                    </div>
                 </div>
              </div>
              <!-- <h6>This patient has missed 5 consecutive submissions</h6> -->
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
 
 <?php 

$questionId = current(array_keys($questionLabels));
$inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
$questionLabel = (isset($questionLabels[$questionId]))?$questionLabels[$questionId]:'';
//$baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;


?>
    <script type="text/javascript">
    var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
    var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 

   $(document).ready(function() {

    // submission chart
    var legends = {score: "Total Score",baseLine: "Base Line"};
    lineChartWithOutBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,"chartdiv",'Submissions','Total Score');
    //lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,<?php echo $flagsCount['baslineScore'];?>,"chartdiv")

    //question chart
    shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$questionLabel}}',0,'questionChart','Submissions','Score');
 
    var chart = AmCharts.makeChart( "submissionschart", {
                 "type": "pie",
                 "theme": "light",
                 "dataProvider": [ {
                   "title": "# Missed",
                   "value": {{ $responseRate['missedCount'] }}
                 }, {
                   "title": "# Completed",
                   "value": {{ $responseRate['completedCount'] }}
                 } 
                 , {
                   "title": "# late",
                   "value": {{ $responseRate['lateCount'] }}
                 } ],
                 "titleField": "title",
                 "valueField": "value",
                 "labelRadius": 5,
         
                 "radius": "30%",
                 "innerRadius": "48%",
                 "labelText": "[[title]]",
                 "export": {
                   "enabled": true
                 }
               } );// Pie Chart
         
        

    $('select[name="generateChart"]').change(function (event) { 
      if($(this).val()=='total_score')
      { 
       // legends = {score: "Total Score"};
       //  lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,<?php echo $flagsCount['baslineScore'];?>,"chartdiv")
       var legends = {score: "Total Score",baseLine: "Base Line"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,"chartdiv",'Submissions','Total Score');
      }
      else if($(this).val()=='red_flags')
      { 
        legends = {Baseline: "Baseline",Previous: "Previous"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['redFlags'];?>,legends,"chartdiv",'Submissions','Total Red Flags');
      }
      else if($(this).val()=='amber_flags')
      {
        legends = {Baseline: "Baseline",Previous: "Previous"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['amberFlags'];?>,legends,"chartdiv",'Submissions','Total Amber Flags');

      }
      else if($(this).val()=='green_flags')
      {
        legends = {Baseline: "Baseline",Previous: "Previous"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['greenFlags'];?>,legends,"chartdiv",'Submissions','Total Green Flags');

      } 

    });

     $('select[name="generateQuestionChart"]').change(function (event) { 
      <?php 
      foreach($questionLabels as $questionId => $label)
      {
        $inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
        //$baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;
        ?>
        if($(this).val()=='{{$questionId}}')
        { 
          shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$label}}',0,'questionChart','Submissions','Score');
        }

        <?php
      }
      ?>

    });
 
});
 

    
</script>

<!-- END PLACE PAGE CONTENT HERE -->
@endsection