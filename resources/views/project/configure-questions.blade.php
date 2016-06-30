@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="#" class="active">Configure Questionnaire</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div>
                    
                     <div class="page-title">
                        <h3><span class="semi-bold">Configure Questionnaire</span></h3>
                     </div>
                  </div>
                  Add questions to your questionnaire by selecting the required question type from the drop-down. You can also add sub questions for question type 'Single choice'. Once questions are added your can reorder the questions and proceed to publish.                 
                   
                          
                           <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                       <h3 class="">{{ $questionnaireName }}</h3>
                      
                      <hr>
          @include('admin.flashmessage')
        
        
        <div class="questions-list_container clearfix">
          <div class="row questions-list__header @if(empty($questionsList)) hidden @endif ">
            <div class="col-sm-3">Question Identifier</div>
            <div class="col-sm-4">The Question</div>
            <div class="col-sm-1 text-center">Options</div>
            <div class="col-sm-2 text-center">Sub Questions</div>
            <div class="col-sm-2"></div>
          </div>
          <?php 
          $i=0;
          ?>
          @if(!empty($questionsList))
            @foreach($questionsList as $questionId => $question)
              <?php 
              $isWeight = false;
              $k = 0;
             if(isset($optionsList[$questionId][0]['label'])  && $optionsList[$questionId][0]['label']=="kg" && $optionsList[$questionId][1]['label']=="st" && $optionsList[$questionId][2]['label']=="lb")
                $isWeight = true;
              ?>
            <form class="form-horizontal col-sm-12 p-l-0 p-r-0" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/configure-questions/'.$questionnaireId ) }}" data-parsley-validate>
            <div class="question-view-edit">
                <div class="row questions-list question-view question" row-count="{{ $i }}">
                  <div class="col-sm-3">
                    <div class="black question-title">{{ $question['title'] }}</div>
                    <div class="type question-type">TYPE: {{ strtoupper($question['type']) }}</div>
                  </div>
                  <div class="col-sm-4">
                    <div class="bold question-text">{{ $question['question'] }}</div>
                  </div>
                  <div class="col-sm-1">
                    <div class="text-center question-option-count">{{ (isset($optionsList[$questionId]) && !$isWeight)? count($optionsList[$questionId]):'' }}</div>
                  </div>
                  <div class="col-sm-2">
                    <div class="text-center has-subquestion">
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
                  </div>
                  <div class="col-sm-2">
                    <div class="clearfix">
                     <input type="hidden" name="previousquestionId[{{ $i }}]" value="">
                     <input type="hidden" name="questionId[{{ $i }}]" value="{{ $questionId }}">
                      <span class="pull-left edit-link edit-question cp">EDIT</span>
                      <i class="pull-right fa fa-trash delete-parent-question delete-question cp" object-id="{{ $questionId }}"></i>
                    </div>
                  </div>
                </div>
            

            <div class="main-question_container question-edit hidden question" row-count="{{ $i }}">
          
            <!-- <div class="row main-question__header">
              <div class="col-sm-3">Question Identifier</div>
              <div class="col-sm-4">The Question</div>
              <div class="col-sm-1 text-center">Options</div>
              <div class="col-sm-2 text-center">Sub Questions</div>
              <div class="col-sm-2"></div>
            </div> -->
          

            <div class="type-questions parentQuestion">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="">Type of question</label> 
                    <select name="questionType[{{ $i }}]" class="select2-container select2 form-control questionType" disabled data-parsley-required>
                        <option selected value="">Select the question type</option>
                        <option @if($question['type']=="single-choice") selected @endif value="single-choice"> Single-choice</option>
                        <option @if($question['type']=="multi-choice") selected @endif value="multi-choice">Multi-choice</option>
                        <option @if($question['type']=="input") selected @endif value="input"> Input</option>
                        <option @if($question['type']=="input" && $isWeight) selected @endif value="input" data-value="weight"> Weight </option>
                        <option @if($question['type']=="descriptive") selected @endif value="descriptive"> Descriptive </option>
                    </select>
                    <input type="hidden" name="questionType[{{ $i }}]"  value="{{ $question['type'] }}">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="">A short question identifier</label>
                    <input name="title[{{ $i }}]" id="title" type="text" value="{{ $question['title'] }}"   placeholder="Enter Identifier" class="form-control" data-parsley-required>
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="text-center question-option-count">{{ (isset($optionsList[$questionId]) && !$isWeight)? count($optionsList[$questionId]):'' }}</div>
                </div>
                <div class="col-sm-2">
                  <div class="text-center">
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
                </div>
                <div class="col-sm-2">
                  <a href="" class="pull-right">
                    <i class="fa fa-trash text-danger delete-parent-question delete-question pull-right cp" object-id="{{ $questionId }}"></i>
                    
                  </a>
                </div>
              </div>

              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">
                    <input name="question[{{ $i }}]" id="question" type="text" value="{{ $question['question'] }}"  placeholder="Enter Question" class="form-control" data-parsley-required>
                  </div>
                </div>
              </div>
              </div><!--/type-question-->
              <?php 
                $j=0;
                ?>
              @if(isset($optionsList[$questionId]))
              <div class="options-container parent-question-options question-options-block @if($isWeight) hidden @endif">
                <div class="row heading-title m-b-15">
                  <div class="col-md-12">
                    <span class="bold">Enter the options for this question</span>
                    <div>You can add a subquestion too. The score declairs severity of patient.</div>
                  </div>
                </div>
                
                @foreach($optionsList[$questionId] as $option)
                <div class="parent-option-container options-list_container">
                  <div class="row options-list m-t-15">
                    <div class="col-sm-1 cust-col-sm-1">
                      <label for="" class="m-t-10">option {{ ($j+1) }}</label>
                      <input type="hidden" name="optionId[{{ $i }}][{{ $j }}]" class="optionId"  value="{{ $option['optionId'] }}">
                    </div>
                    <div class="col-sm-4">
                      <input name="option[{{ $i }}][{{ $j }}]" id="question" type="text" placeholder="Enter option" value="{{ $option['label'] }}" class="form-control" data-parsley-required>
                    </div>

                    <div class="col-sm-1 text-right">
                      <label for="" class="m-t-10">score</label>
                    </div>
                    <div class="col-sm-2 cust-col-sm-2">
                      <input name="score[{{ $i }}][{{ $j }}]" id="question" type="number" placeholder="Enter score" value="{{ $option['score'] }}" class="form-control" min="0" data-parsley-required>
                    </div>

                    <div class="col-sm-4">
                      <i class="fa fa-remove m-t-10 delete-parent-question-option delete-option cp" counter-key="{{ $j }}"></i>
                    </div>
                  </div>
                  
                  <div class="row">
                  @if($question['type']=="single-choice")
                    <input type="checkbox" class="hidden hasSubQuestion" name="hasSubQuestion[{{ $i }}][{{ $j }}]"
                          @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']])) checked @endif/>
                  @endif
                    <div class="col-sm-11 col-sm-offset-1 sub-question">

                      
                      <!-- sub question -->
                    @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']]))
                      <span class="sh-link toggle-subquestion cp p-l-20">SHOW SUB QUESTION</span> <span class="subquestion-error-message alert alert-danger cust-alert-padd hidden"><i class="fa fa-exclamation-triangle"></i> Please fill required fields for these sub-question</span>
                      <?php
                      $subQuestionId = $question['condition'][$option['optionId']];
                      $subQuestion = $subQuestions[$questionId][$subQuestionId];
                      $k = ($k==0)? $i+1 : $k+1;

                      $isWeight = false;
                      if(isset($optionsList[$subQuestionId][0]['label'])  && $optionsList[$subQuestionId][0]['label']=="kg" && $optionsList[$subQuestionId][1]['label']=="st" && $optionsList[$subQuestionId][2]['label']=="lb")
                       {
                          $isWeight = true;
                       }
                      ?>
                      <div class="subquestion-container question hidden " row-count="{{ $k }}">
                        <input type="hidden" name="questionId[{{ $k }}]" value="{{ $subQuestionId }}">
                        <div class="clearfix">
                          <span class="bold pull-left">Edit this Subquestion</span>
                          <a href="" class="fa fa-trash text-danger pull-right delete-question" object-id="{{ $subQuestionId }}"></a>
                        </div>

                        <div class="type-questions">
                          <div class="row">
                            <div class="col-sm-3">
                              <div class="form-group">
                                <label for="">Type of question</label>
                                    <input type="hidden" name="optionKeys[{{ $i }}][{{ $j }}]" value="{{ $k }}">
                                    <select name="subquestionType[{{ $k }}]" class="select2-container select2 form-control subquestionType questionType" disabled="" data-parsley-required>
                                        <option selected value="">Select Question Type</option>
                                        <option @if($subQuestion['type']=="single-choice") selected @endif value="single-choice"> Single-choice</option>
                                        <option @if($subQuestion['type']=="multi-choice") selected @endif value="multi-choice">Multi-choice</option>
                                        <option @if($subQuestion['type']=="input") selected @endif value="input"> Input</option>
                                        <option @if($subQuestion['type']=="input" && $isWeight) selected @endif value="input" data-value="weight"> Weight </option>
                                        <option @if($subQuestion['type']=="descriptive") selected @endif value="descriptive"> Descriptive </option>
                                    </select>
                                    <input type="hidden" name="subquestionType[{{ $k }}]"  value="{{ $question['type'] }}">
                              </div>
                            </div>
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label for="">A short question identifier</label>
                                <input name="subquestionTitle[{{ $k }}]" id="subquestionTitle" type="text" value="{{ $subQuestion['title'] }}"   placeholder="Enter Identifier" class="form-control" data-parsley-required>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-9">
                              <div class="form-group">
                                <input name="subquestion[{{ $k }}]" id="subquestion" type="text" value="{{ $subQuestion['question'] }}"  placeholder="Enter Question" class="form-control" data-parsley-required>
                              </div>
                            </div>
                          </div>
                        </div><!--/type-question-->
                        <?php 
                          $l=0;
                          ?>
                        <div class="question-options-block">
                        <span class="bold m-t-15">Enter the option for this sub question</span>
                        @if(isset($optionsList[$subQuestionId]))

                        @foreach($optionsList[$subQuestionId] as $option)
                        <div class="options-list_container m-t-5 m-b-5">
                          <div class="row options-list">
                            <div class="col-sm-2 option-label">
                              <input type="hidden" name="optionId[{{ $k }}][{{ $l }}]" class="optionId"  value="{{ $option['optionId'] }}">
                              <label for="" class="m-t-10">option {{ ($l+1) }}</label>
                            </div>
                            <div class="col-sm-4">
                              <input name="option[{{ $k }}][{{ $l }}]" id="option" type="text" placeholder="Enter option" value="{{ $option['label'] }}" class="form-control" data-parsley-required>
                            </div>
                            <div class="col-sm-1 text-right">
                              <label for="" class="m-t-10">score</label>
                            </div>
                            <div class="col-sm-2">
                              <input name="score[{{ $k }}][{{ $l }}]" id="score" type="number" placeholder="Enter score" value="{{ $option['score'] }}" class="form-control" min="0" data-parsley-required>
                            </div>

                            <div class="col-sm-3">
                             <i class="fa fa-close m-t-10 delete-option cp" counter-key="{{ $l }}"></i> 
                            </div>
                          </div>
                        </div><!--/options-list_container-->
                        <?php 
                          $l++;
                          ?>
                          @endforeach

                        @endif
                         
                        <div class="options-list_container m-b-5">
                          <div class="row options-list">
                            <div class="col-sm-2 option-label">
                            <input type="hidden" name="optionId[{{ $k }}][{{ $l }}]"  class="optionId" value="">
                              <label for="" class="m-t-10">option {{ ($l+1) }}</label>
                            </div>
                            <div class="col-sm-4">
                              <input name="option[{{ $k }}][{{ $l }}]" id="question" type="text" placeholder="Enter option"  class="form-control" >
                            </div>
                            <div class="col-sm-1 text-right">
                              <label for="" class="m-t-10">score</label>
                            </div>
                            <div class="col-sm-2">
                              <input name="score[{{ $k }}][{{ $l }}]" id="question" type="number" placeholder="Enter score" value="0" class="form-control" min="0">
                            </div>

                            <div class="col-sm-3 add-delete-container">
                            <span href="" class="btn btn-default outline-btn-gray add-option" counter-key="{{ $l }}">Another Option <i class="fa fa-plus"></i></span>

                            </div>
                          </div>
                        </div><!--/options-list_container-->
                         </div> 

                      </div><!--/subquestion-container-->
                    @else
                      @if($question['type']=="single-choice")
                        <span  class="add-link add-sub-question p-l-20">ADD SUB QUESTION</span>
                      @endif  
                    @endif

                    </div>
                  </div>
                </div><!--/options-list_container-->
                <?php 
                  $j++;
                  ?>
                @endforeach
                <div class="parent-option-container options-list_container p-b-10">
                  <div class="row options-list">
                    <div class="col-sm-1 cust-col-sm-1">
                      <label for="" class="m-t-10">option {{ ($j+1) }}</label>
                      <input type="hidden" name="optionId[{{ $i }}][{{ $j }}]" class="optionId"  value="">
                    </div>
                    <div class="col-sm-4">
                      <input name="option[{{ $i }}][{{ $j }}]" id="question" type="text" placeholder="Enter option"  class="form-control" >
                    </div>

                    <div class="col-sm-1 text-right">
                      <label for="" class="m-t-10">score</label>
                    </div>
                    <div class="col-sm-2 cust-col-sm-2">
                      <input name="score[{{ $i }}][{{ $j }}]" id="question" type="number" placeholder="Enter score" value="0" class="form-control" min="0" >
                    </div>

                    <div class="col-sm-4 add-delete-container">
                      <div class="clearfix">
                        <span class="btn btn-default pull-right outline-btn-gray add-option" counter-key="{{ $j }}">Another option <i class="fa fa-plus"></i></span>
                      </div>
                    </div>
                     
                  </div>
                  <div class="row">
                        
                    @if($question['type']=="single-choice")
                      <input type="checkbox" class="hidden hasSubQuestion" name="hasSubQuestion[{{ $i }}][{{ $j }}]" />
                      <div class="col-sm-11 col-sm-offset-1 sub-question">
                      <span  class="add-link add-sub-question p-l-20">ADD SUB QUESTION</span>
                    </div>
                     @endif 
                  </div>
                </div><!--/options-list_container-->

              </div><!--/options-container-->
              @endif

              <div class="row options-container_footer">
                <div class="col-md-12">
                  <div class="clearfix">
                    <button type="button"  class="btn btn-primary pull-right save-question">SAVE</button>
                    <button type="button" class="btn btn-default pull-right cancel-question m-r-10">CANCEL</button>
                  </div>
                </div>
              </div>
              

            </div><!-- /main-question_container -->
        </div>
        </form>
          <?php 
          $i= ($k==0)?$i+1:$k+1;
          ?>
          @endforeach
        @endif

         <div class="no_question @if(!empty($questionsList)) hidden @endif">
            <div >No Questions added yet !</div>
            <small class="m-b-20">Add a Question to continue</small>
          </div>

        </div><!--/question-lists_contaoner-->

        <!-- test -->
        <div class="clearfix">
 
          <button type="button" class="btn btn-link text-success add-question pull-right outline-btn m-b-30">Add @if(!empty($questionsList)) another @endif Question</button>
 
        </div>
        <div class="form-group">
          <div class="questionActions mri-submit text-center">
          <input type="hidden" name="counter" id="counter" value="{{ $i }}">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
          <input type="hidden" value="" name="redirect_url"/>

          <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}" class="questionnaire-settings @if(empty($questionsList)) hidden @endif">
             <button type="button" class="btn btn-link cust-link pull-left" ><i class="fa fa-angle-left" aria-hidden="true"></i> Questionnaire Settings</button>
         </a>
      
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/order-questions/'.$questionnaireId ) }}" class="question-reorder @if(empty($questionsList)) hidden @endif">
            <button type="button" class="btn btn-link cust-link">Reorder the Questions</button>
            </a>

            <button class="btn btn-primary pull-right @if(empty($questionsList)) hidden @endif publish-question">PUBLISH</button>
       
          </div>
        </div>
 
              
                     </div>
                  </div>
 
 
<script type="text/javascript">
  
 
var submitUrl = "{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/configure-questions/'.$questionnaireId ) }}";

$( document ).ready(function() {
    function toggleChevron(e) {
    $(e.target)
        .prev('.panel-heading')
        .find("i.indicator")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
}
$('#accordion').on('hidden.bs.collapse', toggleChevron);
$('#accordion').on('shown.bs.collapse', toggleChevron);
});  
</script>
@endsection