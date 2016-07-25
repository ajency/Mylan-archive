angular.module 'angularApp.dashboard',[]

.controller 'dashboardController', ['$scope', 'DashboardAPI', '$location', 'Storage', '$rootScope'
	, ($scope, DashboardAPI, $location, Storage, $rootScope)->
	
		$scope.view =
			data : []
			display : 'loader'
			QuestinnarieName : questionnaireName
			showMoreButton : false
			limitTo: 10
			errorStartQuestion : false
			phone : hospitalPhone
			errorMsg : ''
			email : hospitalEmail
    	



			init :() -> 
				if Storage.getQuestStatus('get','questionnarireError') == 'questionnarireError'
					@errorStartQuestion = true
					@errorMsg = 'An error occurred while starting questionnaire. Please try again'
				else if Storage.getQuestStatus('get','questionnarireError') == 'offline'
					@errorStartQuestion = true
					@errorMsg = 'Unable to start questionnaire. Please check your internet connection.'
				else if Storage.getQuestStatus('get','questionnarireError') == 'already_taken'
					@errorStartQuestion = true
					@errorMsg = 'The questionnaire has been already  started.'
				else if Storage.getQuestStatus('get','questionnarireError') != ''
					
					@errorStartQuestion = true
					@errorMsg = Storage.getQuestStatus('get','questionnarireError')



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
					if arr.length < 6
						@showMoreButton = false
					else
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
					@init()

			showMore : ->
				@limitTo = @limitTo + 5
				if @data.length < @limitTo 
					@showMoreButton = false


		$rootScope.$on 'on:session:expiry', ->
			Parse.User.logOut()	
			window.location = Url+"/auth/logout"	

				
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
					param =
	  					"patientId" : RefCode

				  	notifyAPI.getNotificationCount param
				  	.then (data)=>
				  		if data > 0 
				  			@notificationCount = data
				  			@badge = true
				  		else
				  			@notificationCount = 0
				  			@badge = false

				  			

				decrement : ->
					@notificationCount = @notificationCount - 1
					@badge = false if @notificationCount <= 0

				init :->
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
