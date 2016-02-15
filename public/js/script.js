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
                alert('Reference Code Already Taken');
                $("#reference_code").val('');
            }

            // $(".cf-loader").addClass('hidden');
        }
    });
    
 
});

    $('select[name="updateSubmissionStatus"]').change(function (event) { 
       var status = $(this).val();
       var responseId = $(this).attr('object-id');
       $("#statusLoader").removeClass('hidden');
       $.ajax({
        url: BASEURL+"/submissions/"+responseId+"/updatesubmissionstatus",
        type: "POST",
        data: {
            status: status
        },
        dataType: "JSON",
        success: function (response) {
          $("#statusLoader").addClass('hidden');
            // if (!response.data)
            // {   
            //     alert('Reference Code Already Taken');
            //     $("#reference_code").val('');
            // }

            // $(".cf-loader").addClass('hidden');
        }
      });

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

$('.add-hospital-user').click(function (event) { 

    var objectType = $(this).attr('object-type');

    if($(".hospital_users:last").find('select').val()=='')
    {
        alert('Please Select '+ objectType);
        return;
    }

    var addHospital = $(".hospital_users:last").find('select').html(); 
    var counter = $('input[name="counter"]').val();
    var i = parseInt(counter) + 1;

    html ='<hr><div class="row hospital_users">';
    html +='<div class="col-md-3">';
    html +='<input type="hidden" name="user_access[]" value="">';
    html +='<select name="hospital[]" id="hospital" class="select2 form-control"  >';
    html += addHospital
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
    html +='<a class="deleteUserHospitalAccess hidden"> delete </a>';
    html +='</div>';
    html +='</div>';

    $('input[name="counter"]').val(i);
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

$('.add-project-user').click(function (event) { 

    var objectType = $(this).attr('object-type');

    if($(".project_users:last").find('select').val()=='')
    {
        alert('Please Select '+ objectType);
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
    html +='<a class="deleteUserProjAccess hidden"> delete </a>';
    html +='</div>';
    html +='</div>';

    $('input[name="counter"]').val(i);
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

function lineChartWithOutBaseLine(chartData,legends,container)
{
    graphs = _.map(legends, function(value, key){ 
        var graphObj = {
        "balloonText": " "+value+" in [[category]]: [[value]]",
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
        "legend": {
            "useGraphSettings": true
        },
        "dataProvider": chartData,
        "valueAxes": [{
            "integersOnly": true,
            "maximum": 20,
            "minimum": 0,
            "reversed": true,
            "axisAlpha": 0,
            "dashLength": 5,
            "position": "top",
            "title": "Total Score"
        }],
         "valueAxes": [{
            "logarithmic": false,
            "dashLength": 0,
        
         }],

        "graphs": graphs,
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

function lineChartWithBaseLine(chartData,legends,baselineScore,container)
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
   
     var chart = AmCharts.makeChart(container, {
          "type": "serial",
          "theme": "light",
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
              "position": "right",
              "title": "Total Score"
          }],
           "valueAxes": [{
              "logarithmic": true,
              "dashLength": 1,
              "guides": [{
                  "dashLength": 6,
                  "inside": true,
                  "label": "Baseline",
                  "lineAlpha": 1,
                  "value": baselineScore,
         
              }],
               }],
         
          "graphs": graphs,
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
              "title": "Date"
          },
          "export": {
            "enabled": true,
              "position": "bottom-right"
           }
         });
}

function shadedLineChartWithBaseLine(chartData,label,baseLine,container)
{  
    var chart = AmCharts.makeChart(container, {
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
             "reversed": true,
             "axisAlpha": 0,
             "dashLength": 5,
             "position": "right",
             "title": "Total Score"
         }],
         
          "valueAxes": [{
             "logarithmic": false,
             "dashLength": 0,
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
             "bullet": "round",
             "title": "Baseline",
             "lineColor": "#000",
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
         "categoryField": "Date",
         "categoryAxis": {
             "gridPosition": "start",
             "axisAlpha": 0,
              "fillColor": "#000000",
             "gridAlpha": 0,
               "position": "bottom",
             "title": "Projects"
         },
         "export": {
           "enabled": true,
             "position": "bottom-right"
          }
         });
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
            "position": "top",
            "title": "Total Score"
        }],
        
        "graphs": [{
            "balloonText": "Red Flag in [[category]]: [[value]]",
            "bullet": "round",
            "title": "Red",
           "lineColor": "#CC0000",
            "valueField": "Red",
            "fillColor": "#CC0000",
            "fillAlphas": 0.2,
        "dashLength": 2,
        "inside": true
        
        }, {
            "balloonText": " Amber Flag in [[category]]: [[value]]",
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
            "balloonText": "Green Flag in [[category]]: [[value]]",
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



