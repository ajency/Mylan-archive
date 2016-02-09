@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="#" class="active">Submissions</a>
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
                          <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/submissions') }}"><i class="fa fa-caret-square-o-left"></i> Back to list of submissions</a>
                           <h4><span class="semi-bold">{{ $questionnaire }}</span></h4>
                           <!-- <p>(Showing Submission details)</p> -->
                           <br>
                  <div id="chartdiv" style="width:100%; Height:500px;"></div> <br>
                  <br>    
                           <div>
                           </div>

                           <div class="user-description-box">
                           <div class="row">
                              <div class="col-md-6">
                                 
                                 <div class="row">
                                    
                                    <div class="col-md-8">
                                       <label>Submission Number</label>
                                       <select name="patientSubmission" id="patientSubmission" class="select2 form-control"  >
                                       @foreach($allSubmissions as $responseId =>$submission)
                                          <option value="{{$responseId}}" {{ ($currentSubmission==$responseId)?'selected':'' }}>{{ $submission }}</option>
                                       @endforeach
                                       </select>
                                    </div>
                                 </div>
                                 <br>
                                 <div>Submitted on {{ $date }}</div>
                              </div>
                              <div class="col-md-3 m-t-25">
                                 <div class="row">
                                    <div class="col-md-2">
                                      
                                    </div>
                                    <div class="col-md-8"> 
                                       <select name="role" id="role" class="select2 form-control"  >
                                          <option value="1">Status</option>
                                          <option value="2">Reviewed</option>
                                          <option value="2" selected>Pending Review</option>
                                       </select>
                                     
                                    </div>

                                    <div class="col-md-2 m-t-15 hidden"> <span class="cf-loader"></span></div>
                                 </div>
                                
                              </div>
                              <div class="col-md-3 m-t-25 text-right ">
                              Previous | Baseline<br>
                                 <span class="text-{{ $responseData['previousFlag'] }}"><i class="fa fa-flag"></i> {{ $responseData['comparedToPrevious'] }}</span><span class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                              <span class="text-{{ $responseData['baseLineFlag'] }}"><i class="fa fa-flag"></i> {{ $responseData['comparedToBaseLine'] }}</span>
                              </div>
                           </div>
                              
                           </div>
                           <br>
                      
                           </div>
                           <br>
 
                           <div class="user-description-box">
                           <?php $i=1;?>
                           @foreach($answersList as $answer)
                              <div class="grid simple">
                                 <div class="grid-body">
                                    @if($answer['questionType']=='single-choice')
                                    <div class="pull-right">
                                       {{ $answer['comparedToPrevious'] }}
                                        @if($answer['previousFlag']=='green')
                                          <span class="text-success"><i class="fa fa-flag"></i></span>
                                        @elseif($answer['previousFlag']=='red')
                                          <span class="text-danger"><i class="fa fa-flag"></i></span>
                                        @elseif($answer['previousFlag']=='amber')
                                        <span class="text-warning"><i class="fa fa-flag"></i></span>
                                        @endif

                                       <span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>

                                        {{ $answer['comparedToBaseLine'] }}
                                        @if($answer['baseLineFlag']=='green')
                                          <span class="text-success"><i class="fa fa-flag"></i></span>
                                        @elseif($answer['baseLineFlag']=='red')
                                          <span class="text-danger"><i class="fa fa-flag"></i></span>
                                        @elseif($answer['baseLineFlag']=='amber')
                                        <span class="text-warning"><i class="fa fa-flag"></i></span>
                                        @endif

                                     </div>
                                     @endif
                                    <label class="semi-bold">Q {{$i}} ) {{ $answer['question']}}</label>
                                    @if($answer['questionType']=='multi-choice')
                                    <?php
                                      $x = 'A';
                                    ?>
                                      @foreach($answer['option'] as $option)
                                      <h5 class="text-success semi-bold">{{ $x }} : {{ $option }}</h5>
                                      <?php $x++;?>
                                      @endforeach
                                    @else
                                      <h5 class="text-success semi-bold">A: {{ $answer['value']}} {{ $answer['option']}}</h5>
                                    @endif
                                    
                                    
                                    @if(isset($previousAnswersList[$answer['questionId']]))
                                    <h5 class="text-success"><span class="text-muted">Previous Answer:</span>
   
                                        @if($previousAnswersList[$answer['questionId']]['questionType']=='multi-choice')
                                           <?php
                                              $x = 'A';
                                            ?>
                                            <br>
                                            @foreach($previousAnswersList[$answer['questionId']]['option'] as $option)
                                            <span class="text-info"><b>{{ $x }}</b> : {{ $option }}</span>  <br>
                                            <?php $x++;?>
                                            @endforeach
                                          
           
                                        @else
                                          <span class="text-info">{{ $previousAnswersList[$answer['questionId']]['value']}} {{ $previousAnswersList[$answer['questionId']]['option']}}</span>
                                        @endif
                                     </h5>
                                    @endif
                                    @if(isset($baseLineAnswersList[$answer['questionId']]))
                                    <h5 class="text-success"><span class="text-muted">Base Line Answer:</span>
   
                                        @if($baseLineAnswersList[$answer['questionId']]['questionType']=='multi-choice')
                                          <?php
                                            $x = 'A';
                                          ?>
                                          <br>
                                          @foreach($baseLineAnswersList[$answer['questionId']]['option'] as $option)
                                          <span class="text-info"><b>{{ $x }}</b>: {{ $option }}</span>  <br>
                                          <?php $x++;?>
                                          @endforeach
                               
           
                                        @else
                                          <span class="text-info">{{ $baseLineAnswersList[$answer['questionId']]['value']}} {{ $baseLineAnswersList[$answer['questionId']]['option']}}</span>
                                        @endif
                                     </h5>
                                    @endif
                                 </div>
                              </div>
                              <?php $i++;?>
                            @endforeach
 
                           </div>
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>


     <script type="text/javascript">
      var chart = AmCharts.makeChart("chartdiv", {
    "theme": "light",
    "type": "serial",
     "legend": {
    "useGraphSettings": true
  },
    "dataProvider": [{
        "question": "Pain",
        "base": 13,
        "prev": 10,
        "current": 7

    }, {
        "question": "Bowel Habits",
        "base": 19,
        "prev": 13,
        "current": 18
    }, {
        "question": "Weight",
      "base": 10,
        "prev": 05,
        "current": 13
    }, {
        "question": "Appetite",
         "base": 20,
        "prev": 22,
        "current": 13
    }, {
        "question": "Well Being   ",
         "base": 29,
        "prev": 13,
        "current": 16
    }, {
        "question": "Diabetes",
         "base": 10,
        "prev": 13,
        "current": 18
    }],
    "valueAxes": [{
        "position": "left",
        "title": "Score",
    }],
    "startDuration": 1,
    "graphs": [{
        "balloonText": "Previous [[category]] (: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Previous",
        "type": "column",
        "valueField": "prev"
    }, {
        "balloonText": "Baseline [[category]] : <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Baseline",
        "type": "column",
        "valueField": "base"
    },{
        "balloonText": "Current [[category]]: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Current",
        "type": "column",
        "clustered":false,
        "columnWidth":0.5,
        "valueField": "current"
    }],
    "plotAreaFillAlphas": 0.1,
    "categoryField": "question",
    "categoryAxis": {
        "gridPosition": "start"
    },
    "export": {
      "enabled": true
     }

});

   $(document).ready(function() {

      $('select[name="patientSubmission"]').change(function (event) { 
         window.location="/{{ $hospital['url_slug'] }}/{{ $project['project_slug'] }}/submissions/"+$(this).val();
      });

   });
      </script> 
                 

 

@endsection