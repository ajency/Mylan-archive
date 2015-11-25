angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI'
	, ($scope, App, Storage, QuestionAPI,DashboardAPI)->

		$scope.view =
			hospitalName: ''
			projectName : ''
			SubmissionData : []

			init :() ->
				Storage.getNextQuestion 'set' , 1
				value = Storage.setHospitalData 'get'
				@hospitalName = value['name']
				@projectName = value['project']

			startQuiz :(quizID) ->
				

				Storage.quizDetails 'set' , quizID: quizID 
				App.navigate 'questionnaire', quizID: quizID

			getSubmission : ->
				DashboardAPI.get()


			displaydata : ->
				@data = @getSubmission()	
				console.log @data 

			summary :->
				App.navigate 'summary', quizID: 111
	
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

			
