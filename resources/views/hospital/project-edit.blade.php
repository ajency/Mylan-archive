@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php
	$previousProject = explode("/",$_SERVER['REQUEST_URI']);
	$projUrl = $previousProject[1]."/".$previousProject[2];
	$currUrl = $_SERVER['REQUEST_URI'];
?>
   <p>
      <ul class="breadcrumb">
         <!--li>
            <a href="#" class="active" > Home</a>
         </li-->
         <li>
            <a href="{{ url() }}/<?php echo $projUrl; ?>" class="active"> Projects</a>
         </li>
         <li>
            <a href="{{ url() }}<?php echo $currUrl; ?>"> Edit Project</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 @if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
	<?php $thrashHide = '' ?>
 @else
	<?php $thrashHide = 'style="display:none;"' ?>
 @endif
<div class="page-title">
   <h3 class="m-b-0"><span class="semi-bold">Edit Project</span></h3>
   <small class="db">Edit the project details</small>
</div>
@include('admin.flashmessage')
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url($hospital['url_slug'].'/projects/'.$project['id']) }}" data-parsley-validate>
<div class="grid simple">
                     <div class="grid-body">
                        <form class="form-no-horizontal-spacing" id="form-condensed" data-parsley-validate>
                           <div class="row column-seperation projectAdd">
                              <div class="col-md-12">
                                 <div class="form-row">
                                    <label>Project Name <span class="text-primary">*</span></label>
                                    <input name="name" id="name" type="text"  class="form-control" data-parsley-required placeholder="Project Name" value="{{ $project['name'] }}">
                                 </div>
                                 <div class="form-row">
                                    <label>Description <span class="text-primary">*</span></label>
                                    <textarea style="width: 100%;" name="description" id="description" rows="3" data-parsley-required placeholder="Write a short summary to describe the projects.">{{ $project['description'] }}</textarea>
                                 </div>
                                 <hr>
                              </div>

                              <div class="col-md-12 attributes_block">
                            <h4>Patient Profile Questions</h4>
                            <div class="row form-group">
                                <div class="col-xs-3">
                                    <label class="form-label">Question</label>
                                </div>
                                <div class="col-xs-3">
                                    <label class="form-label">Control Type</label>
                                </div>
                                <div class="col-xs-3">
                                    <label class="form-label">Defaults</label>
                                </div>
                                <div class="col-xs-2 text-center">
                                    <label class="form-label">Mark as mandatory</label>
                                </div>
                                <div class="col-xs-1 text-center">
                                   
                                </div>
                            </div>
                           <?php $key = 0;?>
                           @if(!empty($projectAttributes))
                           @foreach($projectAttributes as $key =>$attibute)
                            <div class="row allattributes attributeContainer">
                                <div class="col-xs-3">
                                    <input type="text" name="attribute_name[]" class="form-control" value="{{ $attibute['label'] }}" placeholder="Enter patient question"  >
                                    <input type="hidden" name="attribute_id[]" class="form-control" value="{{ $attibute['id'] }}">
                                </div>
                                <div class="col-xs-3">
                                    <select name="controltype[]" class="select2-container select2 form-control">
                                        <option value="">Select Control Type</option>
                                        <option value="textbox" {{ ($attibute['control_type']=='textbox')?'selected':''}} > Text Box</option> 
                                        <option value="select" {{ ($attibute['control_type']=='select')?'selected':''}} >Select Box</option>
                                        <option value="multiple" {{ ($attibute['control_type']=='multiple')?'selected':''}} > Multiple Select Box</option>
                                        <option value="number" {{ ($attibute['control_type']=='number')?'selected':''}} > Number </option>
                                        <option value="weight" {{ ($attibute['control_type']=='weight')?'selected':''}} > Weight </option>
                                    </select>
                                </div>
                                <div class="col-xs-3"> <!-- {{ ($attibute['values']=='')?'readonly':'' }}  -->
                                @if($attibute['control_type']=='weight')
                                    <input type="text"   name="controltypevalues[]" value="{{ $attibute['values'] }}" readonly class="tags text-100">
                                @else 
                                    <input type="text" name="controltypevalues[]" value="{{ $attibute['values'] }}" data-role="tagsinput" class="tags text-100">
                                @endif
                                </div>
                                <div class="col-xs-2">
                                    <div class="validateCheck">
                                    <input type="checkbox" name="validate[{{ $key }}]" {{ ($attibute['validate']=='on')?'checked':''}}>
                                    </div>
                                </div>
                                <div class="col-md-1 text-center">
                                    <div class="deleteProject" <?php echo $thrashHide;?> >
                                        <a class="text-primary deleteProjectAttributes"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                           @endforeach
                           <?php $key = $key+1;?>
                           @endif

                            <div class="row addAttributeBlock attributeContainer addAttribute">

                                <div class="add-unit">
                            <div class="">
                                <div class="col-xs-3">
                                    <input type="text" name="attribute_name[]" class="form-control" value="" placeholder="Enter patient question"  >
                                    <input type="hidden" name="attribute_id[]" class="form-control" value="">
                                </div>
                                <div class="col-xs-3">
                                    <select name="controltype[]" class="select2-container select2 form-control">
                                        <option value="">Select Control Type</option>
                                        <option value="textbox"> Text Box</option>
                                        <option value="select">Select Box</option>
                                        <option value="multiple"> Multiple Select Box</option>
                                        <option value="number"> Number </option>
                                        <option value="weight"> Weight </option>
                                    </select>
                                </div>
                                <div class="col-xs-3">
                                    <input type="text" name="controltypevalues[]" data-role="tagsinput" class="tags text-100">

                                </div>
                                <div class="col-xs-2">
                                <div class="validateCheck">
                                    <input type="checkbox" name="validate[{{ $key }}]"  >
                                    </div>
                                </div>
                                <!-- <div class="deleteProject">
                                    <a class="text-primary hidden"><i class="fa fa-trash"></i></a>
                                <div class="col-xs-1 text-right">
                                    <a class="text-primary deleteProjectAttributes hidden"><i class="fa fa-close"></i></a>
                                </div>
                                </div> -->
                                <div class="col-md-1 text-center">
                                     <div class="deleteProject" <?php echo $thrashHide;?>>
                                        <a class="text-primary deleteProjectAttributes hidden"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                                
                            </div>
                             </div>

                        <input type="hidden" name="counter" value="{{ $key }}">
                        </div>

                        <div class="row m-t-15 m-b-15">
                            <div class="col-md-12 p-r-0">
                             <div class="text-right" <?php echo $thrashHide;?>>
                                <a tabindex="0" class="btn btn-link addAttributes outline-btn">Add question <i class="fa fa-plus"></i></a>
                            </div>
                            </div>
                            </div>
                              
                           </div>
                            
                           <div class="form-actions">
                              <div class="text-right">
                                <input type="hidden" name="_method" value="PUT">
                                 <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
                                 @if(hasHospitalPermission($hospital['url_slug'],['edit']))
                                 <button class="btn btn-primary btn-cons-md" type="submit"><i class="fa fa-check"></i> Save</button>
                                 @endif
                                 <a href="{{ url($hospital['url_slug'].'/projects') }}"><button class="btn btn-default btn-cons-md" type="button"><i class="fa fa-ban"></i> Cancel</button></a>
                              </div>
                           </div>
                           
                        </form>
                     </div>
                  </div>
</form>

@endsection
