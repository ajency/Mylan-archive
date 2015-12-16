angular.module 'PatientApp.dashboard'

.controller 'StartQuestionnaireCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI)->

		$scope.view =
			startQuiz :(quizID) ->
				App.navigate 'questionnaire', respStatus:'noValue'
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'start-questionnaire',
			url: '/start-questionnaire'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/dashboard/start-questionnaire.html'
					controller: 'StartQuestionnaireCtrl'
					

]

			
