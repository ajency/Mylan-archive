
window.addEventListener("load", function(e) {
	 $('#login-form').submit(function(e) { 
		e.preventDefault();
		if ( $(this).parsley().isValid() ) {
			$.ajax({
				url: 'http://mylantest.ajency.in/api/v3/ajaxCApi',
				crossDomain : true,
				type: 'GET',
				data: $( "#login-form" ).serialize(),
				success: function(data){
					if(data.status == 200){
						 $(".step-1").css("display","none");
						 $("#hospitalList").html(data.hospital);
						 var userObj = "";
						userObj = $("#email").val()
						chrome.storage.local.get( userObj, function (result) {
							//set hospital id
							$("#hiddenHospitalId").val(result[userObj].hospital);
							if(result[userObj].hospital != ""){
								$(".step-2").css("display","block");
								$.ajax({
									//url: 'http://mylantest.ajency.in/api/v3/fillproject',
									url: 'http://mylan.local/api/v3/fillproject',
									crossDomain : true,
									type: 'GET',
									data: { 'hospitalId' : $("#hiddenHospitalId").val()},
									success: function(data){
										$("#projectList").html(data.projects);
										$("#projectListItems").html(data.projectItem);
										$("#projectListItems").css("display","block");		
										$("#projectList").prop('disabled', false);
									}
								});
							}else{
								$(".selectHospital").css("display","block");
							}
						});
							
						 $("#userEmail").val($("#email").val());
						 var myVar =""; 
						 myVar = $("#email").val();
						 var obj= {};
						 obj[myVar] = data.userEmail;
						 chrome.storage.local.set(obj);
						 chrome.cookies.set({
							"name": "user",
							"url": "http://mylan.local",
							// "url": "http://mylantest.ajency.in",
							"value": $("#email").val()
						 });
					}else{	
						$( ".infoMsg" ).html("<small>"+data.message+"</small>");
					}	
				}
			});
		}
	});
	
	 $('#select-hospital-form').submit(function(e) { 
		e.preventDefault();
		if ( $(this).parsley().isValid() ) {
			 $(".selectHospital").css("display","none");
			 $(".step-2").css("display","block");
			 var myVar =""; 
			 myVar =  $("#userEmail").val();
			 var obj= {};
			 var hospitalData  = {"hospital":$("#hospitalList").val()};
			 obj[myVar] = hospitalData;
			 chrome.storage.local.set(obj);
			 
		}	
	});	
	
	$("#hospitalList").on("change", function(ev){
		if($("#hospitalList").val() > 0){
			$.ajax({
				//url: 'http://mylantest.ajency.in/api/v3/fillproject',
				url: 'http://mylan.local/api/v3/fillproject',
				crossDomain : true,
				type: 'GET',
				data: { 'hospitalId' : $("#hospitalList").val()},
				success: function(data){
					$("#projectList").html(data.projects);
					$("#projectList").prop('disabled', false);	
					$("#projectListItems").html(data.projectItem);					
					$("#projectListItems").css("display","block");
											
				}
			});
		}else{
			$("#projectList").html("");
		}
	});
	
	 $(document).delegate("#projectListItems ul li","click", function(e){
		var userObj = "";
			chrome.cookies.get({"url": "http://mylan.local", "name": "user"}, function(cookie) {
				userObj = cookie.value;
				chrome.storage.local.get( userObj, function (result) {
					//set hospital id
					$("#hiddenHospitalId").val(result[userObj].hospital);
				});
			});
			
			var projectListVal = this.id;
			$.ajax({
				// url: 'http://mylantest.ajency.in/api/v3/mapping-data',
				url: 'http://mylan.local/api/v3/mapping-data',
				crossDomain : true,
				type: 'GET',
				data: {'hospitalList': $("#hiddenHospitalId").val() , 'projectList': projectListVal },
				success: function(data){
					if(data.status == 200){
						 $( ".step-3 tbody#mapping-tbody" ).html(data.content);	
						 for(var i=0; i<data.referCode.length; i++){
							var myVar = "";
							myVar = data.referCode[i];
							chrome.storage.local.get( myVar, function (result) {
								var key = "";
								key = Object.keys(result);
								$(".patientId-"+key).html(result[key].name);
							});	
						 }
						
						 $( ".step-3 span.hospitalName , .step-3-edit span.hospitalName" ).html(data.hospitalName);	
						 $( ".step-3 span.projectName, .step-3-edit span.projectName" ).html(data.projectName);	
						  $("#newmap").css("display","none");
						 if(data.content == ""){
							 $( ".step-3 tbody#mapping-tbody" ).html( "<tr><td colspan='3'> No data found</td></tr>");
						 }
						 $(".step-3").css("display","block");
						 // $("#hiddenHospitalId").val($("#hospitalList").val());
						 $("#hiddenProjectId").val(projectListVal);
							
						 $heightval = $(window).height() - $('.step-3 .table-cover').position().top - 25;
						 $('.step-3 .table-cover').css('max-height', $heightval);	
					}
				}
			});
	});
	
	$('#mapping-form').submit(function(e) { 
		e.preventDefault();
		if ( $(this).parsley().isValid() ) {
			var userObj = "";
			chrome.cookies.get({"url": "http://mylan.local", "name": "user"}, function(cookie) {
				userObj = cookie.value;
				chrome.storage.local.get( userObj, function (result) {
					//set hospital id
					$("#hiddenHospitalId").val(result[userObj].hospital);
				});
			});
			
			var projectListVal = $("#projectList").val();
			$.ajax({
				// url: 'http://mylantest.ajency.in/api/v3/mapping-data',
				url: 'http://mylan.local/api/v3/mapping-data',
				crossDomain : true,
				type: 'GET',
				data: {'hospitalList': $("#hiddenHospitalId").val() , 'projectList': projectListVal },
				success: function(data){
					if(data.status == 200){
						 $( ".step-3 tbody#mapping-tbody" ).html(data.content);	
						 for(var i=0; i<data.referCode.length; i++){
							var myVar = "";
							myVar = data.referCode[i];
							chrome.storage.local.get( myVar, function (result) {
								var key = "";
								key = Object.keys(result);
								$(".patientId-"+key).html(result[key].name);
							});	
						 }
						
						 $( ".step-3 span.hospitalName , .step-3-edit span.hospitalName" ).html(data.hospitalName);	
						 $( ".step-3 span.projectName, .step-3-edit span.projectName" ).html(data.projectName);	
						  $("#newmap").css("display","none");
						 if(data.content == ""){
							 $( ".step-3 tbody#mapping-tbody" ).html( "<tr><td colspan='3'> No data found</td></tr>");
						 }
						 $(".step-3").css("display","block");
						 // $("#hiddenHospitalId").val($("#hospitalList").val());
						 $("#hiddenProjectId").val($("#projectList").val());
							
						 $heightval = $(window).height() - $('.step-3 .table-cover').position().top - 25;
						 $('.step-3 .table-cover').css('max-height', $heightval);	
					}
				}
			});
		}
	});

	 $(document).delegate(".edit-case","click", function(evt){
		 $(".step-3").css("display","none");
		 $(".step-3-edit").css("display","block");
		 $("#patient_edit_id").val(this.id);
		 var myVar = "";
		 myVar = this.id;
		 chrome.storage.local.get( myVar, function (result) {
			 var key = "";
			 key = Object.keys(result);
			 $("#patient_edit_name").val(result[key].name);
		 });
	});	
	$(document).on('click', '.edit-case-lastmap', function(e) {
		e.preventDefault();
		$('#lastmap').hide();
		$('#lastmap-edit').show();
	});
	$(document).on('click', '.lastmap-goback', function(e) {
		e.preventDefault();
		$('#lastmap').show();
		$('#lastmap-edit').hide();
		
	});
	
	$("#logout").on("click", function(e){
		chrome.cookies.remove({"url": "http://mylan.local", "name": "user"});
		window.close();
	});
	$('#edit-patient-form').submit(function(e) { 
		e.preventDefault();
		if ( $(this).parsley().isValid() ) {
			var myVar =""; 
			myVar = $("#patient_edit_id").val();
			var obj= {};
			obj[myVar] = {"referrence_code" : $("#patient_edit_id").val(), "name" : $("#patient_edit_name").val(), "projectId" : $("#hiddenProjectId").val(), "hospitalId" : $("#hiddenHospitalId").val() };
			chrome.storage.local.set(obj);
			$("#edit-patient-form .alert.alert-success").css("display","block");
		}
	});	
	
	$('#step-3-edit-back, .gobackpop').on("click",function(e){
		$('.step-3-edit, #edit-patient-form .alert.alert-success').css("display","none");
		$("#projectListItems ul li#"+$("#hiddenProjectId").val()).click();
	});
	
	$('#back-step2, .gobackpop').on("click",function(e){
		$('.step-3').css("display","none");
		$('.step-2').css("display","block");
	});
	
	$('.menu-link').click(function(e) {
		e.preventDefault();
		/* code to populate last filled data*/
		var stringData = "";
		chrome.storage.local.get(null, function(items) {
			var allKeys = Object.keys(items);
			$.each( allKeys, function( index, value ){
				var myVar = "";
				myVar = value;
				chrome.storage.local.get( myVar, function (result) {
					stringData += '<tr><td>'+result[myVar].referrence_code+'</td><td class="patientId-'+result[myVar].referrence_code+'">'+result[myVar].name+'</td><td><span class="text-right edit-case-lastmap" id="'+result[myVar].referrence_code+'"><i class="fa fa-pencil" id="'+result[myVar].referrence_code+'"></i></span></td></tr>';
					$("#last-mapped-tbody").html(stringData);
				});
			});
		});
		
		
		/* code to populate last filled data*/
		$('.panels').hide();
		$($(this).attr('href')).show();
		$('.menu-item').removeClass('current');
		$(this).parent('.menu-item').addClass('current');
		$heightval = $(window).height() - $('#lastmap .table-cover').position().top - 25;
		$('#lastmap .table-cover').css('max-height', $heightval);
	});

	$('.right-area').height($(window).height() - 2);
	$(window).resize(function() {
		$('.right-area').height($(window).height() - 2);
	});
	
	$(document).ready(function(e){
		chrome.cookies.get({"url": "http://mylan.local", "name": "user"}, function(cookie) {
		// chrome.cookies.get({"url": "http://mylantest.ajency.in", "name": "authenticate"}, function(cookie) {
			if(cookie){
				var userObj = "";
				userObj = cookie.value;
				$("#userEmail").val(userObj);
				chrome.storage.local.get( userObj, function (result) {
					//set hospital id
					$("#hiddenHospitalId").val(result[userObj].hospital);
					
					if($("#hiddenHospitalId").val() != ""){
						$(".step-1").css("display","none");
						$(".step-2").css("display","block");
						$.ajax({
							//url: 'http://mylantest.ajency.in/api/v3/fillproject',
							url: 'http://mylan.local/api/v3/fillproject',
							crossDomain : true,
							type: 'GET',
							data: { 'hospitalId' : $("#hiddenHospitalId").val()},
							success: function(data){
								$("#projectList").html(data.projects);
								$("#projectList").prop('disabled', false);
								$("#projectListItems").html(data.projectItem);					
								$("#projectListItems").css("display","block");								
							}
						});
					}else{
						$(".step-1").css("display","none");
						$(".selectHospital").css("display","block");
						$.ajax({
							url: 'http://mylan.local/api/v3/hospital-data',
							// url: 'http://mylantest.ajency.in/api/v3/hospital-data',
							crossDomain : true,
							type: 'GET',
							success: function(data){
								if(data.status == 200){
									 $("#hospitalList").html(data.hospital);
								}
							}
						});
					}
				});
			}
		});
	});
});