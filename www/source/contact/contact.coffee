angular.module 'PatientApp.contact',[]

.controller 'contactCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI)->

		$scope.view =
			startQuiz :(quizID) ->
				App.navigate 'questionnaire'
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'contact',
			url: '/contact'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/contact/contact.html'
					controller: 'contactCtrl'
					

]

			
