angular.module 'PatientApp.init'


.controller 'setupCtr', ['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->




		$scope.view =

			verifyRefCode : ->
				Storage.setup 'set'
				.then ->
					App.navigate "setup_password", {}, {animate: false, back: false}

			tologin : ->
				
					App.navigate "main_login", {}, {animate: false, back: false}




]

