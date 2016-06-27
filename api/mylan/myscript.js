$(document).ready(function(e){
	chrome.storage.local.get(null, function(items) {
		var allKeys = Object.keys(items);
		$.each( allKeys, function( index, value ){
			var myVar = "";
			myVar = value;
			chrome.storage.local.get( myVar, function (result) {
				//$(".patient-refer"+result[myVar].referrence_code).html(result[myVar].name);
				allKeys = Object.keys(result[myVar].projectIdObj);
				for(var i=0;i<allKeys.length;i++){
					objectsData = result[myVar].projectIdObj[allKeys[i]];//gets the reference value
					for(var key in objectsData) {
						 $(".patient-refer"+objectsData[key].referrence_codes).html(objectsData[key].name);
					}
				}
			});
		});
	});
	var selectboxData = $(".patient-search span.filter-option.pull-left").html();
	$(".patient-search span.filter-option.pull-left").addClass("patient-refer"+selectboxData);
});


/*
$(document).ready(function(e){
	chrome.storage.local.get(null, function(items) {
		var allKeys = Object.keys(items);
		$.each( allKeys, function( index, value ){
			var myVar = "";
			myVar = value;
			chrome.storage.local.get( myVar, function (result) {
				$(".patient-refer"+result[myVar].referrence_code).html(result[myVar].name);
			});
		});
	});
	var selectboxData = $(".patient-search span.filter-option.pull-left").html();
	$(".patient-search span.filter-option.pull-left").addClass("patient-refer"+selectboxData);
});
*/
	
