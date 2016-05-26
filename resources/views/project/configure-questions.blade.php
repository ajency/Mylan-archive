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
                <div class="row question" row-count="{{ $i }}">
                   <input type="hidden" name="questionId[]" value="{{ $questionId }}">
                   <div class="col-sm-3 m-t-25 ">
                      <select name="questionType[]" class="select2-container select2 form-control">
                          <option selected value="">Select Question Type</option>
                          <option @if($question['type']=="single-choice") selected @endif value="single-choice"> Single-choice</option>
                          <option @if($question['type']=="multi-choice") selected @endif value="multi-choice">Multi-choice</option>
                          <option @if($question['type']=="input") selected @endif value="input"> Input</option>
                          <option @if($question['type']=="descriptive") selected @endif value="descriptive"> Descriptive </option>
                          <option @if($question['type']=="weight") selected @endif value="weight"> Weight </option>
                      </select>
                   </div>
                   <div class="col-sm-2 m-t-25 ">
                      <input name="title[]" id="title" type="text" value="{{ $question['title'] }}"   placeholder="Enter Title" class="form-control" >
                   </div> 
                   <div class="col-sm-6 m-t-25 ">
                      <input name="question[]" id="question" type="text" value="{{ $question['question'] }}"  placeholder="Enter Question" class="form-control" >
                   </div>
                   <div class="col-sm-1 text-center m-t-25 del-question-blk">
                      <button type="button" class="btn btn-white delete-question" object-id="{{ $questionId }}"><i class="fa fa-trash"></i></button>
                   </div>
                   <!-- options -->
                   @if(isset($optionsList[$questionId]))
                   <div>
                    <div class="col-sm-8 question-options-block @if($question['type']=='weight') hidden @endif " >
                      @foreach($optionsList[$questionId] as $option)
                      <div class="row">
                        <input type="hidden" name="optionId[{{ $i }}][]" value="{{ $option['optionId'] }}">
                        <div class="col-sm-7 m-t-25 ">
                        <input name="option[{{ $i }}][]" id="question" type="text" placeholder="Enter option" value="{{ $option['label'] }}" class="form-control" >
                        </div>
                        <div class="col-sm-3 m-t-25 ">
                        <input name="score[{{ $i }}][]" id="question" type="text" placeholder="Enter score" value="{{ $option['score'] }}" class="form-control" >
                        </div> 
                        <div class="col-sm-2 text-center m-t-25">
                        <button type="button" class="btn btn-white delete-option"><i class="fa fa-trash"></i></button>
                        
                        </div>
                      </div>
                      @endforeach
                      <div class="row">
                        <input type="hidden" name="optionId[{{ $i }}][]" value="">
                        <div class="col-sm-8 m-t-25 ">
                        <input name="option[{{ $i }}][]" id="question" type="text" placeholder="Enter option"  class="form-control" >
                        </div>
                        <div class="col-sm-3 m-t-25 ">
                        <input name="score[{{ $i }}][]" id="question" type="text" placeholder="Enter score" class="form-control" >
                        </div> 
                        <div class="col-sm-1 text-right m-t-25">
                        <button type="button" class="btn btn-white add-option"><i class="fa fa-plus"></i></button>
                        </div>
                      </div>
                      </div> 
                      </div>
                      @endif
                    <!--  -->
              </div>
           <?php $i++; ?>
            @endforeach
            
            <div class="row question" row-count="{{ $i }}">
               <input type="hidden" name="questionId[]" value="">
               <div class="col-sm-3 m-t-25 ">
                  <select name="questionType[]" class="select2-container select2 form-control">
                      <option value="">Select Question Type</option>
                      <option value="single-choice"> Single-choice</option>
                      <option value="multi-choice">Multi-choice</option>
                      <option value="input"> Input</option>
                      <option value="descriptive"> Descriptive </option>
                      <option value="weight"> Weight </option>
                  </select>
               </div>
               <div class="col-sm-2 m-t-25 ">
                  <input name="title[]" id="title" type="text"   placeholder="Enter Title" class="form-control" >
               </div> 
               <div class="col-sm-6 m-t-25 ">
                  <input name="question[]" id="question" type="text"   placeholder="Enter Question" class="form-control" >
               </div>
               <div class="col-sm-1 text-right m-t-25 del-question-blk">
                  <button type="button" class="btn btn-white delete-question hidden" object-id=""><i class="fa fa-trash"></i></button>
               </div>

          </div>
        </div>
        <input type="hidden" name="counter" id="counter" value="{{ $i }}">
        <button type="button" class="btn btn-link text-success add-question"><i class="fa fa-plus"></i> Add Question</button>
        <div class="form-group">
          <div class="col-sm-10 text-center mri-submit">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/questionnaire-setting/' ) }}"><button type="button" class="btn btn-success">Previous</button></a>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/order-questions/'.$questionnaireId ) }}"><button type="button" class="btn btn-success">Next</button></a>
          </div>
        </div>

        </form>
                       
                  
                                   
                     </div>
                  </div>
 
<!-- END PLACE PAGE CONTENT HERE -->
<script type="text/javascript">

$(document).ready(function() {

 
     $('.input-days').change(function (event) {  
      if($(this).val() >= 1)
      { 
         $(this).closest(".form-group").find('.input-hours').removeAttr("min"); 
         $(this).closest(".form-group").find('.input-hours').removeAttr("data-parsley-validation-threshold");
      }
      else
      {
        $(this).closest(".form-group").find('.input-hours').attr("min","1"); 
        $(this).closest(".form-group").find('.input-hours').attr("data-parsley-validation-threshold","1");
      }
    });
});

</script>

@endsection