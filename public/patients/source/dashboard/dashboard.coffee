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
				questionnaireData = {}
				Storage.questionnaire 'set', questionnaireData

				startQuestData = {}
				Storage.startQuestionnaire 'set', startQuestData

				summaryData = {}
				Storage.summary 'set', summaryData

				@display = 'loader'
				id = RefCode
				param = 
					"patientId": id

				DashboardAPI.get(param)
				.then (data)=>
					@data = data
					@display = 'noError'
					arr = _.reject(@data, (d) -> d.status == 'base_line')
					if arr.length <= 6
						@showMoreButton = true
				,(error)=>
					@display = 'error'
					@errorType = error

			summary : (id)->
				summaryData = 
					previousState : 'dashboard'
					responseId : id

				Storage.summary 'set', summaryData
				$location.path('summary')

			startQuiz :(val) ->
				console.log '---startQuiz----'
				console.log val 
				startQuestData = val
				Storage.startQuestionnaire 'set', startQuestData
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

.controller 'headerCtrl', ['$scope', '$location', 'App', 'notifyAPI', '$rootScope'
	, ($scope, $location, App, notifyAPI, $rootScope)->
	
		
		$scope.view =
				notificationCount : 0
				badge : false 

				getNotificationCount :->
					console.log 'inside getNotificationCount'
					param =
	  					"patientId" : RefCode

				  	notifyAPI.getNotificationCount param
				  	.then (data)=>
				  		if data > 0 
				  			@notificationCount = data
				  			@badge = true
				  			

				decrement : ->
					@notificationCount = @notificationCount - 1
					@badge = false if @notificationCount <= 0

				init :->
					console.log 'init'
					@getNotificationCount()

				deleteAllNotification : ->
					@notificationCount = 0
					@badge = false


		$rootScope.$on 'notification:count', ->
			$scope.view.getNotificationCount()

		$rootScope.$on 'delete:all:count', ->
			$scope.view.deleteAllNotification()

		$rootScope.$on 'decrement:notification:count', ->
			$scope.view.decrement()

		
]
