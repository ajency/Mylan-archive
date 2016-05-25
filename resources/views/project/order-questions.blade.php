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
        <form class="form-horizontal col-sm-12" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/order-questions/'.$questionnaireId ) }}" data-parsley-validate>

        <div class="form-row question-list">
            <?php 
            $i=0;
            ?>
            @foreach($questionsList as $questionId => $question)
                <div class="row question" row-count="{{ $i }}">
                   <input type="hidden" name="questionId[]" value="{{ $questionId }}">
                   <div class="col-sm-8 m-t-25 ">
                      <span class="">{{ $question['title'] }}</span>
                      {{ $question['question'] }}
                   </div>

              </div>
           <?php $i++; ?>
            @endforeach
            
 
        </div>
      
        <div class="form-group">
          <div class="col-sm-10 text-center mri-submit">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
             
            <button type="submit" class="btn btn-success">Save</button>
             
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