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
                <div class="row questionSummary">
                   <div class="col-md-12 questionSummary__head">
                     <div>
                        <span class="text-center semi-bold">{{ $question['title'] }} :- </span>
                            {{ $question['question'] }}
                     </div>
                   </div>                                   
                   <div class="col-md-8 col-md-offset-2 questionSummary__options">
                   @if(isset($optionsList[$questionId]))
                   <span class="text-center semi-bold col-md-2">Options</span>
                     <div class="col-md-10">
                        <ul>
                            @foreach($optionsList[$questionId] as $option)
                             <li>{{ $option['label'] }}</li>
                            @endforeach
                        </ul>
                     </div>
                     @endif
                   </div> 
                </div>
                
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