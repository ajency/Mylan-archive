angular.module 'PatientApp.main'

.controller 'ParentCtr',['$scope', 'App', 'Storage', 'QuestionAPI'
	, ($scope, App, Storage, QuestionAPI)->

		

]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'parent-questionnaire',
		url: '/parent-questionnaire'
		abstract: true
		templateUrl: 'views/main/question-parent.html'
		controller: 'ParentCtr'

]
