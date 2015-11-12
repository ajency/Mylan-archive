angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI'
	, ($scope, App, Storage, QuestionAPI)->

		$scope.view =
			Hospital_name: 'Sutter Davis Hospital'
			SubmissionData : []

			startQuiz : ->
				Storage.quizDetails 'set' , quizID: '1111' 
				App.navigate 'questionnaire', quizID: '1111'

			getSubmission : ->
				DashboardAPI.get()


			displaydata : ->
				@data = @getSubmission()	
	
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'dashboard',
			url: '/dashboard'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/dashboard/dashboard.html'
					controller: 'DashboardCtrl'

]

			
