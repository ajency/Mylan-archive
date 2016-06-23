@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="#" class="active">Questionnaire Summary</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div>
                    
                     <div class="page-title">
                        <h3><span class="semi-bold">Questionnaire Summary</span></h3>
                     </div>
                  </div>
                                   
                   
                          
                           <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                       <h3 class="">{{ $project['name'] }}</h3>
                      
                      <hr>
          @include('admin.flashmessage')
        <form class="form-horizontal col-sm-12" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/order-questions/'.$questionnaireId ) }}" data-parsley-validate>
            <?php 
            $i=0;
            ?>
            @foreach($questionsList as $questionId => $question)
                <div class="row questionSummary accord-questionSummary">



                  <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $i }}">
                   <div class="col-md-12 questionSummary__head clearfix">
                     <div>
                     <span class="chev-icons"></span>
                        <span class="text-center semi-bold ttuc p-r-15">{{ $question['title'] }} </span>
                            {{ $question['question'] }}
                            @if(isset($optionsList[$questionId]))
                            <span class="label label-default pull-right m-t-5">{{ count($optionsList[$questionId])}} OPTIONS</span>
                            @endif
                     </div>
                   </div>
                   </a>
                  
                  @if(isset($optionsList[$questionId]))
                   <div class="col-md-11 col-md-offset-1 questionSummary__options panel-collapse collapse m-b-15" id="collapse{{ $i }}">
                   <!-- @if(isset($optionsList[$questionId]))
                   <span class="text-center semi-bold col-md-2">Options</span>
                     <div class="col-md-10">
                        <ul>
                            @foreach($optionsList[$questionId] as $option)
                             <li>{{ $option['label'] }}</li>
                            @endforeach
                        </ul>
                     </div>
                     @endif -->
                     <div class="row gray-header">
                       <div class="col-sm-8">Options</div>
                       <div class="col-md-2 text-center">Score</div>
                       <div class="col-sm-2 text-center">Sub Question</div>
                     </div>
                    


                     <!-- test -->
                     <div class="question-options-container">
                      @foreach($optionsList[$questionId] as $option)

                        @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']]))
                           
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $option['optionId'] }}">
                             <div class="question-options">
                             <div class="row">
                               <div class="col-sm-8">
                               <span class="chev-icons"></span>
                                  {{ $option['label'] }}
                               </div>
                               <div class="col-sm-2 text-center">
                                 <span class="bold">{{ $option['score'] }}</span>
                               </div>
                               <div class="col-sm-2 text-center">
                                 <i class="fa fa-check-square-o"></i>
                               </div>
                               </div><!--/row-->
                             </div><!-- /question-options -->
                             </a>

                             <?php
                              $subQuestionId = $question['condition'][$option['optionId']];
                              $subQuestion = $subQuestions[$questionId][$subQuestionId];
                              ?>

                              <div class="row panel-collapse collapse question-options_subquestion__container" id="collapse{{ $option['optionId'] }}">

                                <div class="col-sm-11 col-md-offset-1 ">
                                  <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $subQuestionId }}">
                                    <div class="question-options_subquestion">
                                      <span class="chev-icons"></span>
                                      <span class="ttuc p-r-15">{{ $subQuestion['title'] }}</span>
                                        {{ $subQuestion['question'] }}
                                        @if(isset($optionsList[$subQuestionId]))
                                          <span class="label label-default pull-right m-t-5">{{ count($optionsList[$subQuestionId])}} OPTIONS</span>
                                        @endif
                                   </div>
                                   </a>

                                   <div class="row panel-collapse collapse question-options_subquestion__options" id="collapse{{ $subQuestionId }}">
                                   <div class="col-md-11 col-md-offset-1">
                                     <div class="row">
                                       <div class="col-md-12">
                                         <div class="row gray-header">
                                           <div class="col-sm-8">Options</div>
                                           <div class="col-md-2 text-center">Score</div>
                                           
                                         </div>
                                       </div>
                                     </div>

                                     @if(isset($optionsList[$subQuestionId]))
                                  
                                      @foreach($optionsList[$subQuestionId] as $option)
                                       <div class="question-options">
                                       <div class="row">
                                         <div class="col-sm-8">
                                         <span class="chev-icons"style="padding-right: 20px;">&nbsp;</span>
                                           {{ $option['label'] }}
                                         </div>
                                         <div class="col-sm-2 text-center">
                                           <span class="bold">{{ $option['score'] }}</span>
                                         </div>
                                         </div><!--/row-->
                                       </div><!-- /question-options -->
                                      @endforeach 
                                    @endif
                                   </div>
                                   </div><!--/question-options_subquestion__options-->
                                  </div>


                                 </div><!-- /question-options_subquestion -->

                        @else
                            <div class="question-options">
                             <div class="row">
                               <div class="col-sm-8">
                               <span class="chev-icons"style="padding-right: 20px;">&nbsp;</span>
                                 {{ $option['label'] }}
                               </div>
                               <div class="col-sm-2 text-center">
                                 <span class="bold">{{ $option['score'] }}</span>
                               </div>
                               </div><!--/row-->
                             </div><!-- /question-options -->
                        @endif
                      @endforeach
                      

                      
                         
                       
                     </div><!-- question-options-container -->
                     <!-- /test -->

                   </div> <!-- /questionSummary__options -->
                  @endif
                   
                </div><!--/accord-questionSummary-->
                
           <?php $i++; ?>
            @endforeach

      
        <div class="form-group">
          <div class="col-sm-10 m-t-10 mri-submit questionSubmitBtn">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
          <input type="hidden" value="order" name="submitType"/>
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}"><button type="button" class="btn btn-default"><i class="fa fa-backward" aria-hidden="true"></i> Back</button></a> 
             

          </div>
        </div>

        </form>
                       
                  
                                   
                     </div>
                  </div>
 
<!-- END PLACE PAGE CONTENT HERE -->
<script type="text/javascript">

$(document).ready(function() {
    $('.dd').nestable({});
});

</script>

@endsection