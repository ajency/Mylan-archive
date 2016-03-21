$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//  $.notify.defaults({
//     globalPosition: 'bottom right'
//   });

// $(document).ajaxComplete(function() {
//     var args, ref, ref1, xhr;
//     var objects = arguments;
    
//     xhr = objects[1];
//     if ((ref = xhr.status) === 201 || ref === 202 || ref === 203) {
//       return $.notify(xhr.responseText.message, 'success');
//     } else if ((ref1 = xhr.status) === 200) {
//       return $.notify(xhr.responseText.message, 'error');
//     }
//   });

 
$('.validateRefernceCode').change(function (event) { 
    // $(".cf-loader").removeClass('hidden');
    var controlObj = $(".reference_code");
    controlObj.closest('.form-row').find('input').after('<span class="cf-loader"></span>');
    controlObj.closest('.form-row').find('.parsley-errors-list').find('.refCodeError').remove();
    controlObj.closest('form').find('button[type="submit"]').attr('disabled','disabled');

    $.ajax({
        url: BASEURL+"/patients/"+PATIENT_ID+"/validatereferncecode",
        type: "POST",
        data: {
            reference_code: $(this).val()
        },
        dataType: "JSON",
        success: function (response) {
            if (!response.data)
            {   
                controlObj.closest('.form-row').find('.parsley-errors-list').html('<li class="parsley-required refCodeError">Reference code already taken</li>')
                controlObj.val('');               
            }
            controlObj.closest('.form-row').find('.cf-loader').remove();
            controlObj.closest('form').find('button[type="submit"]').removeAttr('disabled');

        }
    });
    
});


$('.authUserEmail').change(function (event) { 
  var controlObj = $(".authUserEmail");
    controlObj.closest('.form-row').find('input').after('<span class="cf-loader"></span>');
    controlObj.closest('.form-row').find('.parsley-errors-list').find('.emailError').remove();
    controlObj.closest('form').find('button[type="submit"]').attr('disabled','disabled');
    var USER_ID = $(this).attr('objectId');
    if($(this).attr('objectType')=='hospital')
      var URL = BASEURL+"/admin/users/"+USER_ID+"/authuseremail";
    else
      var URL = BASEURL+"/users/"+USER_ID+"/authuseremail";

    $.ajax({
        url: URL ,
        type: "POST",
        data: {
            email: $(this).val()
        },
        dataType: "JSON",
        success: function (response) {
            if (!response.data)
            {   
                controlObj.closest('.form-row').find('.parsley-errors-list').html('<li class="parsley-required emailError">Email already taken</li>');
                controlObj.val('');  
            }
            controlObj.closest('.form-row').find('.cf-loader').remove();
            controlObj.closest('form').find('button[type="submit"]').removeAttr('disabled');
            // $(".cf-loader").addClass('hidden');
        }
    });
    
 
});

    $('select[name="updateSubmissionStatus"]').change(function (event) { 
       var status = $(this).val();
       var responseId = $(this).attr('object-id');
       $("#statusLoader").removeClass('hidden');
       $(this).closest('form').submit();
      //  $.ajax({
      //   url: BASEURL+"/submissions/"+responseId+"/updatesubmissionstatus",
      //   type: "POST",
      //   data: {
      //       status: status
      //   },
      //   dataType: "JSON",
      //   success: function (response) {
      //     $("#statusLoader").addClass('hidden');
      //       // if (!response.data)
      //       // {   
      //       //     alert('Reference Code Already Taken');
      //       //     $("#reference_code").val('');
      //       // }

      //       // $(".cf-loader").addClass('hidden');
      //   }
      // });

    });


