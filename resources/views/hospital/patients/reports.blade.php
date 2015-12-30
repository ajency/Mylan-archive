@extends('layouts.single-hospital')
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
            <a href="#"> Reports</a>
         </li> 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<div class="page-title">
     <h3>Report of Patient Id<span class="semi-bold"> #{{ $patient['reference_code']}}</span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('hospital.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane" id="Submissions">
                           
                        </div>
                        <div class="tab-pane active" id="Reports">
                            <h4>Health Score Results compared with Previous Scores</h4>
                                 <p>The Table below shows the Health Scores for each Week and the Change in their Health when compared with previous Score</p>
                             <br><br>
                 
                            <table class="table table-flip-scroll cf">
                                          <thead class="cf">
                                             <tr>
                                                <th>Week</th>
                                                @foreach($responseArr as $response)
                                                <th>{{ $response }}</th>
                                                @endforeach
                                             </tr>
                                          </thead>
                                          <tbody>
                                          @foreach($questionArr as $questionId => $question)
                                             <tr>
                                                <td>{{ $question }}</td>
                                                <?php 

                                                $lastSubmittedScore =$baseLineArr[$questionId];
                                                ?>
                                                @foreach($responseArr as $responseId => $response)
                                                <?php
                                                $myscore ='';
                                                $difference ='';

                                                if(isset($submissionArr[$responseId][$questionId]))
                                                {
                                                  $myscore = $submissionArr[$responseId][$questionId];
                                                  $difference = ($lastSubmittedScore - $myscore);
                                                  if($lastSubmittedScore < $myscore)
                                                    $class='bg-danger';
                                                  elseif($lastSubmittedScore > $myscore)
                                                    $class='bg-success';
                                                  else
                                                    $class='bg-warning';
                                                }
                                                else
                                                {
                                                  $class='bg-gray';
                                                }
                                                ?>
                                                <td class="{{ $class }}"> {{ $difference }} {{ $myscore }}</td>
                                                 <?php 
                                                  if($myscore!='')
                                                     $lastSubmittedScore = $myscore;
                                                 ?>
                                                @endforeach
                                                
                                             </tr>
                                          @endforeach
                                             
                                             
                                          </tbody>
                                       </table>
                                       <br>
                                    <hr>
                                    <br>
                                 <h4>Health Score Results compared with Baseline Value</h4>
                                 <p>The Table below shows the Health Scores for each Week and the Change in their Health when compared with the Baseline Value for each particular Question</p>
                              <br><br>
                              
                           <table class="table table-flip-scroll cf">
                                          <thead class="cf">
                                             <tr>
                                                <th>Week</th>
                                                @foreach($responseArr as $response)
                                                <th>{{ $response }}</th>
                                                @endforeach
                                                <th>Baseline Value</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                             @foreach($questionArr as $questionId => $question)
                                             <tr>
                                                <td>{{ $question }}</td>
                                                @foreach($responseArr as $responseId => $response)
                                                <?php
                                                $myscore ='';
                                                $difference ='';
                                                if(isset($submissionArr[$responseId][$questionId]))
                                                {
                                                  $myscore = $submissionArr[$responseId][$questionId];
                                                  $baseLineScore = $baseLineArr[$questionId];
                                                  $difference = ($baseLineScore - $myscore);
                                                  
                                                  if($baseLineScore < $myscore)
                                                    $class='bg-danger';
                                                  elseif($baseLineScore > $myscore)
                                                    $class='bg-success';
                                                  else
                                                    $class='bg-warning';
                                                }
                                                else
                                                  $class='bg-gray';
                                                ?>
                                                <td class="{{ $class }}"> {{ $difference }} {{ $myscore }}</td>
                                                @endforeach
                                                <td>{{ $baseLineScore }}</td>
                                             </tr>
                                          @endforeach
                                             
                                             
                                          </tbody>
                                       </table>
                                       <br>
                              <hr>
                              <br>
                              <h4>Report on {{$inputLable}} of the Patient</h4>
                                 <p>The Table below shows the {{$inputLable}} of the Patient over the period. This information has been extracted 
                                    from answer to the Question "What is your current {{$inputLable}}" submitted by the Patient as part of
                                    Questionnaire "Cardiac Care Project 1".</p>
                              <br><br>
                              
                           <div id="line-example" style="width:100%;height:400px;"> </div>

                        </div> 
                     </div>
                     </div>


<script type="text/javascript">
  var chart = AmCharts.makeChart("line-example", {
    "type": "serial",
    "theme": "light",
    "legend": {
        "useGraphSettings": true
    },
    "dataProvider": [
    <?php
        
        foreach($responseArr as $responseId => $response)
        {
          
          ?>
          {
              "occurrence": "<?php echo $response?>",
              "<?php echo str_slug($inputLable);?>": <?php echo $inputScores[$responseId]?>,
              "base_line":<?php echo $inputBaseLineScore?>,
     
          },
           
          <?php 
            
        }
 
    ?>

    ],
    "valueAxes": [{
        "integersOnly": true,
        "maximum": <?php echo (max($inputScores) + 10)?>,
        "minimum": 0,
        "reversed": false,
        "axisAlpha": 0,
        "dashLength": 5,
        "gridCount": 10,
        "position": "left",
        "title": "<?php echo $inputLable;?>"
    }],
    "startDuration": 0.5,
    "graphs": [{
        "balloonText": "<?php echo $inputLable;?> on [[category]]: [[value]]",
        "bullet": "round",
        "title": "<?php echo $inputLable;?>",
        "valueField": "<?php echo str_slug($inputLable);?>",
    "fillAlphas": 0
    }, {
        "balloonText": "Base Line [[category]]: [[value]]",
        "bullet": "round",
        "title": "Base Line",
        "valueField": "base_line",
    "fillAlphas": 0
    }],
    "chartCursor": {
        "cursorAlpha": 0,
        "zoomable": false
    },
    "categoryField": "occurrence",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "fillAlpha": 0.05,
        "fillColor": "#000000",
        "gridAlpha": 0,
        "position": "top"
    },
    "export": {
      "enabled": true,
        "position": "bottom-right"
     }
});

</script>
@endsection
