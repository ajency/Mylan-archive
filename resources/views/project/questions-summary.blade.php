@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Questionnaire Summary</a> </li>
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
                <?php 
                $isWeight = false;
               if(isset($optionsList[$questionId][0]['label'])  && $optionsList[$questionId][0]['label']=="kg" && $optionsList[$questionId][1]['label']=="st" && $optionsList[$questionId][2]['label']=="lb")
                  $isWeight = true;
                ?>
                <div class="row questionSummary accord-questionSummary">


                  @if(($question['type']=="single-choice" || $question['type']=="multi-choice" || $question['type']=="input") && !$isWeight)
                  <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $i }}">
                   <div class="col-md-12 questionSummary__head clearfix">
                     <div class="row">
                      <div class="col-sm-1" style="width: 1.33%;">
                        <span class="chev-icons"></span>
                      </div>
                      <div class="col-md-7">
                        <span class="text-center semi-bold ttuc p-r-15">{{ $question['title'] }} </span>
                        {{ $question['question'] }}
                      </div>

                      <div class="col-md-4" style="width: 38.33%;">
                        @if(isset($optionsList[$questionId]))
                        &nbsp; <span class="label label-default cust-label pull-right m-t-5 test">{{ count($optionsList[$questionId])}} OPTIONS</span> &nbsp;
                        @endif

                        <span class="pull-right m-t-5 quest-type">{{ ucfirst($question['type'])}}</span>
                      </div>
                     </div>
                   </div>
                  </a>
                  @else
                  <div class="col-md-12 questionSummary__head clearfix">
                     <div class="row">
                      <div class="col-sm-1" style="width: 1.33%;">
                        <span class="chev-icons"></span>
                      </div>
                      <div class="col-md-7">
                        <span class="text-center semi-bold ttuc p-r-15">{{ $question['title'] }} </span>
                        {{ $question['question'] }}
                      </div>

                      <div class="col-md-4" style="width: 38.33%;">
                          <span class="pull-right m-t-5 quest-type">{{ ($isWeight)?'weight':ucfirst($question['type']) }}</span>
                      </div>
                     </div>
                   </div>
                   
                  @endif
                  
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
                     <div class="row gray-header bold">
                       <div class="col-sm-8">Options</div>
                       <div class="col-md-2 text-center">Score</div>
                       @if(isset($subQuestions[$questionId]))
                       <div class="col-sm-2 text-center">Sub Question</div>
                       @endif
                     </div>
                    


                     <!-- test -->
                     <div class="question-options-container">
                      @foreach($optionsList[$questionId] as $option)

                        @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']]))
                           <div class="question-options__cover">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $option['optionId'] }}">
                             <div class="question-options">
                             <div class="row">
                               <div class="col-sm-8 p-l-45">
                                  {{ $option['label'] }}
                               </div>
                               <div class="col-sm-2 text-center">
                                 <span class="bold">{{ $option['score'] }}</span>
                               </div>
                               <div class="col-sm-2 text-center has-subquestion">
                                 @if($question['type']=="single-choice")
                                  @if(isset($subQuestions[$questionId]))
                                    Yes
                                  @else
                                    No
                                  @endif
                                  @else
                                    NA
                                  @endif
                               </div>
                               </div><!--/row-->
                             </div><!-- /question-options -->
                             </a>

                             <!-- sub-question -->
                             <!-- markup -->
                             <div class="clearfix">
                               <small><a class="accordion-toggle collapsed p-l-30 collapse-link" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $option['optionId'] }}"></a></small>
                             </div>
                             <!-- /markup -->
                             <?php
                              $subQuestionId = $question['condition'][$option['optionId']];
                              $subQuestion = $subQuestions[$questionId][$subQuestionId];
                              ?>

                              <div class="panel-collapse collapse question-options_subquestion__container" id="collapse{{ $option['optionId'] }}">

                                <!-- <div class="col-sm-11 col-md-offset-1 gray-area"> -->
                                  <!-- <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $subQuestionId }}"> -->
                                    <div class="question-options_subquestion">
                                    <div class="row">
                                      <!-- <div class="col-sm-1" style="width: 1.33%;">
                                        <span class="chev-icons"></span>
                                      </div>  -->
                                      
                                      <div class="col-md-8">
                                        <span class="ttuc p-r-15">{{ $subQuestion['title'] }}</span>
                                        {{ $subQuestion['question'] }}
                                      </div>

                                      <div class="col-md-4">
                                        @if(isset($optionsList[$subQuestionId]))
                                          <span class="label label-default pull-right m-t-5">{{ count($optionsList[$subQuestionId])}} OPTIONS</span>
										  <span class="pull-right m-t-5 quest-type" style="font-size:10px;">{{ $subQuestion['type'] }}</span>
                                        @endif
                                      </div>
                                      </div><!--/row--> 
                                   </div>
                                   <!-- </a> -->

                                   <!-- <div class="row panel-collapse collapse question-options_subquestion__options" id="collapse{{ $subQuestionId }}"> -->
                                   <div class="question-options_subquestion__options">
                                   <!-- <div class="col-md-11 col-md-offset-1" style="width:91.66666667%;"> -->
                                     <div class="subQuestion-option">
                                       <!-- <div class="col-md-12"> -->
                                         <div class="row bold">
                                           <div class="col-sm-8 p-l-45">Options</div>
                                           <div class="col-md-2 text-center">Score</div>
                                           
                                           
                                         </div>
                                       <!-- </div> -->
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
                                   <!-- </div> -->
                                   </div><!--/question-options_subquestion__options-->
                                  <!-- </div> -->


                                 </div><!-- /question-options_subquestion -->
                                 </div><!--/question-options__cover-->

                                 <!-- /sub-question -->
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
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}"><button type="button" class="btn btn-link cust-link"><i class="fa fa-angle-left" aria-hidden="true"></i> Back</button></a> 
             

          </div>
        </div>

        </form>
                       
                  
                                   
                     </div>
                  </div>
 
<!-- END PLACE PAGE CONTENT HERE -->
<script type="text/javascript">
$(document).ready(function() {
    $('.dd').nestable({});
    $('.questionSummary__options.collapse').on('show.bs.collapse', function () {
      $(this).parent('.questionSummary').siblings('.questionSummary').find('.questionSummary__options.in').collapse('hide');
      $(this).parent('.questionSummary').siblings('.questionSummary').addClass('rest-are-closed').find('.accordion-toggle').addClass('collapsed');
      $(this).parent('.questionSummary').addClass('hasopened');
    });
    $('.questionSummary__options.collapse').on('hide.bs.collapse', function () {
      $(this).parent('.questionSummary').siblings('.questionSummary').removeClass('rest-are-closed');
      $(this).parent('.questionSummary').removeClass('hasopened');
    });
});
</script>

@endsection