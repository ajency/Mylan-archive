@extends('layouts.single-hospital')
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
                     <h3><span class="semi-bold">Sequence Number {{ $sequenceNumber }}</span> </h3>
                    
                  </div>
                 
                           <div class="user-description-box">
                              <!-- <div class="pull-right">
                              <span class="text-danger"><i class="fa fa-flag"></i> 5 New</span><span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                              <span class="text-warning"><i class="fa fa-flag"></i> 5 New</span>
                           </div> -->
                              <label>{{ $questionnaire }}</label>
                              <p>Submitted on {{ $date }}</p>
                              <p>Patient #{{ $referenceCode }}</p>
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
 

@endsection