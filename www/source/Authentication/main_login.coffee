angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->

		$scope.view =

			mainlogin : ->
				Storage.login 'set'
				.then ->
				
					App.navigate "questionnaire"
					

			
]
