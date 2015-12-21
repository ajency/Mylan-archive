@extends('layouts.hospital')

@section('content')
<div class="container">
  <div class="row login-container column-seperation">  
        <div class="col-md-5 col-md-offset-1">
          <img src="{{ $logoUrl }}" class="logo" alt=""  data-src="{{ $logoUrl }}" width="auto" height="auto"/>
          <h2>{{ $hospital['name'] }}</h2>
          
        </div>
        <div class="col-md-5 "> <br>
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
     <form id="login-form" class="login-form" role="form" method="POST" action="/hospital/{{ $hospital['id']}}/login">
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
            <div class="checkbox checkbox check-success"> <a href="#">Trouble login in?</a>&nbsp;&nbsp;
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
@endsection
