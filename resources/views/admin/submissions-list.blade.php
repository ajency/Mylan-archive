@extends('layouts.single-mylan')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
      <p>
      <ul class="breadcrumb">
         <li>
            <a href="{{ url( 'admin/' ) }}"><span>HOME</span></a>
         </li>
         <li>
            <a href="#" class="active">Submissions</a>
         </li>
      </ul>
      </p>
<!-- END BREADCRUMBS -->
@endsection
@section('content')


    <div class="page-title">
                     <h3><span class="semi-bold">Submissions</span> </h3>
                    <p>(Showing Submissions for Cardiac Care)</p>
                  </div>
                  <div class="grid simple">
                     <div class="grid-body">

                  <table class="table table-hover table-flip-scroll cf">
                                    <thead class="cf">
                                       <tr>
                                          <th>Refernce Code</th>
                                          <th>Submission Date</th>
                                          <!-- <th>Status</th> -->
                                       </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($responseList as $response)
                                       <tr onclick="window.document.location='/admin/submissions/{{$response['id']}}';">
                                          <td>{{ $response['patient'] }}</td>
                                          <td>{{ $response['updatedAt'] }}</td>
                              
                                          <!-- <td> <span class="label label-warning">REVIEW PENDING</span></td> -->
                                       </tr>
                                     @endforeach   
                                    </tbody>
                                 </table>
                                  </div>
                  </div>
      
 

@endsection