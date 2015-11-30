angular.module 'PatientApp.main', []

.controller 'MainCtr',['$scope', 'App', 'Storage', 'QuestionAPI'
	, ($scope, App, Storage, QuestionAPI)->

		$scope.view =

			onBackClick : ->
				count = -1
				App.goBack count

			resetPassword : ->
				console.log 's'
				App.navigate 'reset_password'


]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'main',
		url: '/main'
		abstract: true
		templateUrl: 'views/main.html'
		controller: 'MainCtr'

]