var uploader = new plupload.Uploader({
    runtimes : 'html5,flash,silverlight,html4',
     
    browse_button : 'pickfiles', // you can pass in id...
    container: document.getElementById('hospital_logo'), // ... or DOM Element itself
     
    url: '/admin/hospital/'+HOSPITAL_ID+'/uploadlogo',
    
    headers: {
            "x-csrf-token": $("[name=_token]").val()
        },

    filters : {
        max_file_size : '10mb',
        mime_types: [
            {title : "Image files", extensions : "jpg,gif,png"},
            {title : "Zip files", extensions : "zip"}
        ]
    },
 
 
    init: {
        PostInit: function() {
             
                uploader.start();
                return false;
             
        },
 
        FilesAdded: function(up, files) {
            uploader.start();
            $('.loader').removeClass('hidden');
        },
 
        UploadProgress: function(up, file) {
    
            $('.loader').html('<div class="progress-bar progress-bar-black animate-progress-bar" data-percentage="' + file.percent +'%" style="width: ' + file.percent +'%;"></div>');
        },

        FileUploaded: function (up, file, xhr) {
            fileResponse = JSON.parse(xhr.response);
            $('#pickfiles').addClass('hidden');
            var imgStr = '<img src="'+ fileResponse.data.image_path +'" height="50px" class="imageUploaded">';
            // imgStr += '<a class="deleteHospitalLogo" data-type="hospital" data-value="'+HOSPITAL_ID+'" href="javascript:;">[delete]</a>';
            $('#hospital_logo').val(fileResponse.data.filename);
            $('#hospital_logo_block').append(imgStr);
            $('.loader').addClass('hidden');
            $('.deleteHospitalLogo').removeClass('hidden');

         },
 
        Error: function(up, err) {
            // document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
        }
    }
});
 
uploader.init();


$('.upload').on('click', '.deleteHospitalLogo', function(event) {

    if (confirm('Are you sure you want to delete this hospital logo?') === false) {
        return;
    }

    var hospitalId = $(this).attr("data-value");
    var imageName ='';
    if(!hospitalId)
    {
        imageName = $("#hospital_logo").val();
        $('.imageUploaded').remove();
        $('#pickfiles').removeClass('hidden');
        $('.deleteHospitalLogo').addClass('hidden');
    }
    else
    {
        $.ajax({
            url: BASEURL + "/admin/hospital/" + hospitalId + "/deletelogo",
            type: "POST",
            data: {
                imageName: imageName
            },
            success: function (response) {
               $('.imageUploaded').remove();
               $('#pickfiles').removeClass('hidden');
               $('.deleteHospitalLogo').addClass('hidden');
            }
        });
    }

    

});

function authHospitalList(Obj ,hospitalObj)
{
    var result = true;

    Obj.closest('.allHospitalsAccess').find('select[name="hospital[]"]').each(function () {

        if (hospitalObj.get(0) != $(this).get(0) && $(this).val() == hospitalObj.val()) {
            alert('Hospital Already Selected');
            hospitalObj.val('');
            result = false;

        }
    });
    return result;
} 

$('.allHospitalsAccess').on('change', 'select[name="hospital[]"]', function(event) {
    authHospitalList($(this) ,$(this));
});


$('.add-hospital-user').click(function (event) { 

    var objectType = $(this).attr('object-type');

    if($(".hospital_users:last").find('select').val()=='')
    {
        alert('Please Select '+ objectType);
        return;
    }

    if(!authHospitalList($(this) ,$(".hospital_users:last").find('select')))
    {

         return;
    }
    

    var addHospital = $(".hospital_users:last").find('select').html(); 
    var counter = $('input[name="counter"]').val();
    var i = parseInt(counter) + 1;

    html ='<hr><div class="row hospital_users">';
    html +='<div class="col-md-4">';
    html +='<input type="hidden" name="user_access[]" value="">';
    html +='<select name="hospital[]" id="hospital" class="select2 form-control"  >';
    html += addHospital
    html +='<select>';
    html +='</div>';
               
    html +='<div class="col-md-4">';
    html +='<div class="radio radio-primary text-center">';
    html +='<input id="access_view_'+i+'" type="radio" name="access_'+i+'" value="view" checked="checked">';
    html +='<label for="access_view_'+i+'">View</label>';
    html +='<input id="access_edit_'+i+'" type="radio" name="access_'+i+'" value="edit">';
    html +='<label for="access_edit_'+i+'">Edit</label>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-md-4 text-center">';
    html +='<a class="deleteUserHospitalAccess hidden"> Delete </a>';
    html +='</div>';
    html +='</div>';

    $('input[name="counter"]').val(i);
    $(".hospital_users:last").find('.deleteUserHospitalAccess').removeClass('hidden');
    $(".hospital_users:last").after(html);
    

});

