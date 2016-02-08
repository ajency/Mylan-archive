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
           <div class="row hidden">
              <div class="col-sm-6">
                 <div class="row">
                    <div class="col-sm-4">
                       <div class="form-row">
                          <label>Start Date</label>
                          <div class="input-append default date" style="width:100%;">
                             <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                             <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-4">
                       <div class="form-row">
                          <label>End Date</label>
                          <div class="input-append default date" style="width:100%;">
                             <input type="text" class="form-control" id="sandbox-advance" style="width:76%;">
                             <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-4 m-t-25">
                       <a href="" class="btn btn-default">Apply</a>
                    </div>
                 </div>
              </div>
              <div class="col-sm-6 m-t-25 text-right">
                 <a href="" class="btn btn-danger"><i class="fa fa-download"></i> Download CSV</a>
                 <a href="" class="btn btn-danger"><i class="fa fa-download"></i> Download PDF</a>
              </div>
           </div>
           <table class="table table-flip-scroll cf">
              <thead class="cf">
                 <tr>
                    <th>Week</th>
                    <th>1 Jan</th>
                    <th>2 Jan</th>
                    <th>11 Jan</th>
                    <th>12 Jan</th>
                    <th>14 Jan</th>
                    <th>18 Jan</th>
                    <th>19 Jan</th>
                    <th>22 Jan</th>
                    <th>23 Jan</th>
                    <th>24 Jan</th>
                    <th>25 Jan</th>
                 </tr>
              </thead>
              <tbody>
                 <tr>
                    <td>Pain</td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"><i class="fa fa-flag text-error" ></i></td>
                    <td class="bg-warning"> <i class="fa fa-flag text-warning" ></i></td>
                    <td></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"></td>
                 </tr>
                 <tr>
                    <td>Bowel Habits</td>
                    <td class="bg-gray"></td>
                    <td class="bg-warning"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-warning"></td>
                    <td class="bg-warning"></td>
                    <td ></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-warning"></td>
                 </tr>
                 <tr>
                    <td>Weight</td>
                    <td class="bg-gray"></td>
                    <td class="bg-success"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"><i class="fa fa-flag text-error" ></i></td>
                    <td class="bg-danger"></td>
                    <td ></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"><i class="fa fa-flag text-error" ></i></td>
                 </tr>
                 <tr>
                    <td>Appetite</td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-warning"></td>
                    <td class="bg-warning"><i class="fa fa-flag text-warning" ></i></td>
                    <td ></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-warning"><i class="fa fa-flag text-warning" ></i></td>
                 </tr>
                 <tr>
                    <td>Well Being</td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"></td>
                    <td class="bg-warning"></td>
                    <td></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-warning"></td>
                 </tr>
                 <tr>
                    <td>Diabetes</td>
                    <td class="bg-gray"></td>
                    <td class="bg-success"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-danger"></td>
                    <td class="bg-warning"></td>
                    <td></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-gray"></td>
                    <td class="bg-warning"></td>
                 </tr>
                 <tr>
                    <td><b>Total</b></td>
                    <td class="bg-gray"></td>
                    <td class="bg-success"></td>
                    <td></td>
                    <td class="bg-danger"> <i class="fa fa-flag text-error" ></i></td>
                    <td class="bg-danger"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="bg-danger"></td>
                 </tr>
              </tbody>
           </table>
           <div>
               
              <hr>
              <br>
              <div class="pull-left">
                 <h4 class="bold">Questionnaire Graph</h4>
              </div>
              <select class="pull-right">
                 <option value="volvo">Pain</option>
                 <option value="saab">Bowel Habits</option>
                 <option value="saab">Weight</option>
                 <option value="saab">Appetite</option>
                 <option value="saab">Well Being</option>
                 <option value="saab">Diabetes</option>
                 <option value="saab">Total</option>
              </select>
              <div id="totalbaseline" class="p-t-20" style="width:100%; height:400px;"></div>
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
                                <th class="sorting">Review Status<br><br>
                                </th>
                             </tr>
                          </thead>
                          <tbody>
                             <tr onclick="window.document.location='p1-submission1.html';">
                                <td>
                                   <h4 class="semi-bold m-0 flagcount">6th Dec</h4>
                                   <sm><b>#12</b></sm>
                                </td>
                                <td class="text-center sorting">
                                   <span>10</span>
                                   <span>12</span>
                                   <span>10</span>
                                </td>
                                <td class="text-center">
                                   <h4 class="semi-bold margin-none flagcount">
                                      <b class="text-success">0</b> / <b class="f-w text-success">2</b>
                                   </h4>
                                </td>
                                <td class="text-center sorting">
                                   <span class=" text-error">04</span>
                                   <span class="text-warning">03</span>
                                   <span class="text-success">04</span>
                                </td>
                                <td class="text-center sorting">
                                   <span class="text-error">24</span>
                                   <span class="text-warning">13</span>
                                   <span class=" text-success">14</span>
                                </td>
                                <td class="text-center text-success">Reviewed</td>
                             </tr>
                             <tr onclick="window.document.location='p1-submission1.html';">
                                <td>
                                   <h4 class="semi-bold m-0 flagcount">13th Dec</h4>
                                   <sm><b>#13</b></sm>
                                </td>
                                <td class="text-center sorting">
                                   <span>09</span>
                                   <span>11</span>
                                   <span>12</span>
                                </td>
                                <td class="text-center">
                                   <h4 class="semi-bold margin-none flagcount">
                                      <b class="text-error">-3</b> / <b class="f-w text-error">-1</b>
                                   </h4>
                                </td>
                                <td class="text-center sorting">
                                   <span class="text-error">14</span>
                                   <span class=" text-warning">13</span>
                                   <span class=" text-success">14</span>
                                </td>
                                <td class="text-center sorting">
                                   <span class="text-error">04</span>
                                   <span class="text-warning">03</span>
                                   <span class="text-success">14</span>
                                </td>
                                <td class="text-center text-success">Reviewed</td>
                             </tr>
                             <tr  onclick="window.document.location='p1-submission1.html';">
                                <td>
                                   <h4 class="semi-bold m-0 flagcount">10th Dec</h4>
                                   <sm><b>#10</b></sm>
                                </td>
                                <td class="text-center sorting">
                                   <span>15</span>
                                   <span>07</span>
                                   <span>07</span>
                                </td>
                                <td class="text-center">
                                   <h4 class="semi-bold margin-none flagcount">
                                      <b class="text-success">8</b> / <b class="f-w text-success">0</b>
                                   </h4>
                                </td>
                                <td class="text-center sorting">
                                   <span class="text-error">04</span>
                                   <span class="text-warning">03</span>
                                   <span class="text-success">04</span>
                                </td>
                                <td class="text-center sorting">
                                   <span class="text-error">24</span>
                                   <span class="text-warning">13</span>
                                   <span class="text-success">14</span>
                                </td>
                                <td class="text-center text-warning">Unreviewed</td>
                             </tr>
                             <tr  onclick="window.document.location='p1-submission1.html';">
                                <td>
                                   <h4 class="semi-bold m-0 flagcount">12th Dec</h4>
                                   <sm><b>#13</b></sm>
                                </td>
                                <td class="text-center sorting">
                                   <span>10</span>
                                   <span>12</span>
                                   <span>14</span>
                                </td>
                                <td class="text-center">
                                   <h4 class="semi-bold margin-none flagcount">
                                      <b class="text-error">-4</b> / <b class="f-w text-error">-2</b>
                                   </h4>
                                </td>
                                <td class="text-center sorting">
                                   <span class="text-error">04</span>
                                   <span class="text-warning">03</span>
                                   <span class="text-success">04</span>
                                </td>
                                <td class="text-center sorting">
                                   <span class="text-error">24</span>
                                   <span class="text-warning">13</span>
                                   <span class="text-success">14</span>
                                </td>
                                <td class="text-center text-success">Reviewed</td>
                             </tr>
                          </tbody>
                       </table>
                       <hr style="margin: 0px 0px 10px 0px;">
                       <div class="text-right">
                          <a href="submissions.html" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
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
                          <tr class="odd gradeX" onclick="window.document.location='p1-submission1.html';">
                             <td width="110px">
                                <div class="p-l-10 p-r-20">
                                   <h4 class="semi-bold m-0 flagcount">13th Dec</h4>
                                   <sm><b>#13</b></sm>
                                </div>
                             </td>
                             <td>In comparison with previous score for Pain for submission 06</td>
                             <td><i class="fa fa-flag text-amber"></i></td>
                          </tr>
                          <tr class="odd gradeX" onclick="window.document.location='p1-submission1.html';">
                             <td width="110px">
                                <div class="p-l-10 p-r-20">
                                   <h4 class="semi-bold m-0 flagcount">6th Dec</h4>
                                   <sm><b>#13</b></sm>
                                </div>
                             </td>
                             <td>In comparison with baseline score set for Weight</td>
                             <td><i class="fa fa-flag text-error"></i></td>
                          </tr>
                          <tr class="odd gradeX" onclick="window.document.location='p1-submission1.html';">
                             <td width="110px">
                                <div class="p-l-10 p-r-20">
                                   <h4 class="semi-bold m-0 flagcount">10th Dec</h4>
                                   <sm><b>#11</b></sm>
                                </div>
                             </td>
                             <td>In comparison with previous score for Bowel Habits for submission 10</td>
                             <td><i class="fa fa-flag text-error"></i></td>
                          </tr>
                          <tr class="odd gradeX" onclick="window.document.location='p1-submission1.html';">
                             <td width="110px">
                                <div class="p-l-10 p-r-20">
                                   <h4 class="semi-bold m-0 flagcount">14th Dec</h4>
                                   <sm><b>#12</b></sm>
                                </div>
                             </td>
                             <td>In comparison with baseline score set for Weight</td>
                             <td><i class="fa fa-flag text-danger"></i></td>
                          </tr>
                          <tr class="odd gradeX" onclick="window.document.location='p1-submission1.html';">
                             <td width="110px">
                                <div class="p-l-10 p-r-20">
                                   <h4 class="semi-bold m-0 flagcount">16th Dec</h4>
                                   <sm><b>#11</b></sm>
                                </div>
                             </td>
                             <td>In comparison with baseline score set for Bowel Habits</td>
                             <td><i class="fa fa-flag text-error"></i></td>
                          </tr>
                          <!-- <tr class="odd gradeX" onclick="window.document.location='single-submission01.html';">
                             <td>10000003</td>
                             <td>29th Oct</td>
                             <td>4,2</td>
                             <td>2 points higher than the previous answer</td>
                             <td><i class="fa fa-flag text-danger"></i></td>
                             <td> <span class="label label-warning">De-Flag</span></td>
                             </tr> -->
                       </tbody>
                    </table>
                    <hr style="margin: 0px 0px 10px 0px;">
                    <div class="text-right">
                       <a href="patient-flag.html" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
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
$baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:0;


