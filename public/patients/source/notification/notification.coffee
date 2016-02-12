angular.module 'angularApp.notification',[]

.controller 'notifyCtrl',['$scope', 'App', '$routeParams', 'notifyAPI', '$location'
	, ($scope, App, $routeParams, notifyAPI, $location)->

		$scope.view =
			data : []
			display : 'loader'

			init :() ->
				console.log 'inside notification controller'
			
				param =
					"patientId" : RefCode
					"page" : 1
					"limit": 10

				console.log '**** notification coffeee ******'
				console.log param

				notifyAPI.getNotification param
				.then (data)=>	
					console.log 'notification data'
					console.log data
					@display = 'noError'
					@data = []
					@data = data
					_.each @data, (value)->
						value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('MMMM Do YYYY')
						value['graceDateDisplay'] = moment(value.graceDate).format('MMMM Do YYYY')

				,(error)=>
					@display = 'error'
					@errorType = error
			
			deleteNotify:(id)->
				console.log '***1deleteNotifcation****'
				console.log id 
				param = 
					"notificationId":id

				notifyAPI.deleteNotification param
				.then (data)->
					console.log 'sucess notification seen data'
					console.log data
					spliceIndex = _.findIndex $scope.view.data, (request)->
						request.id is id
					console.log 'spliceeIndexx'
					console.log spliceIndex 
					$scope.view.data.splice(spliceIndex, 1) if spliceIndex isnt -1
					

				,(error)->
					console.log 'error data'
				


				# App.notification.decrement()

			seenNotify:(id)->
				console.log '***seenNotifcation****'
				console.log id 
				
				param = 
					"notificationId":id

				notifyAPI.setNotificationSeen param
				.then (data)->
					console.log 'sucess notification seen data'
					console.log data
				,(error)->
					console.log 'error data'
				$location.path('dashboard')

			onTapToRetry : ->
				@display = 'loader'
				@init()

			deleteNotifcation:(id)->
				console.log '***deleteNotifcation****'
				console.log id 

]