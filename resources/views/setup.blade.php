@extends('layouts.patient')

@section('content')
<div class="site-wrapper">

      <div class="site-wrapper-inner">
       <div class="cover-container">
          <br>
           <div class="text-right btn-contact" style="margin-top:-85px;"><button type="submit" class="btn btn-default">Contact Us</button></div>
           <br class="hidden-xs">
           <br>
        <div class="bg-white shadow-full">
          <div class="row" style="position:relative;">
            <div class="img-bg hidden-xs">
            </div>
<div class="col-sm-14 col-xs-24 col-sm-push-10  text-center login-info">
              <br>
              <br>
              <h3><span>Welcome to <br>your Healthcare Portal</span></h3>
                <br>
                <br>
                @if (count($errors) > 0)
                <div class="row">
                  <div class="col-sm-10">
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
          </div>
          </div>
					@endif
          <form id="login-form" class="login-form" role="form" method="POST" action="{{ url('/setup') }}"  data-parsley-validate>
                <div class="has-feedback b-b">
                  <input  name="reference_code"  type="text" class="form-control input-lg" id="inputSuccess2" aria-describedby="inputSuccess2Status" placeholder="Enter 8 Digit Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                  <span class="fa fa-question form-control-feedback text-info" aria-hidden="true"></span>
                </div>
                <br class="hidden-xs">
                <div class="checkbox text-left hidden-xs" >
                  
                </div>
                
                <br>
                <div class="row">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="col-sm-12"><button type="submit" class="btn btn-info">Verify Reference Code Now</button></div>
                  <div class="col-sm-12"><a type="submit" class="btn btn-link" data-toggle="modal" data-target=".bs-example-modal-sm">Forgot Reference Code ?</a></div>
                </div>
                </form>
                <br>

                <h5><span>Set up Already Done ?</span></h5>
                
                <a href="{{ url('/login') }}" class="btn btn-link"  ><p>Proceed To Login</p></a>
                <br>


                
       
                
                
            </div>
          </div>
          </div>
      </div>
    </div>

    </div>
  <!-- Modal -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
     <div class="modal-body">
        <div class="text-center">
         <br>
          <h3>Forgot your reference code or password ?</h3>
          <br>
          <p>Kindly Contact your Hospital Administrator or Physician to get your Password Reset</p>
          <br>
        
        <h3 class="f-w-400 line-title"><span>OR</span></h3>
          <br>
          <p>Call our Helpline : <span class="text-info">080 - 234 - 6534</span></p>
        </div>
    </div>
    <div class="modal-footer">
        <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-18"><button class="btn btn-primary btn-block" data-dismiss="modal">Close</button>
        <div class="col-sm-3"></div></div>
      </div>
    </div>
    <br>
  </div>
  </div>
</div>
@endsection