$('.add-mediaction').click(function (event) { 

    if($(".patient-mediaction:last").find('input').val()=='')
    {
        alert('Please Enter Medication ');
        return;
    }

    html ='<div class="row patient-mediaction">';
    html +='<div class="col-sm-6 m-t-25 ">';
    html +='<input name="medications[]" id="medications" type="text"  class="form-control" placeholder="Enter Medication" >';
    html +='</div>';
    html += '<div class="col-sm-1 text-right m-t-25 delete-medication">';
    html +='<button type="button" class="btn btn-white delete-madication hidden"><i class="fa fa-trash"></i></button>';
    html +='</div>';
    html +='</div>';

    $(".patient-mediaction:last").find('.delete-madication').removeClass('hidden');

    $(".patient-mediaction:last").after(html);

});


$('.medication-data').on('click', '.delete-madication', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    $(this).closest('.patient-mediaction').remove();
});


$('.add-visit').click(function (event) { 

    if($(".patient-visit:last").find('input').val()=='')
    {
        alert('Please Enter Visit ');
        return;
    }



    html ='<div class="row patient-visit">';
    html +='<div class="datetime">'; 
    html +='<div class="col-sm-3 m-t-25 form-group">';
    html +='<div class="input-group date datetimepicker">';
    html +='<input name="visit_date[]" id="visit_date" type="text"   placeholder="Enter Date" class="form-control"/>';
    html +='<span class="input-group-addon" >';
    html +='<span class="glyphicon glyphicon-calendar"></span>';
    html +='</span>';
    html +='</div>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-sm-6 m-t-25 ">';
    html +='<textarea name="note[]" id="note" type="text"   placeholder="Enter Note" class="form-control"></textarea> ';
    html +='</div>';
    html += '<div class="col-sm-1 text-right m-t-25 delete-medication">';
    html +='<button type="button" class="btn btn-white delete-visit hidden"><i class="fa fa-trash"></i></button>';
    html +='</div>';
    html +='</div>';

    $(".patient-visit:last").find('.delete-visit').removeClass('hidden');

    $(".patient-visit:last").after(html);

    $(".patient-visit:last").find('input').datetimepicker({
        format: 'DD-MM-YYYY HH:mm'

      });

});


$('.visit-data').on('click', '.delete-visit', function(event) {
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    $(this).closest('.patient-visit').remove();
});


function authProjectList(Obj ,projectObj)
{
    var result = true;

    Obj.closest('.allProjectsAccess').find('select[name="projects[]"]').each(function () {

        if (projectObj.get(0) != $(this).get(0) && $(this).val() == projectObj.val()) {
            alert('Project Already Selected');
            projectObj.val('');
            result = false;

        }
    });
    return result;
} 

$('.allProjectsAccess').on('change', 'select[name="projects[]"]', function(event) {
    authProjectList($(this) ,$(this));
});

$('.add-project-user').click(function (event) { 

    var objectType = $(this).attr('object-type');

    if($(".project_users:last").find('select').val()=='')
    {
        alert('Please Select '+ objectType);
        return;
    }

    if(!authProjectList($(this) ,$(".project_users:last").find('select')))
    {
         return;
    }

    var addProjects = $(".project_users:last").find('select').html(); 
    var counter = $('input[name="counter"]').val();
    var i = parseInt(counter) + 1;

    html ='<hr><div class="row project_users">';
    html +='<div class="col-md-3">';
    html +='<input type="hidden" name="user_access[]" value="">';
    html +='<select name="projects[]" id="projects" class="select2 form-control"  >';
    html += addProjects
    html +='<select>';
    html +='</div>';
               
    html +='<div class="col-md-3">';
    html +='<div class="radio radio-primary">';
    html +='<input id="access_view_'+i+'" type="radio" name="access_'+i+'" value="view" checked="checked">';
    html +='<label for="access_view_'+i+'">View</label>';
    html +='<input id="access_edit_'+i+'" type="radio" name="access_'+i+'" value="edit">';
    html +='<label for="access_edit_'+i+'">Edit</label>';
    html +='</div>';
    html +='</div>';
    html +='<div class="col-md-3">';
    html +='<a class="deleteUserProjectAccess hidden"> delete </a>';
    html +='</div>';
    html +='</div>';

    $('input[name="counter"]').val(i);
    $(".project_users:last").find('.deleteUserProjectAccess').removeClass('hidden');
    $(".project_users:last").after(html);


});

