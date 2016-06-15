
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
         <div class="col-md-12 questionHead sub-question arrow_box-top">
         <div class="col-sm-3 m-t-15 ">
            <input type="hidden" name="optionKeys[{{ $i }}][{{ $j }}]" value="{{ $k }}">
            <select name="subquestionType[{{ $k }}]" class="select2-container select2 form-control subquestionType questionType" disabled="">
                <option selected value="">Select Question Type</option>
                <option @if($subQuestion['type']=="single-choice") selected @endif value="single-choice"> Single-choice</option>
                <option @if($subQuestion['type']=="multi-choice") selected @endif value="multi-choice">Multi-choice</option>
                <option @if($subQuestion['type']=="input") selected @endif value="input"> Input</option>
                <option @if($subQuestion['type']=="input" && $isWeight) selected @endif value="input" data-value="weight"> Weight </option>
                <option @if($subQuestion['type']=="descriptive") selected @endif value="descriptive"> Descriptive </option>
            </select>
            <input type="hidden" name="subquestionType[{{ $k }}]"  value="{{ $question['type'] }}">
         </div>
         <div class="col-sm-2 m-t-15">
            <input name="subquestionTitle[{{ $k }}]" id="subquestionTitle" type="text" value="{{ $subQuestion['title'] }}"   placeholder="Enter Title" class="form-control" >
         </div> 
         <div class="col-sm-6 m-t-15">
            <input name="subquestion[{{ $k }}]" id="subquestion" type="text" value="{{ $subQuestion['question'] }}"  placeholder="Enter Question" class="form-control" >
         </div>
         <div class="col-sm-1 text-center m-t-15 del-question-blk">
            <button type="button" class="btn btn-white delete-question" object-id="{{ $subQuestionId }}"><i class="fa fa-trash"></i></button>
         </div>
         </div>
         <!-- options -->
         @if($subQuestion['type']=="single-choice" || $subQuestion['type']=="multi-choice" || $subQuestion['type']=="input")
          <div class="col-sm-8 col-sm-offset-2 m-t-15 question-options-block @if($isWeight) hidden @endif" >
          <?php 
            $l=0;
            ?>
          @if(isset($optionsList[$subQuestionId]))
            
            @foreach($optionsList[$subQuestionId] as $option)
            <div class="option-block">
            <div class="row">
              <input type="hidden" name="optionId[{{ $k }}][{{ $l }}]" class="optionId"  value="{{ $option['optionId'] }}">
              <div class="col-sm-7 m-t-10 m-b-10">
              <input name="option[{{ $k }}][{{ $l }}]" id="option" type="text" placeholder="Enter option" value="{{ $option['label'] }}" class="form-control" >
              </div>
              <div class="col-sm-3 m-t-10 m-b-10">
              <input name="score[{{ $k }}][{{ $l }}]" id="score" type="number" placeholder="Enter score" value="{{ $option['score'] }}" class="form-control" min="1">
              </div> 
               <div class="col-sm-2 text-center m-t-10 m-b-10">
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
            <div class="row">
              <input type="hidden" name="optionId[{{ $k }}][{{ $l }}]"  class="optionId" value="">
              <div class="col-sm-7 m-t-10 m-b-10 ">
              <input name="option[{{ $k }}][{{ $l }}]" id="question" type="text" placeholder="Enter option"  class="form-control" >
              </div>
              <div class="col-sm-3 m-t-10 m-b-10">
              <input name="score[{{ $k }}][{{ $l }}]" id="question" type="number" placeholder="Enter score" class="form-control" min="1">
              </div> 
              <div class="col-sm-2 text-center m-t-10 m-b-10">
              <button type="button" class="btn btn-white add-option" counter-key="{{ $l }}"><i class="fa fa-plus"></i></button>
              </div>
              <div class="subQuestion-container"></div>
            </div>
          </div> 
        </div> 
          @endif
          <!--  -->
    </div>
  @endif
