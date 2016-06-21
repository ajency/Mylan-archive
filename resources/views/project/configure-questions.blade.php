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
        <form class="form-horizontal col-sm-12" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/configure-questions/'.$questionnaireId ) }}" data-parsley-validate>

        <div class="form-row question-list" id="accordion">
            <?php 
            $i=0;
            ?>
            @if(empty($questionsList))
              <div class="no_question">No Questions added yet !</div>
              <div class="m-b-20">Add a Question to continue</div>
            @else
            @foreach($questionsList as $questionId => $question)
            <?php 
            if($i==0)
            {
              $anchor = "";
              $indicator = "down";
              $containerCollapse = "in";
            }
            else
            {
              $anchor = "collapsed";
              $indicator = "up";
              $containerCollapse = "";
            }
            
            ?>
            
                <?php 
                $isWeight = false;
                $k = 0;
               if(isset($optionsList[$questionId][0]['label'])  && $optionsList[$questionId][0]['label']=="kg" && $optionsList[$questionId][1]['label']=="st" && $optionsList[$questionId][2]['label']=="lb")
                  $isWeight = true;
                ?>
                <div class="row question parentQuestion panel panel-default" row-count="{{ $i }}"> 
                   <input type="hidden" name="questionId[{{ $i }}]" value="{{ $questionId }}">

                   <!-- test -->
                   <div class="col-sm-12 panel-heading questionHead @if(!$isWeight && $question['type']!='descriptive')arrow_box @endif">
                     <div class="row">
                       <div class="col-sm-3">
                         <label>Type of question</label>
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
                       <div class="col-sm-3">
                        <label for="">A short question identifier</label>
                        <input name="title[{{ $i }}]" id="title" type="text" value="{{ $question['title'] }}"   placeholder="Enter Title" class="form-control" data-parsley-required>
                       </div> 
                       <div class="col-sm-4 m-t-30">
                        @if($question['type']=="single-choice" || $question['type']=="multi-choice" || $question['type']=="input")
                         <span class="label label-default">HAS 7 OPTIONS</span>
                          @if($question['type']=="single-choice")
                            <span class="label label-default">HAS SUB QUESTIONS</span>
                          @endif
                        @endif
                        </div>
                        
                        <div class="col-md-2 m-t-25">
                         <div class="clearfix">
                           <div class="pull-right del-question-blk">
                             <button type="button" class="btn btn-white delete-parent-question delete-question" object-id="{{ $questionId }}"><i class="fa fa-trash"></i></button>
                           </div>
                         </div>
                       </div>
                     </div>

                     <div class="row">
                       <div class="col-sm-9">
                         <input name="question[{{ $i }}]" id="question" type="text" value="{{ $question['question'] }}"  placeholder="Enter Question" class="form-control" data-parsley-required>
                       </div>
                       <div class="col-sm-3">
                          @if($question['type']=="single-choice" || $question['type']=="multi-choice" || $question['type']=="input")
                           <a class="accordion-toggle {{ $anchor }}" data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $i }}">
                             <i class="indicator glyphicon glyphicon-chevron-{{ $indicator }}  pull-right"></i>
                          </a>
                          @endif
                       </div>
                     </div>
                   </div>
                   <!-- /test -->

                   <!-- <div class="col-md-12 panel-heading questionHead @if(!$isWeight && $question['type']!='descriptive')arrow_box @endif">
                   <div class="col-sm-3 m-t-15 ">
                   <label>Type of question</label>
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
                   <div class="col-sm-3 m-t-15">
                   <label for="">A short question identifier</label>
                      <input name="title[{{ $i }}]" id="title" type="text" value="{{ $question['title'] }}"   placeholder="Enter Title" class="form-control" data-parsley-required>
                   </div> 
                   <div class="col-sm-5 m-t-15">
                   <label for="">What do you need to ask</label>
                      <input name="question[{{ $i }}]" id="question" type="text" value="{{ $question['question'] }}"  placeholder="Enter Question" class="form-control" data-parsley-required>
                   </div>
                   <div class="col-sm-1 text-center m-t-40 del-question-blk">
                      <button type="button" class="btn btn-white delete-parent-question delete-question" object-id="{{ $questionId }}"><i class="fa fa-trash"></i></button>
                   </div>
                  @if($question['type']=="single-choice" || $question['type']=="multi-choice" || $question['type']=="input")
                   <a class="accordion-toggle {{ $anchor }}" data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $i }}">
                     <i class="indicator glyphicon glyphicon-chevron-{{ $indicator }}  pull-right" style="margin-top: -25px; color: #333;"></i>
                  </a>
                  @endif
                   </div> --><!--/panel-heading-->
      
                   <!-- options -->
                   @if($question['type']=="single-choice" || $question['type']=="multi-choice" || $question['type']=="input")
                   <div class="row panel-collapse collapse {{ $containerCollapse }} p-l-15 p-r-15" id="collapse-{{ $i }}">
                   <!-- <div class="col-sm-1"></div> -->
                    <div class="col-sm-12 question-options-block m-t-20 @if($isWeight) hidden @endif" >
                    <?php 
                      $j=0;
                      ?>
                    @if(isset($optionsList[$questionId]))
                    <div class="row gray-section">
                      <div class="col-md-12">
                        <strong>Enter the options for this question</strong>
                        <p>You can add a sub question too. The score declares the severity of the patient</p>
                      </div>
                      <!-- <div class="col-md-4">
                        <strong>For the sub question</strong>
                        <p>Add the sub question for this option</p>
                      </div> -->
                    </div>
                      
                      @foreach($optionsList[$questionId] as $option)
                      <div class="option-block">
                      <div class="row">
                      <div class="col-sm-1">
                        <label class="p-t-15">option {{ ($j+1) }}</label>
                      </div>
                      <div class="col-sm-11">
                      <div class="optionsDesc">
                        <input type="hidden" name="optionId[{{ $i }}][{{ $j }}]" class="optionId"  value="{{ $option['optionId'] }}">
                        <!-- test -->
                        <div class="row">

                          <div class="col-sm-5 m-t-10 m-b-10">
                          <input name="option[{{ $i }}][{{ $j }}]" id="question" type="text" placeholder="Enter option" value="{{ $option['label'] }}" class="form-control" data-parsley-required>
                          </div>

                          <div class="col-sm-2 m-t-10 m-b-10">
                          <input name="score[{{ $i }}][{{ $j }}]" id="question" type="number" placeholder="Enter score" value="{{ $option['score'] }}" class="form-control" min="0" data-parsley-required> 
                          </div> 

                          @if($question['type']=="single-choice")
                          <div class="col-sm-3 text-center m-t-20">
                          <input type="checkbox" class="js-switch hasSubQuestion" name="hasSubQuestion[{{ $i }}][{{ $j }}]"
                          @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']])) checked @endif/>
                          <small class="help-text">Add sub question</small>
                          <!-- <input type="checkbox" class="hasSubQuestion" name="hasSubQuestion[{{ $i }}][{{ $j }}]"
                          @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']])) checked @endif
                          > -->
                          </div>
                          <div class="col-sm-2 text-right m-t-10 m-b-10">
                          <button type="button" class="btn btn-white delete-option" counter-key="{{ $j }}"><i class="fa fa-trash"></i></button>
                          </div>
                        @else
                          <div class="col-sm-5 text-right m-t-10 m-b-10">
                          <button type="button" class="btn btn-white delete-option" counter-key="{{ $j }}"><i class="fa fa-trash"></i></button>
                          </div>
                        @endif
                        <div class="clearfix"></div>

                        </div><!--/row-->
                        <!-- /test -->
                        
                        
                        </div><!--/optionsDesc-->
                        </div><!--col-sm-11-->
                      </div>

                      <!--****sub Question ****-->
                      <div class="subQuestion-container">
                        @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']]))
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

                            <div class="row question subQuestion-row" row-count="{{ $k }}"> 
                               <input type="hidden" name="questionId[{{ $k }}]" value="{{ $subQuestionId }}">
                               <div class="col-sm-1"></div>
                               <div class="col-sm-10 questionHead sub-question arrow_box-top gray-rbor-section">
                               
                               <div class="col-sm-3">
                               <label>Type of question</label>
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
                               <div class="col-sm-3">
                               <label for="">A short question identifier</label>
                                  <input name="subquestionTitle[{{ $k }}]" id="subquestionTitle" type="text" value="{{ $subQuestion['title'] }}"   placeholder="Enter Title" class="form-control" data-parsley-required>
                               </div> 
                               <div class="col-sm-5 m-t-25">
                                  <input name="subquestion[{{ $k }}]" id="subquestion" type="text" value="{{ $subQuestion['question'] }}"  placeholder="Enter Question" class="form-control" data-parsley-required>
                               </div>
                               <div class="col-sm-1 text-center m-t-25 del-question-blk">
                                  <button type="button" class="btn btn-white delete-question" object-id="{{ $subQuestionId }}"><i class="fa fa-trash"></i></button>
                               </div>
                               </div>
                               <div class="clearfix"></div>
                               <!-- options -->
                               @if($subQuestion['type']=="single-choice" || $subQuestion['type']=="multi-choice" || $subQuestion['type']=="input")
                               <div class="row p-l-15 p-r-15">
                               <div class="col-sm-1"></div>
                                <div class="col-sm-10 gray-rbor-section question-options-block @if($isWeight) hidden @endif" >
                                <?php 
                                  $l=0;
                                  ?>
                                @if(isset($optionsList[$subQuestionId]))
                                  
                                  @foreach($optionsList[$subQuestionId] as $option)
                                  <div class="option-block">
                                  <div class="row p-l-15 p-r-15">
                                    <input type="hidden" name="optionId[{{ $k }}][{{ $l }}]" class="optionId"  value="{{ $option['optionId'] }}">
                                    <div class="col-sm-2">
                                      <label class="p-t-15">option {{ ($l+1) }}</label>
                                    </div>
                                    <div class="col-sm-5 m-t-10 m-b-10">
                                    <input name="option[{{ $k }}][{{ $l }}]" id="option" type="text" placeholder="Enter option" value="{{ $option['label'] }}" class="form-control" data-parsley-required>
                                    </div>
                                    <div class="col-sm-2 m-t-10 m-b-10">
                                    <input name="score[{{ $k }}][{{ $l }}]" id="score" type="number" placeholder="Enter score" value="{{ $option['score'] }}" class="form-control" min="1" data-parsley-required>
                                    </div> 
                                     <div class="col-sm-2 text-right m-t-10 m-b-10 width-23">
                                      <button type="button" class="btn btn-white delete-option" counter-key="{{ $l }}"><i class="fa fa-trash"></i></button>
                                      </div>
                                    <div class="subQuestion-container">

                                    </div>
                                  </div>
                                  </div>
                                  <?php 
                                  $l++;
                                  ?>
                                  @endforeach
                                @endif
                                <div class="option-block">
                                  <div class="row p-l-15 p-r-15">
                                    <input type="hidden" name="optionId[{{ $k }}][{{ $l }}]"  class="optionId" value="">
                                    <div class="col-sm-2"><label class="p-t-15">option {{ ($l+1) }}</label></div>
                                    <div class="col-sm-5 m-t-10 m-b-10 ">
                                    <input name="option[{{ $k }}][{{ $l }}]" id="question" type="text" placeholder="Enter option"  class="form-control" >
                                    </div>
                                    <div class="col-sm-2 m-t-10 m-b-10">
                                    <input name="score[{{ $k }}][{{ $l }}]" id="question" type="number" placeholder="Enter score" class="form-control" min="1">
                                    </div> 
                                    <div class="col-sm-2 text-right m-t-10 m-b-10 width-23">
                                    <button type="button" class="btn btn-white add-option" counter-key="{{ $l }}">Another Option <i class="fa fa-plus"></i></button>
                                    </div>
                                    <div class="subQuestion-container"></div>
                                  </div>
                                </div> 
                              </div>
                              </div> 
                                @endif
                                <!--  -->
                          </div>
                        @endif
                      </div>
                      <!--****/sub Question ****-->
                      </div><!--/option-block-->
                        <!-- <hr class="customHR"> -->
                      <?php 
                      $j++;
                      ?>
                      @endforeach
                    @endif
                    <div class="option-block">
                      <div class="row">
                        <div class="col-sm-1">
                          <label class="p-t-15">option {{ ($j+1) }}</label>
                        </div>

                        <div class="col-sm-11">
                        <div class="optionsDesc">
                        <input type="hidden" name="optionId[{{ $i }}][{{ $j }}]" class="optionId"  value="">
                        <div class="row">
                        <div class="col-sm-5 m-t-10 m-b-10 ">
                        
                        <input name="option[{{ $i }}][{{ $j }}]" id="question" type="text" placeholder="Enter option"  class="form-control" >
                        </div>
                        <div class="col-sm-2 m-t-10 m-b-10">
                        
                        <input name="score[{{ $i }}][{{ $j }}]" id="question" type="number" placeholder="Enter score" class="form-control" min="0" >
                        </div> 
                        @if($question['type']=="single-choice")
                          <div class="col-sm-3 text-center m-t-20">
                          <input type="checkbox" class="js-switch hasSubQuestion" name="hasSubQuestion[{{ $i }}][{{ $j }}]" >
                          <small class="help-text">Add sub question</small>
                          </div>
                          <div class="col-sm-2 text-right m-t-10 m-b-10">
                          <button type="button" class="btn btn-white add-option" counter-key="{{ $j }}"><i class="fa fa-plus"></i></button>
                          </div>
                        @else
                          <div class="col-sm-5 text-right m-t-10 m-b-10">
                          <button type="button" class="btn btn-white add-option" counter-key="{{ $j }}"><i class="fa fa-plus"></i></button>
                          </div>
                        @endif
                        <div class="clearfix"></div>
                        </div>
                        </div>
                        </div>
                      </div>
                        <div class="subQuestion-container"></div>
                    </div>
                  </div> 
                    <div class="col-sm-1"></div>
                  </div><!-- /panel-collapse -->
   
                    @endif
                    
                    <!--  -->
              </div><!--/parentQuestion-->
           <?php 
 
            $i= ($k==0)?$i+1:$k+1;
            ?>
            @endforeach
            @endif
            
            <!-- <div class="row question parentQuestion  panel panel-default" row-count="{{ $i }}">
               <input type="hidden" name="questionId[{{ $i }}]" value="">
               <div class="col-md-12 questionHead panel-heading">
               <div class="col-sm-3 m-t-15">
                  <label>Type of question</label>
                  <select name="questionType[{{ $i }}]" class="select2-container select2 form-control questionType">
                      <option value="">Select Question Type</option>
                      <option value="single-choice"> Single-choice</option>
                      <option value="multi-choice">Multi-choice</option>
                      <option value="input"> Input</option>
                      <option value="descriptive"> Descriptive </option>
                      <option value="input" data-value="weight"> Weight </option>
                  </select>
               </div>
               <div class="col-sm-3 m-t-15">
                  <label for="">A short question identifier</label>
                  <input name="title[{{ $i }}]" id="title" type="text"   placeholder="Enter Title" class="form-control" >
               </div> 
               <div class="col-sm-5 m-t-15">
               <label for="">What do you need to ask</label>
                  <input name="question[{{ $i }}]" id="question" type="text"   placeholder="Enter Question" class="form-control" >
               </div>
               <div class="col-sm-1 text-center m-t-15 del-question-blk">
                  <button type="button" class="btn btn-white delete-question delete-parent-question hidden" object-id=""><i class="fa fa-trash"></i></button>
               </div>
            </div>
          </div> -->
  </div><!--/form-row-->
        <input type="hidden" name="counter" id="counter" value="{{ $i }}">
        <button type="button" class="btn btn-link text-success add-question"><i class="fa fa-plus"></i> Add Question</button>
        <div class="form-group">
          <div class="col-sm-10 questionActions mri-submit">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
          <input type="hidden" value="" name="redirect_url"/>
            <!-- <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}"> -->
            <button type="button" class="btn btn-default validateAndRedirect" url="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}"><i class="fa fa-backward" aria-hidden="true"></i> Questionnaire Settings</button>
            <!-- </a> -->
            <button type="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Save</button>
            <!-- <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/order-questions/'.$questionnaireId ) }}"> -->
            <button type="button" class="btn btn-default validateAndRedirect" url="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/order-questions/'.$questionnaireId ) }}">Order Questions <i class="fa fa-forward" aria-hidden="true"></i></button>
            <!-- </a> -->
          </div>
        </div>

        </form>
              
                     </div>
                  </div>
 
 
<script type="text/javascript">
  
$(function(){

  var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

  elems.forEach(function(html) {
    var switchery = new Switchery(html, { color: '#0aa699', size: 'small' });
  });

});

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