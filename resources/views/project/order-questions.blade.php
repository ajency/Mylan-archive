@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Question Sorting</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div>                   
   <div class="page-title m-b-0">
      <h3 class="m-b-0"><span class="semi-bold">Question Sorting</span></h3>
   </div>
</div>

<p>Order the questions in the sequence in which you want the patients to answer the questionnaire.</p>
                                   
                   
                          
    <div class="grid simple">
      <div class="grid-body no-border table-data">
             <br>
          <h3 class="">{{ $questionnaireName }}</h3>
        
        <hr>
          @include('admin.flashmessage')
        <form class="form-horizontal col-sm-12" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/order-questions/'.$questionnaireId ) }}" data-parsley-validate>
          <div class="dd draggableList dark">
            
            <?php 
            $i=0;
            ?>
            @if(!empty($questionsList))
            <ol class="dd-list">
            @foreach($questionsList as $questionId => $question)
                <li class="dd-item" data-id="{{ $i }}">
                    <input type="hidden" name="questionId[]" value="{{ $questionId }}">
                    <div class="dd-handle"><span class="semi-bold ttuc p-r-15">{{ $question['title'] }}  </span> {{ $question['question'] }}</div>
                </li>
           <?php $i++; ?>
            @endforeach
            </ol>
            @else
              <div>No Questions Created</div>
            @endif
            
         </div>

 
      
        <div class="form-group">
          <div class="m-t-30 mri-submit questionSubmitBtn text-center clearfix col-md-12">
          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
          <input type="hidden" value="order" name="submitType"/>
          <input type="hidden" value="" name="redirect_url"/>
            <a href="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/configure-questions/'.$questionnaireId ) }}" class="pull-left">
            <button type="button" class="btn btn-link cust-link" ><i class="fa fa-angle-left" aria-hidden="true"></i> Previous</button>
            </a> 
            @if(!empty($questionsList))
            <button type="submit" class="btn btn-default"> SAVE</button>
            <button type="button" class="btn btn-primary publish-questionnaire pull-right">PUBLISH</button> 
            @endif
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