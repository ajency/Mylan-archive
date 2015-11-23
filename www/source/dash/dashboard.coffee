angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI'
	, ($scope, App, Storage, QuestionAPI,DashboardAPI)->

		$scope.view =
			Hospital_name: 'Sutter Davis Hospital'
			SubmissionData : []

			startQuiz :(quizID) ->
				Storage.getNextQuestion 'set' , 1

				Storage.quizDetails 'set' , quizID: quizID 
				App.navigate 'questionnaire', quizID: quizID

			getSubmission : ->
				DashboardAPI.get()


			displaydata : ->
				@data = @getSubmission()	
				console.log @data 
	
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

			
