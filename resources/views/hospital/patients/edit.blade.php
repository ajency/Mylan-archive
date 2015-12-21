@extends('layouts.single-hospital')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
        <li>
          <a href="projects.html">Patients</a>
        </li>
        <li><a href="#" class="active">Edit</a> </li>
      </ul>
    </p>
<!-- END BREADCRUMBS -->
@endsection

@section('content')
<!-- BEGIN PAGE TITLE -->
<div class="page-title"> <i class="icon-custom-left"></i>
        <h3><span class="semi-bold">Patients</span></h3>
      </div> 
     <div class="grid simple">
           <div class="grid-body">
      <form class="form-no-horizontal-spacing" id="form-condensed"  method="POST" action="{{ url($hospital['url_slug'].'/patients/'.$patient['id'] ) }}" data-parsley-validate>
              <div class="row column-seperation">
                <div class="col-md-6">
                  <div class="form-row">
                     <label>Reference Code</label>
                        <input disabled name="reference_code" id="reference_code" type="text" value="{{ $patient['reference_code'] }}"  class="form-control" placeholder="Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                    </div>
                    <div class="row form-row">
   
                    <div class="col-sm-6">
                      <label>Project</label>
                      <select disabled name="project" id="project" class="select2 form-control"  data-parsley-required>
                          <option value="">Select</option>
                          @foreach($projects as $project)
                          <option {{ ($patient['project_id']==$project['id'])?'selected':'' }} value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                          @endforeach
                        </select>
                    </div>
                    </div>
                     
                </div>
                
              </div>
        <div class="form-actions">
          <div class="text-right">
          <input type="hidden" name="_method" value="PUT">
                   <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
            <button class="btn btn-primary btn-cons-md" type="submit"><i class="icon-ok"></i> Save</button>
            <a href="{{'/admin/patients'}}"><button class="btn btn-default btn-cons-md" type="button">Cancel</button></a>
          </div>
          </div>
      </form>
            </div>
          </div>

<!-- END PLACE PAGE CONTENT HERE -->
@endsection