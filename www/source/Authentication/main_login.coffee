angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->

		$scope.view =

			mainlogin : ->
				# Storage.setup 'set'
				# .then ->
					App.navigate "questionnaire", {}, {animate: false, back: false}

			
]
