angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI','HospitalData'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData)->

		$scope.view =
			hospitalName: HospitalData.name
			projectName : HospitalData.project
			SubmissionData : []

			init :() ->
				Storage.getNextQuestion 'set' , 1
				# value = Storage.setHospitalData 'get'
				# @hospitalName = value['name']
				# @projectName = value['project']

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
					resolve:
						HospitalData :($q, Storage)->
							defer = $q.defer()
							Storage.hospital_data 'get'
							.then (data)->
								defer.resolve data
							defer.promise

]

			
