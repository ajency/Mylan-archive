angular.module 'PatientApp.Auth',[]

.controller 'setup_passwordCtr',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->

		$scope.view =

			completesetup : ->
				# Storage.setup 'set'
				# .then ->
					App.navigate "main_login", {}, {animate: false, back: false}




	



]
