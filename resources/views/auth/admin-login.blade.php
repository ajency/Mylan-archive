@extends('layouts.hospital')

@section('content')
<div class="container">
  <div class="row login-container column-seperation">  
        <div class="col-md-5 col-md-offset-1 text-center m-t-20">
          <img src="{{ asset('img/mylan-logo.png') }}" class="logo" alt=""  data-src="{{ asset('img/mylan-logo.png') }}" width="200px" height="auto"/>
          <!-- <h2>Slutter Davis Hospital</h2> -->
          
        </div>
        <div class="col-md-5 "> <br>
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
     <form id="login-form" class="login-form" role="form" method="POST" action="{{ url('/admin/login') }}">
     <div class="row">
     <div class="form-group col-md-10">
            <label class="form-label">Email</label>
            <div class="controls">
        <div class="input-with-icon  right">                                       
          <i class=""></i>
          <input type="text" name="email" id="email" class="form-control">                                 
        </div>
            </div>
          </div>
          </div>
      <div class="row">
          <div class="form-group col-md-10">
            <label class="form-label">Password</label>
            <span class="help"></span>
            <div class="controls">
        <div class="input-with-icon  right">                                       
          <i class=""></i>
          <input type="password" name="password" id="password" class="form-control">                                 
        </div>
            </div>
          </div>
          </div>
      <div class="row">
          <div class="control-group  col-md-10">
            <div class="checkbox checkbox check-success"> <a href="#" data-toggle="modal" data-target=".bs-example-modal-sm">Trouble logging in?</a>&nbsp;&nbsp;
              <input type="checkbox" id="checkbox1" name="remember" value="1">
              <label for="checkbox1">Keep me remembered </label>
            </div>
          </div>
          </div>
          <div class="row">
            <div class="col-md-10">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="type" value="patient">
              <button class="btn btn-primary btn-cons pull-right" type="submit">Login</button>
            </div>
          </div>
      </form>
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
