angular.module 'PatientApp.notification',[]

.controller 'contactCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI)->

		$scope.view =
			startQuiz :(quizID) ->
				App.navigate 'questionnaire'
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'notification',
			url: '/notification'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/notification/notification.html'
					controller: 'contactCtrl'
					

]

			
