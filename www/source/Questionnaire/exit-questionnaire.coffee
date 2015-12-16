angular.module 'PatientApp.Quest'

.controller 'ExitQuestionnaireCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI)->

		$scope.view =
			exit :()->
				ionic.Platform.exitApp()
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'exit-questionnaire',
			url: '/exit-questionnaire'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/questionnaire/exit.html'
					controller: 'ExitQuestionnaireCtrl'
					

]

			
