angular.module 'PatientApp.Auth',[]

.controller 'setup_passwordCtr',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->


		  	
		$scope.view =
			New_password:''
			Re_password: ''

			completesetup : ->
				# Storage.setup 'set'
				# .then ->
					console.log @New_password
					console.log @Re_password

					App.navigate "main_login", {}, {animate: false, back: false}




		 



]
