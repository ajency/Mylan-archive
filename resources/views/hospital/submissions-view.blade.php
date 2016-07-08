@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>Home</span></a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Submissions</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="page-title">
     <h3>Patient Id<span class="semi-bold ttuc"> #{{ $patient['reference_code']}}</span></h3>
  </div>
 <div class="tabbable tabs-left">
                      @include('hospital.patients.side-menu')
                     <div class="tab-content">
                        <div class="tab-pane table-data" id="Patients">
                        </div>
                        <div class="tab-pane table-data active" id="Submissions">
                        <h4><span class="semi-bold">{{ $questionnaire }}</span></h4>
                           <p>(Showing Submission details)</p>
                           <br>
                           <div class="user-description-box">
                           <div class="row">
                              <div class="col-md-6">
                                 
                                 <div class="row">
                                    
                                    <div class="col-md-8">
                                       <label>Sequence Number - {{ $sequenceNumber }} </label>
                                         
                                       <!-- <select name="role" id="role" class="select2 form-control"  >
                                          <option value="1">Select Sequence</option>
                                          <option value="2">Sequence 1</option>
                                          <option value="3">Sequence 2</option>
                                          <option value="4">Sequence 3</option>
                                          <option value="5">Sequence 4</option>
                                          <option value="6">Sequence 5</option>
                                          <option value="7">Sequence 6</option>
                                          <option value="8">Sequence 7</option>
                                          <option value="9">Sequence 8</option>
                                          <option value="10">Sequence 9</option>
                                          <option value="11">Sequence 10</option>
                                          <option value="12">Sequence 11</option>
                                          <option value="13" selected>Sequence 12</option>
                                       </select> -->
                                    </div>
                                 </div>
                                 <br>
                                 <div>Submitted on {{ $date }}</div>
                              </div>
                              <!-- <div class="col-md-3 m-t-25">
                                 <div class="row">
                                    <div class="col-md-2">
                                       <h4><i class="fa fa-circle text-warning"></i></h4>
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
                                 -->
                              </div>
                              <!-- <div class="col-md-3 m-t-25 text-right ">
                                 <span class="text-danger"><i class="fa fa-flag"></i> 4</span><span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                              <span class="text-warning"><i class="fa fa-flag"></i> 4</span>
                              </div> -->
                           </div>
                              
                           </div>
                           <br>
 
                           <div class="user-description-box">
                           <?php $i=1;?>
                           @foreach($answersList as $answer)
                              <div class="grid simple">
                                 <div class="grid-body">
                                    @if($answer['questionType']=='single-choice')
                                    <div class="pull-right">
                                        @if($answer['baseLineFlag']=='green')
                                          <span class="text-success"><i class="fa fa-flag"></i></span>
                                        @elseif($answer['baseLineFlag']=='red')
                                          <span class="text-danger"><i class="fa fa-flag"></i></span>
                                        @elseif($answer['baseLineFlag']=='amber')
                                        <span class="text-warning"><i class="fa fa-flag"></i></span>
                                        @endif

                                       <span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                                        @if($answer['previousFlag']=='green')
                                          <span class="text-success"><i class="fa fa-flag"></i></span>
                                        @elseif($answer['previousFlag']=='red')
                                          <span class="text-danger"><i class="fa fa-flag"></i></span>
                                        @elseif($answer['previousFlag']=='amber')
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


 
                 

 

@endsection