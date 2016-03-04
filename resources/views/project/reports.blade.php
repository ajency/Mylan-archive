@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="#" class="active">Reports</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
  
   
   <div class="row">
     <div class="col-sm-4">
        <div class="page-title">
          <h3><span class="semi-bold">Patient</span>  Report Highlights</h3>
        </div>
     </div>
     <div class="col-sm-8 pull-right">
       <div class="m-t-10">
       <div class="patient-search pull-right">
        <form name="patientFilter" method="GET"> 
         <select class="selectpicker pull-right" data-live-search="true" title="Patient" name="referenceCode">
             @foreach($allPatients as $patient)
               <option {{ ($referenceCode==$patient)?'selected':''}} value="{{ $patient }}">{{ $patient }}</option>
             @endforeach
            </select> 
         </form>
 
              </div>
       

     
       
     
   </div>
     </div>
   </div>

                  <div class="grid simple">
                        <div class="grid-body no-border table-data">
                                
       
      <div class="tab-pane table-data active"  >
      <br>
      <div class="row">
         <div class="col-sm-8">
           <h3 class="m-b-25">Patient Id<span class="semi-bold"> {{ $referenceCode }}</span></h3>
       </div>
 

          <div class=" text-right pull-right m-r-15 m-t-10">
             <form name="searchData" method="GET"> 
            
             <input type="hidden" class="form-control" name="startDate"  >
             <input type="hidden" class="form-control" name="endDate"  >
             <input type="hidden" class="form-control" name="referenceCode"  id="patientRefCode" value="{{ $referenceCode }}" >
                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
                   <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                   <span></span> <b class="caret"></b>
                </div>

             </form>
             <input type="hidden" name="flag" value="0">
            </div>
      </div>
         
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
            <div class="col-sm-4">
               <div id="submissionschart"></div>
            </div>
            <div class="col-sm-8">
               <div class="row p-t-80">
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
         <hr>
         <br>
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
         <br>
         <hr>
         <div class="row">
            <div class="col-sm-12">
               <div class="pull-left">
                  <h4 class="bold">Questionnaire Graph</h4>
               </div>
               <select class="pull-right" name="generateQuestionChart">
                    @foreach($questionLabels as $questionId =>$question)
                    <option value="{{ $questionId }}" >{{ $question }}</option>
                    @endforeach
                 </select>
               <div id="questionChart" class="p-t-20" style="width:100%; height:400px;"></div>
            </div>
         </div>
         <br>
         <HR>
         <!-- <div class="row">
            <div class="col-sm-12">
               <div class="pull-left">
                  <h4 class="bold">Submission Graph</h4>
               </div>
               <select class="pull-right" name="generateChart">
                    <option value="total_score" selected>Total Score</option>
                    <option value="red_flags" > Red Flags</option>
                    <option value="amber_flags">  Amber Flags</option>
                    <option value="green_flags">Green Flags</option>
                 </select>
               <div id="chartdiv"></div>
            </div>
         </div>
         <br>
         <HR> -->
         
         <h4 class="bold">Question score per submission graph</h4>
                               <p>The graph displays previous score,current score and the baseline score of a patient for every question for the selected submission</p>
                            <br><br>
                     
                     <select class="pull-right" name="generateSubmissionChart">
                      @foreach($submissionNumbers as $submissionNumber => $responseId)
                      <option value="{{ $responseId }}">Submission {{ $submissionNumber }}</option>
                      @endforeach
                    </select> 
              

                     <div id="submissionChart" class="p-t-20" style="width:100%; height:500px;"></div>
      </div>


 
                   </div>
                     </div>
 <?php 
$questionId = current(array_keys($questionLabels));
$inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
$questionLabel = (isset($questionLabels[$questionId]))?$questionLabels[$questionId]:'';

$submissionJson = (isset($submissionChart[$firstSubmission])) ? json_encode($submissionChart[$firstSubmission]):'[]';
?>

 <script type="text/javascript">
     var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
    var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} ';

    $(document).ready(function() {

    //var legends = {score: "Total Score"};
    //lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,0,"chartdiv",'Submissions','Total Score');

      //question chart
    shadedLineChartWithBaseLine(<?php echo $inputJson;?>,'{{$questionLabel}}',0,'questionChart','Submissions','Score');

    //submission chart
    submissionBarChart(<?php echo $submissionJson; ?>,'submissionChart');

      $('select[name="referenceCode"]').change(function (event) { 
        $("#patientRefCode").val($(this).val());
         $(this).closest('form').submit();
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

      $('select[name="generateSubmissionChart"]').change(function (event) { 
      <?php 
      foreach($submissionNumbers as $submissionNumber => $responseId)
      {
        $submissionJson = json_encode($submissionChart[$responseId]);
        ?>
        if($(this).val()=='{{$responseId}}')
        { 
          submissionBarChart(<?php echo $submissionJson; ?>,'submissionChart');
        }

        <?php
      }
      ?>

    });

    //   $('select[name="generateChart"]').change(function (event) { 
    //   if($(this).val()=='total_score')
    //   { 
    //    var legends = {score: "Total Score"};
    //     lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,0,"chartdiv",'Submissions','Total Score');
    //   }
    //   else if($(this).val()=='red_flags')
    //   { 
    //     legends = {Baseline: "Baseline",Previous: "Previous"};
    //     lineChartWithOutBaseLine(<?php echo $flagsCount['redFlags'];?>,legends,"chartdiv",'Submissions','Total Red Flags');
    //   }
    //   else if($(this).val()=='amber_flags')
    //   {
    //     legends = {Baseline: "Baseline",Previous: "Previous"};
    //     lineChartWithOutBaseLine(<?php echo $flagsCount['amberFlags'];?>,legends,"chartdiv",'Submissions','Total Amber Flags');

    //   }
    //   else if($(this).val()=='green_flags')
    //   {
    //     legends = {Baseline: "Baseline",Previous: "Previous"};
    //     lineChartWithOutBaseLine(<?php echo $flagsCount['greenFlags'];?>,legends,"chartdiv",'Submissions','Total Green Flags');

    //   } 

    // });

         
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
         });
         
      </script>
@endsection