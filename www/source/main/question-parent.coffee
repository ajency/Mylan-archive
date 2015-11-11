angular.module 'PatientApp.main'

.controller 'ParentCtr',['$scope', 'App'
	, ($scope, App)->

		$scope.view =
			onBackClick : ->
				count = -1
				App.goBack count

]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'parent-questionnaire',
		url: '/parent-questionnaire'
		abstract: true
		templateUrl: 'views/main/question-parent.html'
		controller: 'ParentCtr'

]
