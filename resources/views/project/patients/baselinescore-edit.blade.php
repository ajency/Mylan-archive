@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
 
      <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients' ) }}">Patients</a></li>
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'] ) }}">{{ $patient['reference_code']}}</a> </li>
        <li><a href="#" class="active">Baseline Score</a> </li>
 
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')

<div class="page-title">
     <h3>Patient Id<span class="semi-bold"> #{{ $patient['reference_code']}}</span></h3>
  </div>
 <div class="tabbable tabs-left">
    @include('project.patients.side-menu')
   <div class="tab-content">
      <div class="tab-pane table-data" id="Patients">
      </div>
      <div class="tab-pane table-data" id="Submissions">
           
      </div>
      <div class="tab-pane active" id="baseline">

         <h4><span class="semi-bold">{{ $questionnaire }}</span></h4>
         <p>(Baseline score for Patient Id {{ $patient['reference_code']}})</p>
         <br>
         <form method="post" action="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/base-line-score-edit') }}" data-parsley-validate>
 
         <input type="hidden" name="patientId" value="{{ $patient['id']}}">
         <input type="hidden" name="questionnaireId" value="{{ $questionnaireId }}">
        <div class="user-description-box">
        <?php
        $x = 1;
        ?>
        @foreach($questionsList as $questionId => $questions)
        <?php 
          $questionOptions=[];
          if($questions['type']!='descriptive')
          {
            $questionOptions = $optionScore[$questionId];
            asort($questionOptions);
          }
          
  
         ?>
            <input type="hidden" name="questionType[{{ $questionId }}]" value="{{ $questions['type'] }}">
            <div class="grid simple">
               <div class="grid-body">
                  <label class="semi-bold">Q{{ $x }}) {{ $questions['question'] }}?</label>
                   
                    @if($questions['type']=='input')
                    <div class="row">
                    <?php $i=1;?>
                      @foreach($questionOptions as $optionId=>$score)
                        <?php 
                          $value = '';
                          $option = $optionsList[$questionId][$optionId];

                          if((isset($answersList[$questionId]['optionId'])) && ($answersList[$questionId]['optionId']==$option['id']))
                              $value = $answersList[$questionId]['value'];
                        ?>
                        <div class="col-md-4">
                           <label>{{ $option['label'] }}</label>
                           <input name="question[{{ $questionId }}][{{ $option['id'] }}]" value="{{ $value }}" class="form-control inputBox" type="text" pla {{ ($i==1)?'data-parsley-required':''}} data-parsley-type="number" data-parsley-trigger="change"/>

                        </div>
                         <?php $i++;?>
                      @endforeach
                      
                    </div>
                     
                      
                    @elseif($questions['type']=='single-choice')

                      <select name="question[{{ $questionId }}]" id="question_{{ $questionId }}" class="select2 form-control" data-parsley-required>
                       <option value="">Select option for Baseline</option>

                       @foreach($questionOptions as $optionId=>$score)
                          <?php 
                          $selected = '';
                          $option = $optionsList[$questionId][$optionId];

                          if((isset($answersList[$questionId]['optionId'])) && ($answersList[$questionId]['optionId']==$option['id']))
                              $selected = 'selected';
                          ?>
                          <option {{ $selected }} value="{{ $option['id'] }}-{{ $option['score'] }}">{{ $option['label'] }}</option>
                      @endforeach
                      </select>
                       
                    @elseif($questions['type']=='multi-choice')
                      <select name="question[{{ $questionId }}][]" id="question_{{ $questionId }}" class="multiselect select2 form-control" multiple="multiple"  data-parsley-mincheck="1" data-parsley-required>
                     
                      @foreach($questionOptions as $optionId=>$score)
                       <?php 
                          $selected = '';
                          $option = $optionsList[$questionId][$optionId];

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
           <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patient['id'].'/base-line-score') }}" class="btn btn-default"><i class="fa fa-times"></i> Cancel</a>
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

  $('.inputBox').change(function (event) { 
      if($(this).val()=='')
      { 
        $(this).closest('.row').find('input:first').attr('data-parsley-required','');
        $(this).closest('.row').find('.parsley-required').show();
      }
      else
      {  
        $(this).closest('.row').find('.parsley-required').hide();
        $(this).closest('.row').find('input').removeAttr('data-parsley-required');
      }
    });
});
</script>
 
@endsection
