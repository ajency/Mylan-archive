angular.module 'PatientApp.dashboard',[]

.controller 'DashboardCtrl',['$scope', 'App', 'Storage', 'QuestionAPI','DashboardAPI','HospitalData'
	, ($scope, App, Storage, QuestionAPI, DashboardAPI, HospitalData)->

		$scope.view =
			hospitalName: HospitalData.name
			projectName : HospitalData.project
			SubmissionData : []
			data : []
			display : 'loader'

			init :() ->
				Storage.getNextQuestion 'set' , 1
				# value = Storage.setHospitalData 'get'
				# @hospitalName = value['name']
				# @projectName = value['project']

			startQuiz :() ->
				# App.navigate 'questionnaire', quizID: quizID
				App.navigate 'start-questionnaire'

			getSubmission : ->
				@display = 'loader'
				Storage.setData 'refcode','get'
				.then (refcode)=>
					param = 
						"patientId":refcode
					DashboardAPI.get param
					.then (data)=>
						console.log 'inside then'
						console.log data
						@data = data
						@display = 'noError'
					,(error)=>
						@display = 'error'
						@errorType = error



			displaydata : ->
				@getSubmission()	
				

			summary : (id)->
				console.log '---summary---id'
				console.log id
				App.navigate 'summary', summary:id

			resumeQuiz : (id)->
				console.log 'resumeQuiz'
				console.log id
				App.navigate 'questionnaire', respStatus:id

			onTapToRetry : ->
				@display = 'loader'
				console.log 'onTapToRetry'
				@getSubmission()

			pastDate: (date) ->
				moment(date).format('MMMM Do YYYY')


	
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

			
