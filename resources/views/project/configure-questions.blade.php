@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="#" class="active">Questionnaire Settings</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div>
                    
                     <div class="page-title">
                        <h3><span class="semi-bold">Settings</span></h3>
                     </div>
                  </div>
                                   
                   
                          
                           <div class="grid simple">
                        <div class="grid-body no-border table-data">
                           <br>
                       <h3 class="">{{ $project['name'] }}</h3>
                      
                      <hr>
          @include('admin.flashmessage')
        <form class="form-horizontal col-sm-12" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/configure-questions/'.$questionnaireId ) }}" data-parsley-validate>

        <div class="form-row question-list">
            <?php 
            $i=0;
            ?>
            @foreach($questionsList as $questionId => $question)
                <?php 
                $isWeight = false;
                $k = 0;
               if(isset($optionsList[$questionId][0]['label'])  && $optionsList[$questionId][0]['label']=="kg" && $optionsList[$questionId][1]['label']=="st" && $optionsList[$questionId][2]['label']=="lb")
                  $isWeight = true;
                ?>
                <div class="row question parentQuestion" row-count="{{ $i }}">
                   <input type="hidden" name="questionId[{{ $i }}]" value="{{ $questionId }}">
                   <div class="col-md-12 questionHead @if(!$isWeight && $question['type']!='descriptive')arrow_box @endif">
                   <div class="col-sm-3 m-t-15 ">
                      <select name="questionType[{{ $i }}]" class="select2-container select2 form-control questionType" disabled>
                          <option selected value="">Select Question Type</option>
                          <option @if($question['type']=="single-choice") selected @endif value="single-choice"> Single-choice</option>
                          <option @if($question['type']=="multi-choice") selected @endif value="multi-choice">Multi-choice</option>
                          <option @if($question['type']=="input") selected @endif value="input"> Input</option>
                          <option @if($question['type']=="input" && $isWeight) selected @endif value="input" data-value="weight"> Weight </option>
                          <option @if($question['type']=="descriptive") selected @endif value="descriptive"> Descriptive </option>
                      </select>
                      <input type="hidden" name="questionType[{{ $i }}]"  value="{{ $question['type'] }}">
                   </div>
                   <div class="col-sm-2 m-t-15">
                      <input name="title[{{ $i }}]" id="title" type="text" value="{{ $question['title'] }}"   placeholder="Enter Title" class="form-control" >
                   </div> 
                   <div class="col-sm-6 m-t-15">
                      <input name="question[{{ $i }}]" id="question" type="text" value="{{ $question['question'] }}"  placeholder="Enter Question" class="form-control" >
                   </div>
                   <div class="col-sm-1 text-center m-t-15 del-question-blk">
                      <button type="button" class="btn btn-white delete-parent-question delete-question" object-id="{{ $questionId }}"><i class="fa fa-trash"></i></button>
                   </div>
                   </div>
                   <!-- options -->
                   @if($question['type']=="single-choice" || $question['type']=="multi-choice" || $question['type']=="input")
                   <div class="row">
                   <div class="col-sm-1"></div>
                    <div class="col-sm-10 question-options-block m-t-20 @if($isWeight) hidden @endif" >
                    <?php 
                      $j=0;
                      ?>
                    @if(isset($optionsList[$questionId]))
                      
                      @foreach($optionsList[$questionId] as $option)
                      <div class="option-block">
                      <div class="row">
                      <div class="optionsDesc">
                        <input type="hidden" name="optionId[{{ $i }}][{{ $j }}]" class="optionId"  value="{{ $option['optionId'] }}">
                        <div class="col-sm-7 m-t-10 m-b-10">
                        <input name="option[{{ $i }}][{{ $j }}]" id="question" type="text" placeholder="Enter option" value="{{ $option['label'] }}" class="form-control" >
                        </div>
                        <div class="col-sm-3 m-t-10 m-b-10">
                        <input name="score[{{ $i }}][{{ $j }}]" id="question" type="number" placeholder="Enter score" value="{{ $option['score'] }}" class="form-control" min="1">
                        </div> 
                        @if($question['type']=="single-choice")
                          <div class="col-sm-1 text-center m-t-15 m-b-15">
                          <input type="checkbox" class="js-switch hasSubQuestion" name="hasSubQuestion[{{ $i }}][{{ $j }}]"
                          @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']])) checked @endif/>
                          <!-- <input type="checkbox" class="hasSubQuestion" name="hasSubQuestion[{{ $i }}][{{ $j }}]"
                          @if(!empty($question['condition']) && isset($question['condition'][$option['optionId']])) checked @endif
                          > -->
                          </div>
                          <div class="col-sm-1 text-center m-t-10 m-b-10">
                          <button type="button" class="btn btn-white delete-option" counter-key="{{ $j }}"><i class="fa fa-trash"></i></button>
                          </div>
                        @else
                          <div class="col-sm-2 text-center m-t-10 m-b-10">
                          <button type="button" class="btn btn-white delete-option" counter-key="{{ $j }}"><i class="fa fa-trash"></i></button>
                          </div>
                        @endif
                        <div class="clearfix"></div>
                        </div>
                      </div>

                      <!--****sub Question ****-->
                      <div class="subQuestion-container">
                        @include('project.sub-questions')
                      </div>
                      <!--****/sub Question ****-->
                      </div>
                        <!-- <hr class="customHR"> -->
                      <?php 
                      $j++;
                      ?>
                      @endforeach
                    @endif
                    <div class="option-block">
                      <div class="row">
                        <div class="optionsDesc">
                        <input type="hidden" name="optionId[{{ $i }}][{{ $j }}]" class="optionId"  value="">
                        <div class="col-sm-7 m-t-10 m-b-10 ">
                        <input name="option[{{ $i }}][{{ $j }}]" id="question" type="text" placeholder="Enter option"  class="form-control" >
                        </div>
                        <div class="col-sm-3 m-t-10 m-b-10">
                        <input name="score[{{ $i }}][{{ $j }}]" id="question" type="number" placeholder="Enter score" class="form-control" min="1">
                        </div> 
                        @if($question['type']=="single-choice")
                          <div class="col-sm-1 text-center m-t-10 m-b-10">
                          <input type="checkbox" class="js-switch hasSubQuestion" name="hasSubQuestion[{{ $i }}][{{ $j }}]" >
                          </div>
                          <div class="col-sm-1 text-center m-t-10 m-b-10">
                          <button type="button" class="btn btn-white add-option" counter-key="{{ $j }}"><i class="fa fa-plus"></i></button>
                          </div>
                        @else
                          <div class="col-sm-2 text-center m-t-10 m-b-10">
                          <button type="button" class="btn btn-white add-option" counter-key="{{ $j }}"><i class="fa fa-plus"></i></button>
                          </div>
                        @endif
                        <div class="clearfix"></div>
                        </div>
                      </div>
                        <div class="subQuestion-container"></div>
                    </div>
                  </div> 
                    <div class="col-sm-1"></div>
                  </div>
                    @endif
                    
                    <!--  -->
              </div>
           <?php 
            $i= ($k==0)?$i+1:$k+1;
            ?>
            @endforeach
            
            <div class="row question parentQuestion" row-count="{{ $i }}">
               <input type="hidden" name="questionId[{{ $i }}]" value="">
               <div class="col-md-12 questionHead">
               <div class="col-sm-3 m-t-15">
                  <select name="questionType[{{ $i }}]" class="select2-container select2 form-control questionType">
                      <option value="">Select Question Type</option>
                      <option value="single-choice"> Single-choice</option>
                      <option value="multi-choice">Multi-choice</option>
                      <option value="input"> Input</option>
                      <option value="descriptive"> Descriptive </option>
                      <option value="input" data-value="weight"> Weight </option>
                  </select>
               </div>
               <div class="col-sm-2 m-t-15">
                  <input name="title[{{ $i }}]" id="title" type="text"   placeholder="Enter Title" class="form-control" >
               </div> 
               <div class="col-sm-6 m-t-15">
                  <input name="question[{{ $i }}]" id="question" type="text"   placeholder="Enter Question" class="form-control" >
               </div>
               <div class="col-sm-1 text-center m-t-15 del-question-blk">
                  <button type="button" class="btn btn-white delete-question delete-parent-question hidden" object-id=""><i class="fa fa-trash"></i></button>
               </div>
            </div>
          </div>
  </div>
        <input type="hidden" name="counter" id="counter" value="{{ $i }}">
        <button type="button" class="btn btn-link text-success add-question"><i class="fa fa-plus"></i> Add Question</button>
        <div class="form-group">
          <div class="col-sm-10 questionActions mri-submit">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}">
            <button type="button" class="btn btn-default"><i class="fa fa-backward" aria-hidden="true"></i> Questionnaire Settings</button></a>
            <button type="button" class="btn btn-primary save-questions"><i class="fa fa-check" aria-hidden="true"></i> Save</button>
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/order-questions/'.$questionnaireId ) }}"><button type="button" class="btn btn-default">Order Questions <i class="fa fa-forward" aria-hidden="true"></i></button></a>
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
  
</script>
@endsection