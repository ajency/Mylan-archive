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
                       <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/edit' ) }}" class="btn btn-white text-success"><i class="fa fa-pencil-square-o"></i> Edit</a>
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
                    <option value="submissions">Submissions</option>
                      <option value="baseline">Base line flags</option>
                      <option value="previous">Previous flags</option>
                       
                  </select>
    
                           <div id="chartdiv"></div>
              </div>
           </div>
           <div>
             <br><br>  

        <select class="pull-right" name="generateQuestionChart">
          @foreach($questionLabels as $questionId => $label)
            <option value="{{ $questionId }}">{{ $label }}</option>
          @endforeach
         </select> 

                           <div id="totalbaseline" class="p-t-20" style="width:100%; height:400px;"></div>

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
     <div class="col-sm-12">
          <table class="table table-hover" id="example">
              <thead>
                 <tr>
                    <th class="hidden">Patient</th>
                    <th class="hidden">Doctor</th>
                    <th>Submission #</th>
                    <th>Reason for Flag</th>
                    <th>Type</th>
                    <th>Date</th>
                    
                 </tr>
              </thead>
              <tbody>
              <?php 
                  $i=1;
               ?>
               @foreach($openRedFlags as $openRedFlag)
                <?php 
                  if($i==6)
                       break;
                  ?>
                 <tr class="odd gradeX">
                    <td>{{ $openRedFlag['sequenceNumber'] }}</td>
                    <td>{{ $openRedFlag['reason'] }}</td>
                    <td><i class="fa fa-flag text-{{ $openRedFlag['flag'] }}"></i></td>
                    <td>{{ $openRedFlag['date'] }}</td>
                 </tr>
              @endforeach
                                               
              </tbody>
           </table>
              <hr style="margin: 0px 0px 10px 0px;">
      <!--  <div class="text-right">
              <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/flags') }}" class="text-success">View All <i class="fa fa-long-arrow-right"></i> &nbsp; &nbsp;</a>
           </div> -->
      </div>
          
     </div>
<!-- <a href="patient-flag.html">    Open Red Flags</a></h4>
                       <span>( 5 recently generated red flags )</span> -->
               
                      
                    
                       <br><br>
    <div class="grid simple ">
        <div class="grid simple grid-table">
            <div class="grid-title no-border">
              <a href="#"> <h4>Submissions <span class="semi-bold">(5 recent submissions)</span></h4></a>
            </div>
        </div>
   </div>
                       <!-- submission -->
              
                       
                       <!-- <h6>This patient has missed 5 consecutive submissions</h6> -->
 
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
 
 <?php 

$questionId = current(array_keys($questionLabels));
$inputJson = (isset($questionChartData[$questionId])) ? json_encode($questionChartData[$questionId]):'[]';
$questionLabel = (isset($questionLabels[$questionId]))?$questionLabels[$questionId]:'';
$baseLine = (isset($questionBaseLine[$questionId]))?$questionBaseLine[$questionId]:'';

//submission chart
$submissionChartJson = (isset($submissionChart['chartData'])) ? json_encode($submissionChart['chartData']):'[]';
$submissionChartbaseLine = (isset($submissionChart['baseLine']))?$submissionChart['baseLine']:'';
?>
    <script type="text/javascript">
     

   $(document).ready(function() {

    //patientFlagsChart(<?php echo $flagsCount['baslineFlags'];?>);
    //submission chart
    patientInputGraph(<?php echo $submissionChartJson;?>,'Submission',0,{{$submissionChartbaseLine}},'chartdiv');

    //question chart
    patientInputGraph(<?php echo $inputJson;?>,'{{$questionLabel}}',0,{{$baseLine}},'totalbaseline');

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
      if($(this).val()=='submissions')
      { 
        patientInputGraph(<?php echo $submissionChartJson;?>,'Submission',0,{{$submissionChartbaseLine}},'chartdiv');
      }
      else if($(this).val()=='previous')
      { 
        patientFlagsChart(<?php echo $flagsCount['previousFlags'];?>);
      }
      else if($(this).val()=='baseline')
      {
        patientFlagsChart(<?php echo $flagsCount['baslineFlags'];?>);

      }
       

    });

     $('select[name="generateQuestionChart"]').change(function (event) { 
      <?php 
      foreach($questionLabels as $questionId => $questionLabel)
      {
        $inputJson = json_encode($questionChartData[$questionId]);
        $baseLine = $questionBaseLine[$questionId];
        ?>
        if($(this).val()=='{{$questionId}}')
        { 
          patientInputGraph(<?php echo $inputJson;?>,'{{$questionLabel}}',0,{{$baseLine}},'totalbaseline');
        }

        <?php
      }
      ?>

    });

});
 

    
</script>
<!-- END PLACE PAGE CONTENT HERE -->
@endsection