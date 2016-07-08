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
            <a href="{{ url() }}<?php echo $currUrl; ?>"> Add Project</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')
 
<div class="page-title">
   <h3><span class="semi-bold">Add Project</span></h3>
</div>
@include('admin.flashmessage')
<form class="form-no-horizontal-spacing" id="form-condensed" method="POST" action="{{ url($hospital['url_slug'].'/projects') }}" data-parsley-validate>
<div class="grid simple">
                     <div class="grid-body">
                        <form class="form-no-horizontal-spacing" id="form-condensed" data-parsley-validate>
                           <div class="row column-seperation projectAdd">
                              <div class="col-md-12">
                                 <div class="form-row">
                                    <label>Project Name <span class="text-primary">*</span></label>
                                    <input name="name" id="name" type="text"  class="form-control" placeholder="Project Name" data-parsley-required>
                                 </div>
                                 <div class="form-row">
                                    <label>Description <span class="text-primary">*</span></label>
                                    <textarea name="description" id="description" rows="3" placeholder="Write a short summary to describe the projects." style="width:100%;" data-parsley-required></textarea>
                                 </div>
                              </div>

                           <div class="col-md-12 attributes_block">
                            <h4>Attributes</h4>
                            <div class="row form-group">
                                <div class="col-xs-3">
                                    <label class="form-label">Label</label>
                                </div>
                                <div class="col-xs-3">
                                    <label class="form-label">Control Type</label>
                                </div>
                                <div class="col-xs-4">
                                    <label class="form-label">Defaults</label>
                                </div>
                                <div class="col-xs-1 text-center">
                                    <label class="form-label">Validate</label>
                                </div>
                                <div class="col-xs-1">
                                    
                                </div>
                            </div>
                             
      

                            <div class="row addAttributeBlock attributeContainer addAttribute">
                                
                                <div class="add-unit">
                            <div class="">
                                <div class="col-xs-3">
                                    <input type="text" name="attribute_name[]" class="form-control" value="" placeholder="Enter Attribute Name"  >
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
                                <div class="col-xs-4">
                                    <input type="text" name="controltypevalues[]" data-role="tagsinput" class="tags">

                                </div>
                                <div class="col-xs-1">
                                <div class="validateCheck">
                                    <input type="checkbox" name="validate[1]"  >
                                </div>
                                </div>
                                <div class="col-md-1 text-center">
                                    <div class="deleteProject">
                                        <a class="text-primary hidden"><i class="fa fa-Trash"></i></a>
                                    </div>
                                </div>
                            </div>
                               
                            </div>
                            <input type="hidden" name="counter" value="1">
                             </div>



                                <div class="row">
                                <div class="col-md-12">
                                 <div class="text-right">
                                    <a tabindex="0" class="btn btn-link addAttributes"><i class="fa fa-plus"></i>Add Attribute</a>
                                </div>
                                </div>
                                </div>
                       
                        </div>
                              
                           </div>
                           <div class="form-actions">
                              <div class="text-right">
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

