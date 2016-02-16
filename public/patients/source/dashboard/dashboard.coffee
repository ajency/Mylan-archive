angular.module 'angularApp.dashboard',[]

.controller 'dashboardController', ['$scope', 'DashboardAPI', '$location', 'Storage'
	, ($scope, DashboardAPI, $location, Storage)->
	
		$scope.view =
			data : []
			display : 'loader'
			QuestinnarieName : questionnaireName
			showMoreButton : true
			limitTo: 5

			init :() -> 

				@display = 'loader'
				id = RefCode
				param = 
					"patientId": id

				DashboardAPI.get(param)
				.then (data)=>
					@data = data
					@display = 'noError'
					if @data.length < 6
						@showMoreButton = false
				,(error)=>
					@display = 'error'
					@errorType = error

			summary : (id)->
				summaryData = 
					previousState : 'dashboard'
					responseId : id

				Storage.summary 'set', summaryData
				$location.path('summary')

			startQuiz :() ->
				$location.path 'start-questionnaire'

			resumeQuiz : (id)->

				questionnaireData = 
					respStatus : 'resume'
					responseId : id

				Storage.questionnaire 'set', questionnaireData

				$location.path 'questionnaire'

			onTapToRetry : ->
					@display = 'loader'
					console.log 'onTapToRetry'
					@init()

			showMore : ->
				@limitTo = @limitTo + 5
				if @data.length < @limitTo 
					@showMoreButton = false

				
]

.controller 'EachRequestTimeCtrl', ['$scope', ($scope)->
	setTime = ->
		$scope.submissions.yr =  moment($scope.submissions.occurrenceDate).format('YYYY')
		$scope.submissions.month =  moment($scope.submissions.occurrenceDate).format('MMM')
		$scope.submissions.date =  moment($scope.submissions.occurrenceDate).format('Do')
	setTime()
]

.controller 'headerCtrl', ['$scope', '$location', ($scope, $location)->
	console.log 'header ctrl'
	$scope.notifyIocn = '23'
		
]
