@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
         <li>
            <a href="#"> Patients</a>
         </li>
         <li>
            <a href="#"> Submissions</a>
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
      <div class="tab-pane table-data" id="Submissions">
           
      </div>
      <div class="tab-pane active" id="baseline">

         <h4><span class="semi-bold">{{ $questionnaire }}</span></h4>
         <p>(Baseline score for Patient Id <span class="ttuc patient-refer{{ $patient['reference_code']}}">{{ $patient['reference_code']}}</span>)</p>
         <br>
         <form method="post" action="{{ url($hospital['url_slug'].'/patients/'.$patient['id'].'/base-line-score-edit') }}" data-parsley-validate>
         <input type="hidden" name="baseLineResponseId" value="{{ $baseLineResponseId }}">
         <input type="hidden" name="patientId" value="{{ $patient['id']}}">
         <input type="hidden" name="questionnaireId" value="{{ $questionnaireId }}">
        <div class="user-description-box">
        <?php
        $x = 1;
        ?>
        @foreach($questionsList as $questionId => $questions)
            <input type="hidden" name="questionType[{{ $questionId }}]" value="{{ $questions['type'] }}">
            <div class="grid simple">
               <div class="grid-body">
                  <label class="semi-bold">Q{{ $x }}) {{ $questions['question'] }}?</label>
                   
                    @if($questions['type']=='input')
                    <div class="row">
                      @foreach($optionsList[$questionId] as $option)
                        <?php 
                          $value = '';
                          if((isset($answersList[$questionId]['optionId'])) && ($answersList[$questionId]['optionId']==$option['id']))
                              $value = $answersList[$questionId]['value'];
                        ?>
                        <div class="col-md-4">
                           <label>{{ $option['label'] }}</label>
                           <input name="question[{{ $questionId }}][{{ $option['id'] }}]" value="{{ $value }}" class="form-control" type="text" pla/>
                        </div>
                      @endforeach
                      
                    </div>
                     
                      
                    @elseif($questions['type']=='single-choice')
                      <select name="question[{{ $questionId }}]" class="select2 form-control" data-parsley-required>
                       <option value="">Select option for Baseline</option>
                       @foreach($optionsList[$questionId] as $option)
                          <?php 
                          $selected = '';
                          if((isset($answersList[$questionId]['optionId'])) && ($answersList[$questionId]['optionId']==$option['id']))
                              $selected = 'selected';
                          ?>
                          <option {{ $selected }} value="{{ $option['id'] }}-{{ $option['score'] }}">{{ $option['label'] }}</option>
                      @endforeach
                      </select>
                       
                    @elseif($questions['type']=='multi-choice')
                      <select name="question[{{ $questionId }}][]" id="role" class="multiselect select2 form-control" multiple="multiple"   data-parsley-required>
                       <option value="">Select option for Baseline</option>
                       @foreach($optionsList[$questionId] as $option)
                       <?php 
                          $selected = '';
                          if(isset($answersList[$questionId][$option['id']]))
                              $selected = 'selected';
                          ?>
                          <option  {{ $selected }} value="{{ $option['id'] }}-{{ $option['score'] }}">{{ $option['label'] }}</option>
                      @endforeach
                      </select>
                    @elseif($questions['type']=='descriptive')
                        <?php 
                          $value = '';
                          if(isset($answersList[$questionId]['value']))
                              $value = $answersList[$questionId]['value'];
                        ?>
                        <textarea name="question[{{ $questionId }}]" class="form-control" data-parsley-required>{{ $value }}</textarea>               
                    @endif

               </div>
            </div>
        <?php
        $x ++;
        ?>    
        @endforeach
        <div class="text-right">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
           <a href="{{ url($hospital['url_slug'].'/patients/'.$patient['id'].'/base-line-score') }}" class="btn btn-default"><i class="fa fa-times"></i> Cancel</a>
           <button class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
        </div>
      </form>
      </div>
      </div>
      <div class="tab-pane " id="Reports">

      </div> 
   </div>
   </div>

<script type="text/javascript">
$(function(){
  $(".multiselect").multiselect();
});
</script>
 
@endsection
