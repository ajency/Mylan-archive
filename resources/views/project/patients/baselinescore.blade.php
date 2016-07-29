@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
<p>
      <ul class="breadcrumb">
 
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}" class="ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</a> </li>
        <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Baseline Score</a> </li>
 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="page-title">
     <h3>Patient <span class="semi-bold ttuc"><span class="patient-refer{{ $patient['reference_code']}}">Id #{{ $patient['reference_code']}}</span></span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('project.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data" id="Submissions">
                             
                        </div>
                        <div class="tab-pane active" id="baseline">
                           <div class="pull-right">
							@if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))	
                              <a class="btn btn-white" href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/base-line-score-edit') }}"><span class="text-success"><i class="fa fa-pencil-square-o"></i> Add</span></a>
							@endif	
                           </div>
                           <h4><span class="semi-bold">{{ $questionnaire }}</span></h4>
                           <p>(Baseline score for Patient <span class="ttuc patient-refer{{ $patient['reference_code']}}">Id {{ $patient['reference_code']}}</span>)</p>
                           <br>
                           @include('admin.flashmessage')
                          <div class="user-description-box">
                          <?php
                          $x = 1;
                          ?>
                          @if(!empty($questionsList))
                          @foreach($questionsList as $questionId => $questions)
                              <div class="grid simple">
                                 <div class="grid-body">
                                    <label class="semi-bold">Q{{ $x }}) {{ $questions['question'] }}?</label>
                                    @if(isset($answersList[$questionId]))
                                      @if($questions['type']=='input')
                                        <h5 class="text-success semi-bold">{{ getInputValues($answersList[$questionId]['optionValues']) }} </h5>
                                      @elseif($questions['type']=='single-choice')
                                       
                                        <h5 class="text-success semi-bold">{{ $answersList[$questionId]['label'] }}</h5>
                                        <h5 class="text-success"><span class="text-muted">Baseline Score:</span> <span class="text-info">{{ $answersList[$questionId]['score']}}</span></h5>
                                      @elseif($questions['type']=='multi-choice')
                                        <?php 
                                        $baseLineStr= '';
                                        ?> 
                                        @foreach($answersList[$questionId] as $answers)
                                          <h5 class="text-success semi-bold">{{ $answers['label'] }}</h5>
                                          <?php 
                                          $baseLineStr .= $answers['label'].', ';
                                          ?> 
                                        @endforeach
                                        <h5 class="text-success"><span class="text-muted">Baseline Score:</span> <span class="text-info">{{ $baseLineStr }}</span></h5>
                                      @elseif($questions['type']=='descriptive')
                                        <h5 class="text-success semi-bold">{{ $answersList[$questionId]['value']}} </h5>
                                      @endif
                                    @else
                                      <h5 class="text-success semi-bold">-</h5>
                                    @endif 
                                     
                                 </div>
                              </div>
                          <?php
                          $x ++;
                          ?>    
                          @endforeach
                          @else 
                            <h4><span class="semi-bold">Questionnaire is not set for the project {{ $projectName }}</span></h4>
                          @endif
                       
                           </div>
                        </div>
                        <div class="tab-pane " id="Reports">

                        </div> 
                     </div>
                     </div>


 
@endsection
