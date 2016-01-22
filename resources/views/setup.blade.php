@extends('layouts.patient')

@section('content')
<div class="site-wrapper">

      <div class="site-wrapper-inner">
       <div class="cover-container">
         <!--  <br>
           <div class="text-right btn-contact" style="margin-top:-85px;"><button type="submit" class="btn btn-default">Contact Us</button></div>
           <br class="hidden-xs">
           <br> -->
        <div class="bg-white shadow-full" style="border: 1px solid #ddd;">
          <div class="row" style="position:relative;">
            <div class="img-bg hidden-xs col-sm-10">
              <img src="{{ url('Mylan-web/images/doctor.jpg') }}" class="img-responsive">
            </div>
<div class="col-sm-14 col-xs-24 text-center login-info">
              <br>
              <br>
              <h3><span>Welcome to <br>your Healthcare Portal</span></h3>
                <br>
                <br>
                @if (count($errors) > 0)
                
                  
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
          
         
					@endif
          <form id="login-form" class="login-form" role="form" method="POST" action="{{ url('/setup') }}"  data-parsley-validate>
                <div class="has-feedback ">
                  <input  name="reference_code"  type="text" class="form-control input-lg b-b"   aria-describedby="inputSuccess2Status" placeholder="Enter your Reference Code" data-parsley-required data-parsley-maxlength="8" data-parsley-minlength="8" data-parsley-maxlength-message="This value is too long. It should have 8 characters" data-parsley-minlength-message="This value is too short. It should have 8 characters" >
                   
                </div>
                <br class="hidden-xs">
 
                <div class="row">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="col-sm-12"><button type="submit" class="btn btn-info">Verify Reference Code Now</button></div>
                  <div class="col-sm-12"><a  href="#" class="btn btn-link" data-toggle="modal" data-target=".bs-example-modal-sm">Forgot Reference Code</a></div>
                </div>
                </form>
                <br><br>
                  <p stlye="margin-bottom:0;"><span>Set up Already Done?</span></p>
                 <a href="{{ url('/login') }}"><p>Proceed To Login</p></a>
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
          <h3>Forgot your Reference code?</h3>
          <br>
          <p>Kindly Contact your Hospital Administrator or Physician to get your Reference Code.</p>
          <br>
        
        <h3 class="f-w-400 line-title"><span>OR</span></h3>
          <br>
          <p>Call our Helpline : <span class="text-info">080 - 234 - 6534</span></p>
        </div>
    </div>
    <div class="modal-footer">
        <div class="row">
        <div class="col-sm-5"></div>
        <div class="col-sm-14"><button class="btn btn-primary btn-block" data-dismiss="modal">Close</button>
        <div class="col-sm-5"></div></div>
      </div>
    </div>
    <br>
  </div>
  </div>
</div>
@endsection
