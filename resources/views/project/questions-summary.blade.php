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
                <div class="row questionSummary accord-questionSummary">



                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                   <div class="col-md-12 questionSummary__head clearfix">
                     <div>
                     <span class="chev-icons"></span>
                        <span class="text-center semi-bold ttuc p-r-15">{{ $question['title'] }} </span>
                            {{ $question['question'] }}
                            <span class="label label-default pull-right m-t-5">4 OPTIONS</span>
                     </div>
                   </div>
                   </a>
                  
                  
                   <div class="col-md-11 col-md-offset-1 questionSummary__options panel-collapse collapse in m-b-15" id="collapseOne">
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
                     <div class="row gray-header">
                       <div class="col-sm-8">Options</div>
                       <div class="col-md-2 text-center">Score</div>
                       <div class="col-sm-2 text-center">Sub Question</div>
                     </div>
                    


                     <!-- test -->
                     <div class="question-options-container">

                     <div class="question-options">
                     <div class="row">
                       <div class="col-sm-8">
                       <span class="chev-icons"style="padding-right: 20px;">&nbsp;</span>
                         Pain still there, even after medication 
                       </div>
                       <div class="col-sm-2 text-center">
                         <span class="bold">4</span>
                       </div>
                       </div><!--/row-->
                     </div><!-- /question-options -->

                     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                       <div class="question-options">
                       <div class="row">
                         <div class="col-sm-8">
                         <span class="chev-icons"></span>
                           Pain still there, even after medication 
                         </div>
                         <div class="col-sm-2 text-center">
                           <span class="bold">4</span>
                         </div>
                         <div class="col-sm-2 text-center">
                           <div class="checkbox p-t-0 subquest-checkbox">
                              <label>
                                <input type="checkbox">
                              </label>
                            </div>
                         </div>
                         </div><!--/row-->
                       </div><!-- /question-options -->
                       </a>

                       <div class="row panel-collapse collapse question-options_subquestion__container" id="collapseTwo">

                      <div class="col-sm-11 col-md-offset-1 ">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                          <div class="question-options_subquestion">
                            <span class="chev-icons"></span>
                            <span class="ttuc p-r-15">PAIN</span>
                            which statement describe best of your pain in last month?
                            <span class="label label-default pull-right m-t-5">4 OPTIONS</span>
                         </div>
                         </a>

                         <div class="row panel-collapse collapse question-options_subquestion__options" id="collapseThree">
                         <div class="col-md-11 col-md-offset-1">
                           <div class="row">
                             <div class="col-md-12">
                               <div class="row gray-header">
                                 <div class="col-sm-8">Options</div>
                                 <div class="col-md-2 text-center">Score</div>
                                 
                               </div>
                             </div>
                           </div>

                           <div class="question-options">
                           <div class="row">
                             <div class="col-sm-8">
                             <span class="chev-icons"style="padding-right: 20px;">&nbsp;</span>
                               Pain still there, even after medication 
                             </div>
                             <div class="col-sm-2 text-center">
                               <span class="bold">4</span>
                             </div>
                             </div><!--/row-->
                           </div><!-- /question-options -->

                         </div>
                         </div><!--/question-options_subquestion__options-->
                        </div>


                       </div><!-- /question-options_subquestion -->
                         
                       
                     </div><!-- question-options-container -->
                     <!-- /test -->

                   </div> <!-- /questionSummary__options -->
                   
                   
                </div><!--/accord-questionSummary-->
                
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