function validateHospitalUser()
{
    var duplicateHospital =  [];
    var allHospitals =  []; 
    $('select[name="hospital[]"]').each(function() {
            hospital = $(this).val();
            allHospitals.push(hospital);
        
    });

    var sorted_arr = allHospitals.sort();  
    for (var i = 0; i < allHospitals.length-1; i++) {
        if (sorted_arr[i + 1] == sorted_arr[i]) {
            duplicateHospital.push(sorted_arr[i]);
        }
    } 
   
    if (duplicateHospital!='') {
        alert('Duplicate hospital entry made');
        return false;
    }
    
}

$('.deleteUserHospitalAccess').click(function (event) { 
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    var userAccessId = $(this).attr("data-id");

    if(userAccessId)
    {
        $.ajax({
            url: BASEURL + "/admin/user-access/" + userAccessId,
            type: "DELETE",
            success: function (response) {
 
            }
        });
    }

    $(this).closest('.hospital_users').remove();
    
 
});

$('.deleteUserProjectAccess').click(function (event) { 
    if (confirm('Are you sure you want to delete this record?') === false) {
        return;
    }

    var userAccessId = $(this).attr("data-id");

    if(userAccessId)
    {
        $.ajax({
            url: BASEURL + "/delete-user-access/" + userAccessId,
            type: "DELETE",
            success: function (response) {
 
            }
        });
    }

    $(this).closest('.project_users').remove();
    
 
});

function lineChartWithOutBaseLine(chartData,legends,container,xaxisLable,yaxisLabel)
{
    graphs = _.map(legends, function(value, key){ 
        var graphObj = {
        "balloonText": " "+value+" on [[category]]: [[value]]",
        "bullet": "round",
        "title": value,
        "valueField": key,
        "dashLength": 2,
        "inside": true

        };

        return graphObj;
    })
   

    var chart = AmCharts.makeChart(container, {
        "type": "serial",
        "theme": "light",
        // "marginRight": 40,
        // "marginLeft": 40,
        // "autoMarginOffset": 20,
        "legend": {
            "useGraphSettings": true
        },
        "dataProvider": chartData,
        "valueAxes": [{
            "integersOnly": true,
            "maximum": 20,
            "minimum": 0,
            "reversed": false,
            "axisAlpha": 0,
            "dashLength": 5,
            "position": "top",
            "title": yaxisLabel
        }],
         "valueAxes": [{
            "logarithmic": false,
            "dashLength": 0,
            "position": "left",
            "title": yaxisLabel
         }],

        "graphs": graphs,
        "chartCursor": {
            "cursorAlpha": 0,
            "zoomable": false
        },
        // "chartScrollbar": {
          
        //  },
        "categoryField": "Date",
        "categoryAxis": {
            "gridPosition": "start",
            "markPeriodChange": false,
            "minHorizontalGap": 100,
            // "labelRotation": 45,
            "axisAlpha": 0,
             "fillColor": "#000000",
            "gridAlpha": 0,
              "position": "bottom",
            "title": xaxisLable
        },
        "export": {
          "enabled": true,
            "position": "bottom-right"
         }
    });
  //   chart.addListener("dataUpdated", zoomChart);
  //   function zoomChart() {
  //   if (chart.zoomToIndexes) {
  //       chart.zoomToIndexes(130, chartData.length - 1);
  //   }
  // }
}

function lineChartWithBaseLine(chartData,legends,baselineScore,container,xaxisLable,yaxisLabel)
{
    graphs = _.map(legends, function(value, key){ 
        var graphObj = {
          "balloonText": value+" on [[category]]: [[value]]",
              "bullet": "round",
              "title": value,
              "valueField": key,
             "dashLength": 2,
             "inside": true
        };

        return graphObj;
    })

    var baseLineObj = {
        "balloonText": "Baseline in [[category]]: [[value]]",
        "type":"step",
        "lineThickness": 1,
        "title": "Baseline",
        "valueField": "baseLine",
        "bullet": "square",
    
    }
   graphs.push(baseLineObj); 

     var chart = AmCharts.makeChart(container, {
          "type": "serial",
          "theme": "light",
          // "marginRight": 40,
          // "marginLeft": 40,
          // "autoMarginOffset": 20,
          "legend": {
              "useGraphSettings": true
          },
          "dataProvider": chartData,
          "valueAxes": [{
              "integersOnly": true,
              "maximum": 50,
              "minimum": 0,
              "axisAlpha": 0,
              "dashLength": 5,
              "position": "left",
              "title": yaxisLabel
          }],
           "valueAxes": [{
              "logarithmic": false,
              "dashLength": 0,
              // "guides": [{
              //     "dashLength": 6,
              //     "inside": true,
              //     "label": "Baseline",
              //     "lineAlpha": 1,
              //     "value": baselineScore,
         
              // }],
              "position": "left",
              "title": yaxisLabel
               }],
         
          "graphs": graphs,
          "chartCursor": {
              "cursorAlpha": 0,
              "zoomable": false
          },
         //  "chartScrollbar": {
          
         // },
          "categoryField": "Date",
          "categoryAxis": {
              "gridPosition": "start",
              "markPeriodChange": false,
              "minHorizontalGap": 100,
              // "labelRotation": 45,
              "axisAlpha": 0,
               "fillColor": "#000000",
              "gridAlpha": 0,
                "position": "bottom",
              "title": xaxisLable
          },
          "export": {
            "enabled": true,
              "position": "bottom-right"
           }
         });

     // chart.addListener("dataUpdated", zoomChart);
     // function zoomChart() {
     //    if (chart.zoomToIndexes) {
     //      console.log(chartData.length);
     //        chart.zoomToIndexes(130, chartData.length - 1);
     //    }
     //  }
}

