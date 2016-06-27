
window.addEventListener("load", function(e) {
	 $('#login-form').submit(function(e) { 
		e.preventDefault();
		if ( $(this).parsley().isValid() ) {
			$.ajax({
				url: 'http://mylantest.ajency.in/api/v3/ajaxCApi',
				// url: 'http://mylan.local/api/v3/ajaxCApi',
				crossDomain : true,
				type: 'GET',
				data: $( "#login-form" ).serialize(),
				success: function(data){
					if(data.status == 200){
						 $(".step-1").css("display","none");
						 $("#hospitalList").html(data.hospital);
						var userObj = "";
						userObj = $("#email").val()+"_hospitalId";
						 $("#userEmail").val($("#email").val());
						 chrome.storage.local.get( userObj, function (result) {
							if(Object.getOwnPropertyNames(result).length === 0){
								$(".selectHospital").css("display","block");
							}else{
								if(result[userObj].hospital){
									$("#hiddenHospitalId").val(result[userObj].hospital);
									if(result[userObj].hospital != ""){
										$(".step-2").css("display","block");
										$.ajax({
											url: 'http://mylantest.ajency.in/api/v3/fillproject',
											// url: 'http://mylan.local/api/v3/fillproject',
											crossDomain : true,
											type: 'GET',
											data: { 'hospitalId' : $("#hiddenHospitalId").val()},
											success: function(data){
												$(".logoutClass").html(data.projectItem);
												$("#projectList").html(data.projects);
												$("#projectList").prop('disabled', false);
												$(".logoutClass li").first().click();
											}
										});
									}else{
										$(".selectHospital").css("display","block");
									}
								}else{
									$(".selectHospital").css("display","block");
								}
							}	
						});
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
			 var userObj = "";
			userObj = $("#email").val()+"_hospitalId";
			 $("#hiddenHospitalId").val($("#hospitalList").val());
			 var obj= {};
			 var hospitalData  = {"hospital":$("#hospitalList").val()};
			 obj[userObj] = hospitalData;
			 chrome.storage.local.set(obj);
			 
		}	
	});	
	
	$("#hospitalList").on("change", function(ev){
		if($("#hospitalList").val() > 0){
			$.ajax({
				url: 'http://mylantest.ajency.in/api/v3/fillproject',
				// url: 'http://mylan.local/api/v3/fillproject',
				crossDomain : true,
				type: 'GET',
				data: { 'hospitalId' : $("#hospitalList").val()},
				success: function(data){
					$(".logoutClass").html(data.projectItem);
					$("#projectList").html(data.projects);
					$("#projectList").prop('disabled', false);
					$(".logoutClass li").first().click();						
				}
			});
		}else{
			$("#projectList").html("");
		}
	});
	
	 $(document).delegate(".logoutClass li","click", function(e){
		 $(".step-3-edit").css("display","none");
		var userObj = "";
			$(".logoutClass li#"+this.id).html()
			 $( ".step-3 span.hospitalName , .step-3-edit span.hospitalName" ).html();	
			$( ".step-3 span.projectName, .step-3-edit span.projectName" ).html();	
			var projectListVal = this.id;
			$.ajax({
				url: 'http://mylantest.ajency.in/api/v3/mapping-data',
				// url: 'http://mylan.local/api/v3/mapping-data',
				crossDomain : true,
				type: 'GET',
				data: {'hospitalList': $("#hiddenHospitalId").val() , 'projectList': projectListVal },
				success: function(data){
					if(data.status == 200){
						 $( ".step-3 tbody#mapping-tbody" ).html(data.content);	
						var myVar = "";
						myVar = $("#userEmail").val();
						chrome.storage.local.get( myVar, function (result) {
							objectsData = result[$("#userEmail").val()].projectIdObj[projectListVal];//gets the reference value
							console.log(objectsData);
							for(var key in objectsData) {
								$(".patientId-"+key).html(objectsData[key].name);
							}
						});	
						
						  $("#newmap").css("display","none");
						 if(data.content == ""){
							 $( ".step-3 tbody#mapping-tbody" ).html( "<tr><td colspan='3'> No data found</td></tr>");
						 }
						 $(".step-3").css("display","block");
						 $("#hiddenProjectId").val(projectListVal);
							
						 $heightval = $(window).height() - $('.step-3 .table-cover').position().top - 35;
						 $('.step-3 .table-cover').css('max-height', $heightval);	
						 
					}
					 $( ".step-3 span.hospitalName , .step-3-edit span.hospitalName" ).html(data.hospitalName);	
					 $( ".step-3 span.projectName, .step-3-edit span.projectName" ).html(data.projectName);	
				}
			});
	});
	
	$('#mapping-form').submit(function(e) { 
		e.preventDefault();
		if ( $(this).parsley().isValid() ) {
			var userObj = "";
			chrome.cookies.get({"url": "http://mylan.local", "name": "user"}, function(cookie) {
				userObj = cookie.value+"_hospitalId";
				chrome.storage.local.get( userObj, function (result) {
					// set hospital id
					$("#hiddenHospitalId").val(result[userObj].hospital);
				});
			});
			
			var projectListVal = $("#projectList").val();
			$.ajax({
				url: 'http://mylantest.ajency.in/api/v3/mapping-data',
				// url: 'http://mylan.local/api/v3/mapping-data',
				crossDomain : true,
				type: 'GET',
				data: {'hospitalList': $("#hiddenHospitalId").val() , 'projectList': projectListVal },
				success: function(data){
					if(data.status == 200){
						 $( ".step-3 tbody#mapping-tbody" ).html(data.content);	
						 for(var i=0; i<data.referCode.length; i++){
							var myVar = "";
							myVar = $("#userEmail").val();
							chrome.storage.local.get( myVar, function (result) {
								var key = "";
								key = Object.keys(result);
								console.log(result);
								//$(".patientId-"+result[key].referrence_code).html(result[key].name);
							});	
						 }
						
						 $( ".step-3 span.hospitalName , .step-3-edit span.hospitalName" ).html(data.hospitalName);	
						 $( ".step-3 span.projectName, .step-3-edit span.projectName" ).html(data.projectName);	
						  $("#newmap").css("display","none");
						 if(data.content == ""){
							 $( ".step-3 tbody#mapping-tbody" ).html( "<tr><td colspan='3'> No data found</td></tr>");
						 }
						 $(".step-3").css("display","block");
						 $("#hiddenHospitalId").val($("#hospitalList").val());
						 $("#hiddenProjectId").val($("#projectList").val());
							
						 $heightval = $(window).height() - $('.step-3 .table-cover').position().top - 35;
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
		myVar = $("#userEmail").val();
		chrome.storage.local.get( myVar, function (result) {
			objectsData = result[$("#userEmail").val()].projectIdObj[$("#hiddenProjectId").val()];//gets the reference value
			$("#patient_edit_name").val();
			$("#patient_edit_name").val(objectsData[$("#patient_edit_id").val()].name);
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
			saveEditedInfo();
			$("#edit-patient-form .alert.alert-success").css("display","block");
		}
	});	
	
	$('#step-3-edit-back, .gobackpop').on("click",function(e){
		$('.step-3-edit, #edit-patient-form .alert.alert-success').css("display","none");
		$(".logoutClass li#"+$("#hiddenProjectId").val()).click();
		$("#patient_edit_name").val("");
	});
	
	$('#back-step2, .gobackpop').on("click",function(e){
		$('.step-3').css("display","none");
		$('.step-2').css("display","block");
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
				userObj = cookie.value+"_hospitalId";
				$("#userEmail").val(cookie.value);
				chrome.storage.local.get( userObj, function (result) {
					//set hospital id
					if(Object.getOwnPropertyNames(result).length === 0){
						$(".step-1").css("display","none");
						$(".selectHospital").css("display","block");
						$.ajax({
							// url: 'http://mylan.local/api/v3/hospital-data',
							url: 'http://mylantest.ajency.in/api/v3/hospital-data',
							crossDomain : true,
							type: 'GET',
							success: function(data){
								if(data.status == 200){
									 $("#hospitalList").html(data.hospital);
								}
							}
						});
					}else{
						if(result[userObj].hospital != "undefined"){
							$("#hiddenHospitalId").val(result[userObj].hospital);
							if($("#hiddenHospitalId").val() != ""){
								$(".step-1").css("display","none");
								$(".step-2").css("display","block");
								$.ajax({
									url: 'http://mylantest.ajency.in/api/v3/fillproject',
									// url: 'http://mylan.local/api/v3/fillproject',
									crossDomain : true,
									type: 'GET',
									data: { 'hospitalId' : $("#hiddenHospitalId").val()},
									success: function(data){
										$(".logoutClass").html(data.projectItem);
										$("#projectList").html(data.projects);
										$("#projectList").prop('disabled', false);
										$(".logoutClass li").first().click();
										
									}
								});
									
							}
						}else{
							$(".step-1").css("display","none");
							$(".selectHospital").css("display","block");
							$.ajax({
								// url: 'http://mylan.local/api/v3/hospital-data',
								url: 'http://mylantest.ajency.in/api/v3/hospital-data',
								crossDomain : true,
								type: 'GET',
								success: function(data){
									if(data.status == 200){
										 $("#hospitalList").html(data.hospital);
									}
								}
							});
						}
					}
					
				});
			}
		});
	});
	//  ============================================================================================================================================================//
	function saveEditedInfo(){
		var projectId,referrence_code,name,hospitalId="";
		projectId = $("#hiddenProjectId").val();
		referrence_code =  $("#patient_edit_id").val();
		name = $("#patient_edit_name").val();
		hospitalId = $("#hiddenHospitalId").val();
		var myVar = $("#userEmail").val();
		chrome.storage.local.get( $("#userEmail").val(), function (result) {
			if(Object.getOwnPropertyNames(result).length === 0){
				obj= {};
					projectIdObj = {};
					projectIdObj[String($("#hiddenProjectId").val())]= {};
					projectIdObj[String($("#hiddenProjectId").val())][$("#patient_edit_id").val()] =  { "referrence_codes" : $("#patient_edit_id").val(), "name" : $("#patient_edit_name").val(), "projectId" : $("#hiddenProjectId").val(), "hospitalId" : hospitalId };
					obj[$("#userEmail").val()] = {"hospitalId" : "hospital id" , projectIdObj };
					chrome.storage.local.set(obj);
					chrome.storage.local.get( $("#userEmail").val(), function (result) {
						console.log(result);
					});
			}else{
				if(result[$("#userEmail").val()].projectIdObj != undefined){
					allKeys = Object.keys(result[$("#userEmail").val()].projectIdObj);
					var objectsData = "";
					var projectIdObj = {};
					for(var i=0;i<allKeys.length;i++){
						objectsData = result[$("#userEmail").val()].projectIdObj[allKeys[i]];//gets the reference value
						projectIdObj[allKeys[i]]= {};
						for(var key in objectsData) {
							projectIdObj[allKeys[i]][key] = objectsData[key];
						}
						if(allKeys[i] == $("#hiddenProjectId").val()){
							projectIdObj[$("#hiddenProjectId").val()][$("#patient_edit_id").val()] =  { "referrence_codes" : $("#patient_edit_id").val(), "name" : $("#patient_edit_name").val(), "projectId" : $("#hiddenProjectId").val(), "hospitalId" : hospitalId };
						}	
						if(i+1 == allKeys.length && allKeys[i] != $("#hiddenProjectId").val() ){
							referrence_code =  $("#patient_edit_id").val();
							name = $("#patient_edit_name").val();
							hospitalId = $("#hiddenHospitalId").val();
							projectIdObj[$("#hiddenProjectId").val()]= {};
							projectIdObj[$("#hiddenProjectId").val()][$("#patient_edit_id").val()] =  { "referrence_codes" : $("#patient_edit_id").val(), "name" : $("#patient_edit_name").val(), "projectId" : $("#hiddenProjectId").val(), "hospitalId" : hospitalId };
							
						}
					}
					var obj= {};
					obj[$("#userEmail").val()] = {"hospitalId" : "hospital id" , projectIdObj };
					chrome.storage.local.set(obj);
					chrome.storage.local.get( $("#userEmail").val(), function (result) {
						console.log(result);
					});
				}else{
					obj= {};
					projectIdObj = {};
					projectIdObj[String($("#hiddenProjectId").val())]= {};
					projectIdObj[String($("#hiddenProjectId").val())][$("#patient_edit_id").val()] =  { "referrence_codes" : $("#patient_edit_id").val(), "name" : $("#patient_edit_name").val(), "projectId" : $("#hiddenProjectId").val(), "hospitalId" : hospitalId };
					obj[$("#userEmail").val()] = {"hospitalId" : "hospital id" , projectIdObj };
					chrome.storage.local.set(obj);
					chrome.storage.local.get( $("#userEmail").val(), function (result) {
						console.log(result);
					});
				}
			}
		});
		
	}
		
});
