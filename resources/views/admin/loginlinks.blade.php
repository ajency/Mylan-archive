@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active">Login Links</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


    <div class="pull-right m-t-10">
                     
                  </div>
                  <div class="page-title">
                     <h3 class="m-b-0"><span class="semi-bold">{{ $accessData['type'] }} Links</span></h3>
                      
                  </div>
                  <div class="grid simple">
                     <div class="grid-body no-border">
                        <br>
                        @if(isset($accessData['links']))
                        @foreach($accessData['links'] as $data)
                        <div>
                           <div class="pull-right">
                              <a target="_blank" href="{{ $data['URL'] }}" ><button class="btn btn-default btn-small m-r-15">Login as {{ $data['loginName']}}</button></a>
                     
                           </div>
                            
                              <h3><span class="semi-bold">{{ $data['NAME'] }}</span></h3>
                            
                        </div>
                        <br>
                  
                        <hr>
                        @endforeach
                        @else 
                         <div>
                   
                            
                              <h3><span class="semi-bold">No data found</span></h3>
                            
                        </div>
                        @endif
                        
                     </div>
                  </div>

@endsection