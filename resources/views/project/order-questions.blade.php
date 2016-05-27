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
          <div class="dd draggableList dark">
            <ol class="dd-list">
            <?php 
            $i=0;
            ?>
            @foreach($questionsList as $questionId => $question)
                <li class="dd-item" data-id="{{ $i }}">
                    <input type="hidden" name="questionId[]" value="{{ $questionId }}">
                    <div class="dd-handle"><span class="">{{ $question['title'] }} : </span> {{ $question['question'] }}</div>
                </li>
           <?php $i++; ?>
            @endforeach

            </ol>
         </div>

 
      
        <div class="form-group">
          <div class="col-sm-10 m-t-10 mri-submit questionSubmitBtn">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
            
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/configure-questions/'.$questionnaireId ) }}"><button type="button" class="btn btn-default"><i class="fa fa-backward" aria-hidden="true"></i> Previous</button></a> 
            <button type="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Save</button>
            <a href="#"><button type="button" class="btn btn-default">  <i class="fa fa-check-square-o" aria-hidden="true"></i> Publish</button></a> 
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