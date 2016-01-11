@extends('layouts.patient')

@section('content')
<div class="site-wrapper">

      <div class="site-wrapper-inner">
       <div class="cover-container">
          <br>
           <div class="text-right btn-contact" style="margin-top:-85px;"><button type="submit" class="btn btn-default">Contact Us</button></div>
           <br class="hidden-xs">
           <br>
        <div class="bg-white shadow-full" style="border:1px solid #ddd;">
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
                  <div class="col-sm-24">
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
                <form id="login-form" class="login-form" role="form" method="POST" action="{{ url('/login') }}" data-parsley-validate>
                <div class="has-feedback b-b">  
                <?php
                $referenceCode = Session::get('referenceCode');
                ?>
                <input type="text" class="form-control input-lg" id="inputSuccess2" aria-describedby="inputSuccess2Status" name="reference_code" placeholder="Enter 8 Digit Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                <!-- @if($referenceCode=='')
                  <input type="text" class="form-control input-lg" id="inputSuccess2" aria-describedby="inputSuccess2Status" name="reference_code" placeholder="Enter 8 Digit Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                @else 
                  <h4 class="text-left"><span class="text-muted">Reference Code :</span> <span class="text-info">{{ $referenceCode }}</span></h4> 
                  <input type="hidden" value="{{ $referenceCode }}" name="reference_code" placeholder="Enter 8 Digit Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                @endif --> 
                 <span class="fa fa-question form-control-feedback text-info" aria-hidden="true"></span>
                </div>
                <input type="password" name="password" class="form-control input-lg" id="password" placeholder="Enter your Password" data-parsley-required data-parsley-maxlength="4" data-parsley-minlength="4" data-parsley-maxlength-message="This value is too long. It should have 4 characters" data-parsley-minlength-message="This value is too short. It should have 4 characters"  />
                <br class="hidden-xs">
                <div class="checkbox text-left hidden-xs" >
                  <p>
                  <label>
                    <input type="checkbox" id="checkbox1" name="remember" value="1"> Remember Me
                  </label></p>
                </div>
                
                <br>
                
                <button type="submit" class="btn btn-info btn-block">Login</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="type" value="patient">
                <br>
                <br>
                <a href="#" data-toggle="modal" data-target=".bs-example-modal-sm"><p>Forgot your reference code<br> or password ?</p></a>
                <br>
                </form>
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