function shadedLineChartWithBaseLine(chartData,label,baseLine,container,xaxisLable,yaxisLabel)
{  
    var chart = AmCharts.makeChart(container, {
         "type": "serial",
         "theme": "light",
         // "marginRight": 40,
         //  "marginLeft": 40,
         //  "autoMarginOffset": 20,
         "legend": {
             "useGraphSettings": true
         },
         "dataProvider": chartData,
         "valueAxes": [{
             "integersOnly": true,
             "maximum": 6,
             "minimum": 1,
             "reversed": true,
             "axisAlpha": 0,
             "dashLength": 5,
             "position": "left",
             "title": yaxisLabel
         }],
         
          "valueAxes": [{
             "logarithmic": false,
             "dashLength": 0,
             "position": "left",
            "title": yaxisLabel
              }],
         
         "graphs": [ {
             "balloonText": "[[category]]: [[value]]",
             "bullet": "round",
             "title": "Score",
             "lineColor": "#05A8A5",
             "valueField": "score",
             "fillColor": "#ecb42f",
             "fillAlphas": 0.2,
              "dashLength": 2,
              "hidden":false,
               "inside": true
         
         },
         {
             "balloonText": "[[category]]: [[value]]",
             "type":"step",
             "bullet": "square",
             "title": "Baseline",
             "lineColor": "#333",
             "valueField": "baseLine",
              "dashLength": 2,
              "hidden":false,
               "inside": true
         
         }
         ],
         
         "chartCursor": {
             "cursorAlpha": 0,
             "zoomable": false
         },
         //  "chartScrollbar": {
          
         // },
         "categoryField": "Date",
         "categoryAxis": {
             "gridPosition": "start",
             "markPeriodChange": false,
              "minHorizontalGap": 100,
             "axisAlpha": 0,
              "fillColor": "#000000",
             "gridAlpha": 0,
               "position": "bottom",
             "title": xaxisLable
         },
         "export": {
           "enabled": true,
             "position": "bottom-right"
          }
         });

  //   chart.addListener("dataUpdated", zoomChart);
  //   function zoomChart() {
  //   if (chart.zoomToIndexes) {
  //       chart.zoomToIndexes(20, chartData.length - 1);
  //   }
  // }
}

function patientFlagsChart(chartData)
{
         var chart = AmCharts.makeChart("chartdiv", {
        "type": "serial",
        "theme": "light",
        "legend": {
            "useGraphSettings": true
        },
        "dataProvider": chartData,
        "valueAxes": [{
            "integersOnly": true,
            "maximum": 6,
            "minimum": 1,
            "reversed": false,
            "axisAlpha": 0,
            "dashLength": 5,
            "position": "left",
            "title": "Total Score"
        }],
        
        "graphs": [{
            "balloonText": "Red Flag on [[category]]: [[value]]",
            "bullet": "round",
            "title": "Red",
           "lineColor": "#CC0000",
            "valueField": "Red",
            "fillColor": "#CC0000",
            "fillAlphas": 0.2,
        "dashLength": 2,
        "inside": true
        
        }, {
            "balloonText": " Amber Flag on [[category]]: [[value]]",
            "bullet": "round",
            "title": "Amber",
            "lineColor": "#ecb42f",
            "valueField": "Amber",
           "dashLength": 2,
           "fillColor": "#ecb42f",
            "fillAlphas": 0.2,
           "hidden":false,
           "inside": true
        }, {
            "balloonText": "Green Flag on [[category]]: [[value]]",
            "bullet": "round",
            "title": "Green",
            "lineColor": "#05A8A5",
            "valueField": "Green",
            "fillColor": "#ecb42f",
            "fillAlphas": 0.2,
             "dashLength": 2,
             "hidden":false,
              "inside": true

        }],
        "chartCursor": {
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "Date",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
             "fillColor": "#000000",
            "gridAlpha": 0,
              "position": "bottom",
            "title": "Submission"
        },
        "export": {
          "enabled": true,
            "position": "bottom-right"
         }
    });
}

