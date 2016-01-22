@extends('layouts.single-project')
@section('breadcrumb')
<!-- BEGIN BREADCRUMBS -->
<p>
      <ul class="breadcrumb">
         <li>
            <a href="#" class="active" > HOME</a>
         </li>
         <li>
            <a href="#"> Patients</a>
         </li>
          
      </ul>
      </p>
       
<!-- END BREADCRUMBS -->
@endsection
@section('content')
<div>
           <div class="pull-right m-t-25">
              <a href="#" class="btn btn-danger hidden"><i class="fa fa-download"></i> Download CSV</a>
              <a href="{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/create' ) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Patient</a>
           </div>
           <div class="page-title">
              <h3><span class="semi-bold">Patients</span></h3>
              <p>(Click on any Patient ID to see Profile Details)</p>
           </div>
        </div>
                         
         
                 
                 <div class="grid simple">
              <div class="grid-body no-border table-data">
                 <br>
           
            <div class="row">
                 <!-- <div class="col-sm-6"> <h3 class="bold margin-none">Response Rate</h3></div> -->
                 <div class="col-sm-2">
                 </div>
                 <div class="col-sm-4 pull-right">
                    <form name="searchData" method="GET"> 
                   <input type="hidden" class="form-control" name="startDate"  >
                   <input type="hidden" class="form-control" name="endDate"  >
                      <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; height:34px;border-radius:6px;">
                         <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                         <span></span> <b class="caret"></b>
                      </div>

                   </form>
                   <input type="hidden" name="flag" value="0">
                 </div>
              </div><hr>
              <div class="row column-seperation">
                 <div class="col-md-6 ">
                  <div class="tiles white added-margin">
                        <div class="tiles-body">
                        <div class="row">
                            <div class="col-sm-8">
                               <div id="piechart"></div>   
                            </div>
                            <div class="col-sm-4">
                              <h4 class="bold margin-none">{{ $completedResponses }}</h4>
                              <p> # Completed</p>
                              <h4 class="bold margin-none">{{ $missedResponses }}</h4>
                              <p> # Missed</p>
                               
                            </div>
                        </div>
                         
                          </div>
                    </div>
                 </div>
                 <div class="col-md-6 ">
                       <div class="tiles white added-margin light-gray" style="zoom: 1;">
                              <div class="tiles-body">
                                  <div class="tiles-title"> Total Patients </div>
                                  <div class="heading"> <span class="animate-number" data-value="{{ count($patients) }}" data-animation-duration="1200">{{ count($patients) }}</div>
                    <div class="progress transparent progress-small no-radius">
                    <div class="progress-bar progress-bar-black animate-progress-bar" data-percentage="{{ count($patients) }}%" style="width: {{ count($patients) }}%;"></div>
                    </div>
                    <h5 class="text-black"><b><i class="fa fa-group"></i> &nbsp;{{ $newPatients }}</b> New Patients Added </h5>
                  
                    </div>
                    </div>
                 </div>
               
              </div>
              <br>
              <!-- Chart - Added -->

              <br>
                <div class="row">
              <div class="col-sm-offset-9 col-sm-3">
              <div class="dataTables_filter" id="example_filter"><label>Search <input type="text" aria-controls="example" class="input-medium"></label></div>
              </div>
              </div>
                <div class="alert alert-info alert-black">
                    Patients Summary
                </div>
  <table class="table table-flip-scroll table-hover">
     <thead class="cf">
        <tr>
           <th width="12%">Patient ID</th>
           <th width="35%">Total Submissions</th>
           <th class="sorting">
              Compared To Previous
              <br> 
              <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
              <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
              <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
           </th>
           <th class="sorting">
              Compared To Baseline
              <br> 
              <sm><i class="fa fa-flag text-error"></i>  <i class="iconset top-down-arrow"></i></sm>
              <sm><i class="fa fa-flag text-warning"></i>  <i class="iconset top-down-arrow"></i></sm>
              <sm><i class="fa fa-flag text-success"></i>  <i class="iconset top-down-arrow"></i></sm>
           </th>
           <th class="sorting">Graph <br> <br></th>
        </tr>
     </thead>
     <tbody>
      @foreach($patients as $patient)
      <?php
        $patientId = $patient['id'];
        $patientStatus = $patient['account_status'];
        $referenceCode = $patient['reference_code'];
        

        if(!isset($patientsSummary[$referenceCode])) //inactive patient data
        {
            $patientsSummary[$referenceCode]['lastSubmission'] = '-';
            $patientsSummary[$referenceCode]['nextSubmission'] = '-';
            $patientsSummary[$referenceCode]['completed'] = [];
            $patientsSummary[$referenceCode]['missed'] = [];
            $patientsSummary[$referenceCode]['count'] = [];
            $patientsSummary[$referenceCode]['totalFlags'] = [];
        }
        
        $patientSummary = $patientsSummary[$referenceCode];
      ?>
        <tr onclick="window.document.location='{{ url($hospital['url_slug'].'/'.$project['project_slug'].'/patients/'.$patientId) }}'">
           <td>{{ $referenceCode }}</td>
           <td>
              <div class="lst-sub">
                 <h2 class="bold pull-left">
                    {{ count($patientSummary['completed']) }}<br>
                    <sm class="text-success">Completed</sm>
                 </h2>
                 <h2 class="bold pull-left">
                    {{ count($patientSummary['missed']) }}<br>
                    <sm class="text-danger">Missed</sm>
                 </h2>
                 <div class="pull-left p-t-20">
                    <span class="sm-font">Last Submission  <b>{{ $patientSummary['lastSubmission'] }}</b></span><br>
                    <span class="sm-font">Next Submission  <b>{{ $patientSummary['nextSubmission'] }}</b></span>
                 </div>
              </div>
           </td>
           <td class="text-center sorting">
              <span class=" text-error">
              @if(isset($patientSummary['previousFlag']['red']))
              {{ count($patientSummary['previousFlag']['red']) }}
              @else
              0
             @endif
                              </span>
              <span class="text-warning">
                @if(isset($patientSummary['previousFlag']['amber']))
                  {{ count($patientSummary['previousFlag']['amber']) }}
                  @else
                  0
                  @endif
              </span>
              <span class="text-success">
                @if(isset($patientSummary['previousFlag']['green']))
                  {{ count($patientSummary['previousFlag']['green']) }}
                  @else
                  0
                 @endif
              </span>
           </td>
           <td class="text-center sorting">
              <span class=" text-error">
                @if(isset($patientSummary['baseLineFlag']['red']))
                  {{ count($patientSummary['baseLineFlag']['red']) }}
                  @else
                  0
                 @endif
              </span>
              <span class="text-warning">
                @if(isset($patientSummary['baseLineFlag']['amber']))
                {{ count($patientSummary['baseLineFlag']['amber']) }}
                @else
                0
                @endif
              </span>
              <span class="text-success">
                @if(isset($patientSummary['baseLineFlag']['green']))
                  {{ count($patientSummary['baseLineFlag']['green']) }}
                  @else
                  0
                 @endif
              </span>
           </td>
           <td>
              <div class="chart-block" style="padding:28px">
                 <div id="line1" style="vertical-align: middle; display: inline-block; width: 100px; height: 30px;"></div>
              </div>
           </td>
        </tr>
        @endforeach  
         
     </tbody>
  </table>
                         
           </div>
        </div>
                 
                  
      <script type="text/javascript">
      var STARTDATE = ' {{ date("D M d Y", strtotime($startDate)) }} '; 
      var ENDDATE = '{{ date("D M d Y", strtotime($endDate)) }} '; 

      $(document).ready(function() {
         $('.input-daterange input').datepicker({
             format: 'dd-mm-yyyy'
         }); 
      }); 

      var chart = AmCharts.makeChart( "piechart", {
        "type": "pie",
        "theme": "light",
        "dataProvider": [ {
          "title": "Completed",
          "value": {{ $completedResponses }}
        }, {
          "title": "Missed",
          "value": {{ $missedResponses }},
           "fillColor": "#CC0000",
          "fillAlphas": 0.5,
        } ],
        "titleField": "title",
        "valueField": "value",
        "labelRadius": 5,

        "radius": "42%",
        "innerRadius": "60%",
        "labelText": "[[title]]",
        "export": {
          "enabled": true
        }
      } );
      </script>      
 

@endsection