?>
    <script type="text/javascript">
    var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
    var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 

   $(document).ready(function() {

    // submission chart
    var legends = {score: "Total Score"};
    lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,<?php echo $flagsCount['baslineScore'];?>,"chartdiv")

    //question chart
    shadedLineChartQithBaseLine(<?php echo $inputJson;?>,'{{$questionLabel}}',{{$baseLine}},'totalbaseline')
 
    var chart = AmCharts.makeChart( "submissionschart", {
                 "type": "pie",
                 "theme": "light",
                 "dataProvider": [ {
                   "title": "# Missed",
                   "value": {{ $responseRate['missed'] }}
                 }, {
                   "title": "# Completed",
                   "value": {{ $responseRate['completed'] }}
                 } 
                 , {
                   "title": "# late",
                   "value": {{ $responseRate['late'] }}
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
      if($(this).val()=='total_score')
      { 
       legends = {score: "Total Score"};
        lineChartWithBaseLine(<?php echo $flagsCount['totalFlags'];?>,legends,<?php echo $flagsCount['baslineScore'];?>,"chartdiv")
      }
      else if($(this).val()=='red_flags')
      { 
        legends = {Baseline: "Baseline",Previous: "Previous"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['redFlags'];?>,legends,"chartdiv");
      }
      else if($(this).val()=='amber_flags')
      {
        legends = {Baseline: "Baseline",Previous: "Previous"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['amberFlags'];?>,legends,"chartdiv");

      }
      else if($(this).val()=='green_flags')
      {
        legends = {Baseline: "Baseline",Previous: "Previous"};
        lineChartWithOutBaseLine(<?php echo $flagsCount['greenFlags'];?>,legends,"chartdiv");

      } 

    });


});
 

    
</script>
<!-- END PLACE PAGE CONTENT HERE -->
@endsection