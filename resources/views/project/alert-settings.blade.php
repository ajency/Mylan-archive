@extends('layouts.single-project')

@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<?php  
	$currUrl = $_SERVER['REQUEST_URI'];
?>
      <p>
      <ul class="breadcrumb">
        <li><a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/dashboard' ) }}"><span>Home</span></a></li>
        <li><a href="{{ url() }}<?php echo $currUrl; ?>" class="active">Alert Settings</a> </li>
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
                       <h3 class="">{{ ucfirst($project['name']) }}</h3>
                      
                      <hr>
        @include('admin.flashmessage')
		@if(hasProjectPermission($hospital['url_slug'],$project['project_slug'],['edit']))
			<?php
				$hideViewAccess = '';
			?>
			<form class="form-horizontal col-sm-12" method="post" action="{{ url( $hospital['url_slug'].'/'.$project['project_slug'].'/alert-setting/' ) }}" data-parsley-validate>
		@else	
			<form class="form-horizontal col-sm-12" method="post" onsubmit="return false;" data-parsley-validate>
			<?php
				$hideViewAccess = 'style="display:none";';
			?>
		@endif
          <div class="col-md-12 settings_block">
                             
            <div class="row form-group">
                <!--div class="col-xs-2">
                    <label class="form-label">Alert Type</label>
                </div-->
                <div class="col-xs-3">
                    <label class="form-label">Flag Count</label>
                </div>
                <div class="col-xs-3">
                    <label class="form-label">Operation</label>
                </div>
                <div class="col-xs-3">
                    <label class="form-label">Flag Colour</label>
                </div>
                <div class="col-xs-2 text-center">
                    <label class="form-label">Compared To</label>
                </div>
                <div class="col-xs-1 text-center">
                   
                </div>
            </div>
             <?php $key = 0;?>
             @foreach($settings as $key =>$setting)
              <div class="row allsettings settingsContainer">
              

                   <!--div class="col-xs-2">
                    <select name="alert_type[]" class="select2-container select2 form-control">
                      <option value="">Select alert type</option>
                      <option value="flag_count" {{ ($setting['alertType']=='flag_count')?'selected':''}}>Flag count based</option>
                      <option value="total_score" {{ ($setting['alertType']=='total_score')?'selected':''}}>Total score based</option>
                    </select>
                  </div-->

                  <div class="col-xs-3">
                      <input type="text" name="flag_count[]" class="form-control" value="{{ $setting['flagCount'] }}" placeholder="Enter Flag Count"  >
                      <input type="hidden" name="setting_id[]" class="form-control" value="{{ $setting['id'] }}">
                  </div>
                  <div class="col-xs-3">
                    <select name="operation[]" class="select2-container select2 form-control">
                      <option value="">Select Operation</option>
                      <option value="greater_than" {{ ($setting['operation']=='greater_than')?'selected':''}} >Greater Than</option>
                      <option value="greater_than_equal_to" {{ ($setting['operation']=='greater_than_equal_to')?'selected':''}} >Greater Than Equal To</option>
                      <option value="less_than" {{ ($setting['operation']=='less_than')?'selected':''}} >Less Than</option>
                      <option value="less_than_equal_to" {{ ($setting['operation']=='less_than_equal_to')?'selected':''}} > Less Than Equal To </option>
                    </select>
                     
                  </div>
                  <div class="col-xs-3">
                    <select name="flag_colour[]" class="select2-container select2 form-control">
                      <option value="">Select Flag Colour</option>
                      <option value="red" {{ ($setting['flagColour']=='red')?'selected':''}} >Red</option>
                      <option value="amber" {{ ($setting['flagColour']=='amber')?'selected':''}} >Amber</option>
                      <option value="green" {{ ($setting['flagColour']=='green')?'selected':''}} >Green</option>
                    </select>
                  </div>
                  <div class="col-xs-2">
                    <select name="compared_to[]" class="select2-container select2 form-control">
                      <option value="">Select Compared To</option>
                      <option value="previous" {{ ($setting['comparedTo']=='previous')?'selected':''}} >Previous</option>
                      <option value="baseline" {{ ($setting['comparedTo']=='baseline')?'selected':''}} >Baseline</option>
                    </select>
                     
                  </div>
                  <div class="col-md-1 text-center">
                      <div class="deleteSettings">
                          <a class="text-primary deleteAlertSettings"><i class="fa fa-trash"></i></a>
                      </div>
                  </div>
              </div>
             @endforeach

              <div class="row addSettingsBlock addSettingsContainer settingsContainer">

               <!--div class="col-xs-2">
                <select name="alert_type[]" class="select2-container select2 form-control">
                  <option value="">Select alert type</option>
                  <option value="flag_count">Flag count based</option>
                  <option value="total_score">Total score based</option>
                </select>
              </div-->

              <div class="col-xs-3">
                <input type="text" name="flag_count[]" class="form-control"  placeholder="Enter Flag Count"  >
                <input type="hidden" name="setting_id[]" class="form-control" >
              </div>
              <div class="col-xs-3">
                <select name="operation[]" class="select2-container select2 form-control">
                  <option value="">Select Operation</option>
                  <option value="greater_than" >Greater Than</option>
                  <option value="greater_than_equal_to">Greater Than Equal To</option>
                  <option value="less_than">Less Than</option>
                  <option value="less_than_equal_to" > Less Than Equal To </option>
                </select>
                     
              </div>
              <div class="col-xs-3">
                <select name="flag_colour[]" class="select2-container select2 form-control">
                  <option value="">Select Flag Colour</option>
                  <option value="red" >Red</option>
                  <option value="amber" >Amber</option>
                  <option value="green" >Green</option>
                </select>
              </div>
              <div class="col-xs-2">
                <select name="compared_to[]" class="select2-container select2 form-control">
                  <option value="">Select Compared To</option>
                  <option value="previous"  >Previous</option>
                  <option value="baseline">Baseline</option>
                </select>
                     
              </div>
              <div class="col-md-1 text-center">
               <div class="deleteSettings">
                  <a class="text-primary hidden deleteAlertSettings"><i class="fa fa-trash"></i></a>
                </div>
              </div>

          <input type="hidden" name="counter" value="{{ ($key+1) }}">
          </div>

          <div class="row">
              <div class="col-md-11">
               <div class="text-right">
                  <a tabindex="0" class="btn btn-link addSettings"><i class="fa fa-plus"></i>Add More</a>
              </div>
              </div>
              </div>
                              
        </div>


                        <div class="form-group">
                          <div class="col-sm-11 text-right">
                          <input type="hidden" value="{{ csrf_token()}}" name="_token"/>
                            <button type="submit" class="btn btn-success m-r-15 m-t-10" <?php echo $hideViewAccess?>>Save</button>
                          </div>
                        </div>
                      </form>
                       
                  
                                   
                     </div>
                  </div>
 
<!-- END PLACE PAGE CONTENT HERE -->
@endsection