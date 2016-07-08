@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>" class="active" > HOME</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"> Projects</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"> Edit Project</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Edit Project</span></h3>
</div>
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url($hospital['url_slug'].'/projects/'.$project['id']) }}" data-parsley-validate>
<div class="grid simple">
                     <div class="grid-body">
                        <form class="form-no-horizontal-spacing" id="form-condensed">
                           <div class="row column-seperation">
                              <div class="col-md-6">
                                 <div class="form-row">
                                    <label>Project Name</label>
                                    <input name="name" id="name" type="text"  class="form-control" placeholder="Project Name" value="{{ $project['name'] }}">
                                 </div>
                                 <div class="form-row">
                                    <label>Description</label>
                                    <textarea name="description" id="description" rows="3" placeholder="Write a short summary to describe the projects." style="width:100%;">{{ $project['description'] }}</textarea>
                                 </div>
                              </div>
                              
                           </div>
                           <div class="form-actions">
                              <div class="text-right">
                                <input type="hidden" name="_method" value="PUT">
                                 <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
                                 <button class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
                                 <a href="{{ url($hospital['url_slug'].'/projects') }}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
</form>

@endsection