function submissionChart(chartData,baseLine)
{
    var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "light",
    "marginRight": 70,
    "autoMarginOffset": 20,
    "dataProvider": chartData,
    "balloon": {
        "cornerRadius": 6
    },
    // "valueAxes": [{
    //     "axisAlpha": 0
    // }],
    "valueAxes": [{
    "logarithmic": true,
    "dashLength": 1,
    "guides": [{
        "dashLength": 6,
        "inside": true,
        "label": "Baseline",
        "lineAlpha": 1,
        "value": baseLine
    }],
       }],
    "graphs": [{
        "balloonText": "[[category]]<br><b><span style='font-size:14px;'>[[value]] Score</span></b>",
        "bullet": "round",
        "bulletSize": 6,
        "connect": false,
        "lineColor": "#b6d278",
        "lineThickness": 2,
        "negativeLineColor": "#487dac",
        "valueField": "value"
    }],
    "chartCursor": {
        "categoryBalloonDateFormat": "DD-MM-YYYY",
        "cursorAlpha": 0.1,
        "cursorColor": "#000000",
        "fullWidth": true,
        "graphBulletSize": 2
    },
    "chartScrollbar": {},
    "dataDateFormat": "DD-MM-YYYY",
    "categoryField": "date",
    "categoryAxis": {
        "minPeriod": "DD",
        "parseDates": true,
        "minorGridEnabled": true
    }
});


}

function submissionBarChart(chartData,container)
{
    var chart = AmCharts.makeChart(container, {
    "theme": "light",
    "type": "serial",
     "legend": {
    "useGraphSettings": true
  },
    "dataProvider": chartData,
    "startDuration": 1,
    "graphs": [{
        "balloonText": "Previous [[category]] (: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Previous",
        "type": "column",
        "valueField": "prev"
    }, {
        "balloonText": "Baseline [[category]] : <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Baseline",
        "type": "column",
        "valueField": "base"
    },{
        "balloonText": "Current [[category]]: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Current",
        "type": "column",
        "clustered":false,
        "columnWidth":0.5,
        "valueField": "current"
    }],
    "plotAreaFillAlphas": 0.1,
    "categoryField": "question",
    "categoryAxis": {
        "gridPosition": "start"
    },
    "export": {
      "enabled": true
     }

});


}


function miniGraph(chartData,container)
{  
    AmCharts.makeChart(container, {
    "type": "serial",
    "dataProvider": chartData,
    "valueAxes": [{
        "axisAlpha": 0,
        "gridAlpha": 0
    }],
    "graphs": [{
        "dashLength": 2,
        "inside": true,
         "title": "score",
         "lineColor": "#05A8A5",
        "valueField": "score"
    },
     {
        "type":"step",
        "lineThickness": 0.5,
        "title": "Baseline",
        "lineColor": "#576475",
        "valueField": "baseLine"
    }         ],
    "marginTop": 0,
    "marginRight": 0,
    "marginLeft": 0,
    "marginBottom": 0,
    "autoMargins": false,
    "categoryField": "day",
    "categoryAxis": {
        "axisAlpha": 0,
        "gridAlpha": 0
    }
});
 
}





// function miniGraph(chartData,container)
// {
//     // line chart, with different line color below zero         
//     var chart = new AmCharts.AmSerialChart(AmCharts.themes.none);
//     chart.dataProvider = chartData;
//     chart.categoryField = "submission";
//     chart.autoMargins = false;
//     chart.marginLeft = 0;
//     chart.marginRight = 5;
//     chart.marginTop = 0;
//     chart.marginBottom = 0;

