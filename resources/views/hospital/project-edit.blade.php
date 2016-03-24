@extends('layouts.single-hospital')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
   <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > Home</a>
         </li>
         <li>
            <a href="#"> Projects</a>
         </li>
         <li>
            <a href="#"> Edit Project</a>
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
                        <form class="form-no-horizontal-spacing" id="form-condensed" data-parsley-validate>
                           <div class="row column-seperation projectAdd">
                              <div class="col-md-3 b-0">
                                 <div class="form-row">
                                    <label>Project Name</label>
                                    <input name="name" id="name" type="text"  class="form-control" data-parsley-required placeholder="Project Name" value="{{ $project['name'] }}">
                                 </div>
                                 <div class="form-row">
                                    <label>Description</label>
                                    <textarea name="description" id="description" rows="3" data-parsley-required placeholder="Write a short summary to describe the projects." style="width:100%;">{{ $project['description'] }}</textarea>
                                 </div>
                              </div>

                              <div class="col-md-9 attributes_block">
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
                                <div class="col-xs-2">
                                    <label class="form-label">Validate</label>
                                </div>
                            </div>
                           
                           @foreach($projectAttributes as $key=>$attibute)
                            <div class="row allattributes attributeContainer">
                                <div class="col-xs-3">
                                    <input type="text" name="attribute_name[]" class="form-control" value="{{ $attibute['label'] }}" placeholder="Enter Attribute Name"  >
                                    <input type="hidden" name="attribute_id[]" class="form-control" value="{{ $attibute['id'] }}">
                                </div>
                                <div class="col-xs-3">
                                    <select name="controltype[]" class="select2-container select2 form-control">
                                        <option value="">Select Control Type</option>
                                        <option value="textbox" {{ ($attibute['control_type']=='textbox')?'selected':''}} > Text Box</option>control_type
                                        <option value="select" {{ ($attibute['control_type']=='select')?'selected':''}} >Select Box</option>
                                        <option value="multiple" {{ ($attibute['control_type']=='multiple')?'selected':''}} > Multiple Select Box</option>
                                        <option value="number" {{ ($attibute['control_type']=='number')?'selected':''}} > Number </option>
                                        <option value="weight" {{ ($attibute['control_type']=='weight')?'selected':''}} > Weight </option>
                                    </select>
                                </div>
                                <div class="col-xs-4"> <!-- {{ ($attibute['values']=='')?'readonly':'' }}  -->
                                @if($attibute['control_type']=='weight')
                                    <input type="text"   name="controltypevalues[]" value="{{ $attibute['values'] }}" readonly class="tags text-100">
                                @else 
                                    <input type="text" name="controltypevalues[]" value="{{ $attibute['values'] }}" data-role="tagsinput" class="tags text-100">
                                @endif
                                </div>
                                <div class="col-xs-2">
                                    <input type="checkbox" name="validate[{{ $key }}]" {{ ($attibute['validate']=='on')?'checked':''}}>
                                </div>
                                <div class="deleteProject">
                                    <a class="text-primary deleteProjectAttributes"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                           @endforeach

                            <div class="row addAttributeBlock attributeContainer">

                                <div class="add-unit">
                            <div class="">
                                <div class="col-xs-3">
                                    <input type="text" name="attribute_name[]" class="form-control" value="" placeholder="Enter Attribute Name"  >
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
                                <div class="col-xs-4">
                                    <input type="text" name="controltypevalues[]" data-role="tagsinput" class="tags text-100">

                                </div>
                                <div class="col-xs-2">
                                    <input type="checkbox" name="validate[{{ ($key+1) }}]"  >
                                </div>
                                <div class="deleteProject">
                                    <a class="text-primary hidden"><i class="fa fa-trash"></i></a>
                                <div class="col-xs-1 text-right">
                                    <a class="text-primary deleteProjectAttributes hidden"><i class="fa fa-close"></i></a>
                                </div>
                            </div>
                                
                            </div>
                             </div>

                        <input type="hidden" name="counter" value="{{ ($key+1) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                             <div class="text-right">
                                <a tabindex="0" class="btn btn-link addAttributes"><i class="fa fa-plus"></i>Add Attribute</a>
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
