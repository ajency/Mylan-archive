angular.module 'PatientApp.dashboard'

.controller 'StartQuestionnaireCtrl',['$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', '$stateParams'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI, $stateParams)->

		$scope.view =
			startQuiz :() ->
				console.log $stateParams.responseId
				App.navigate 'questionnaire', respStatus: $stateParams.responseId
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'start-questionnaire',
			url: '/start-questionnaire:responseId'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/dashboard/start-questionnaire.html'
					controller: 'StartQuestionnaireCtrl'
					

]

			