//     var graph = new AmCharts.AmGraph();
//     graph.valueField = "score";
//     graph.showBalloon = false;
//     graph.lineColor = "#ffbf63";
//     graph.negativeLineColor = "#289eaf";
//     chart.addGraph(graph);

//     var baseLine = new AmCharts.AmGraph();
//     baseLine.valueField = "baseLine";
//     baseLine.showBalloon = false;
//     baseLine.lineColor = "#000";
//     baseLine.negativeLineColor = "#84B761";
//     chart.addGraph(baseLine);

//     var valueAxis = new AmCharts.ValueAxis();
//     valueAxis.gridAlpha = 0;
//     valueAxis.axisAlpha = 0;
//     chart.addValueAxis(valueAxis);

//     var categoryAxis = chart.categoryAxis;
//     categoryAxis.gridAlpha = 0;
//     categoryAxis.axisAlpha = 0;
//     categoryAxis.startOnAxis = true;

//     // using guide to show 0 grid
//     // var guide = new AmCharts.Guide();
//     // guide.value = 0;
//     // guide.lineAlpha = 0.1;
//     // valueAxis.addGuide(guide);
//     chart.write(container);
 
// }


$('.sortSubmission').click(function (event) { 
       var startDate = $('input[name="startDate"]').val();
       var endDate = $('input[name="endDate"]').val();

       var sortObject = $(this);
       var sort_type = sortObject.attr('sort-type'); 
       var sort = sortObject.attr('sort'); 
       var limit = $("#submissionData").attr('limit');
       var object_type = $("#submissionData").attr('object-type');
       var object_id = $("#submissionData").attr('object-id');

       var cond_type = sortObject.closest('table').attr('cond-type'); 
       var cond = sortObject.closest('table').attr('cond'); 

       sortObject.closest('.grid-body').find(".loader-outer").removeClass('hidden');
       $.ajax({
        url: BASEURL+"/getsubmissionlist",
        type: "GET",
        data: {
            sort: sort+'-'+sort_type,
            cond: cond+'-'+cond_type,
            startDate:startDate ,
            endDate:endDate ,
            limit:limit ,
            object_type:object_type,
            object_id:object_id  
        },
        dataType: "JSON",
        success: function (response) {
            
            console.log(sort_type);  
           if(sort_type=='asc')
            { 
              sortObject.attr('sort-type','desc');
              
              sortObject.find('.sortCol').removeClass('fa-angle-down');
              sortObject.find('.sortCol').addClass('fa-angle-up');
            }
            else
            {  
              sortObject.attr('sort-type','asc');
              sortObject.find('.sortCol').addClass('fa-angle-down');
              sortObject.find('.sortCol').removeClass('fa-angle-up');

            } 
            $("#submissionData").html(response.data);
             

           sortObject.closest('.grid-body').find(".loader-outer").addClass('hidden');
        }
      });

    });

 
$( document ).on("click", ".sortPatientSummary", function() {
       var startDate = $('input[name="startDate"]').val();
       var endDate = $('input[name="endDate"]').val();

       var sortObject = $(this);
       var sort_type = sortObject.attr('sort-type'); 
       var sort = sortObject.attr('sort'); 
       var limit = $("#patientSummaryData").attr('limit');
       sortObject.closest('.grid-body').find(".loader-outer").removeClass('hidden');
       $.ajax({
        url: BASEURL+"/getpatientsummarylist",
        type: "GET",
        data: {
            sort: sort+'-'+sort_type,
            startDate:startDate ,
            endDate:endDate ,
            limit:limit 
        },
        dataType: "JSON",
        success: function (response) {
 
           if(sort_type=='asc')
            { 
              
              sortObject.attr('sort-type','desc');
              
              sortObject.find('.sortCol').removeClass('fa-angle-down');
              sortObject.find('.sortCol').addClass('fa-angle-up');
            }
            else
            {  
              
              sortObject.attr('sort-type','asc');
              sortObject.find('.sortCol').addClass('fa-angle-down');
              sortObject.find('.sortCol').removeClass('fa-angle-up');

            } 

            $("#patientSummaryData").html(response.data);
            miniChartData = response.miniChartData;
              
            $.each(response.patientIds, function (index, value) {  
              chartData = $.parseJSON( miniChartData[index] );
 
              miniGraph(chartData,'chart_mini_'+value);

           }); 

          sortObject.closest('.grid-body').find(".loader-outer").addClass('hidden');
        }
      });

    });

