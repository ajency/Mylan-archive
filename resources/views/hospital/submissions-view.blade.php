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
                     <h3><span class="semi-bold">{{ $referenceCode }}</span> </h3>
                    
                  </div>
                 
                           <div class="user-description-box">
                              <!-- <div class="pull-right">
                              <span class="text-danger"><i class="fa fa-flag"></i> 5 New</span><span class="text-muted">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                              <span class="text-warning"><i class="fa fa-flag"></i> 5 New</span>
                           </div> -->
                              <label>{{ $questionnaire }}</label>
                              <p>Submitted on {{ $date }}</p>
                           </div>
                           <br>
                           <div class="user-description-box">
                           <?php $i=1;?>
                           @foreach($answersList as $answer)
                              <div class="grid simple">
                                 <div class="grid-body">
                                    <!-- <div class="pull-right">
                                       <span class="text-danger">Score : 4 <i class="fa fa-flag"></i></span>
                                     </div> -->
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