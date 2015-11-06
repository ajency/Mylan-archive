angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->

		$scope.view =

			mainlogin : ->
				
					App.navigate "questionnaire", {}, {animate: false, back: false}
					

			
]
