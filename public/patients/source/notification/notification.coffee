angular.module 'angularApp.notification',[]

.controller 'notifyCtrl',['$scope', 'App', '$routeParams', 'notifyAPI', '$location'
	, ($scope, App, $routeParams, notifyAPI, $location)->

		$scope.view =
			data : []
			display : 'loader'
			page : 0
			noNotification : null
			limit : 10

			init :() ->
			
				param =
					"patientId" : RefCode
					"page" : @page
					"limit": @limit

				console.log '**** notification coffeee ******'
				console.log param

				notifyAPI.getNotification param
				.then (data)=>	
					console.log 'notification data'
					console.log data
					@display = 'noError'
					dataSize = _.size data
					if dataSize > 0
						if dataSize < @limit
							@canLoadMore = false
						else
							@canLoadMore = true
					else
						@canLoadMore = false

					
				
					@data = @data.concat data
					_.each @data, (value)->
						value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('MMMM Do YYYY')
						value['graceDateDisplay'] = moment(value.graceDate).format('MMMM Do YYYY')

				,(error)=>
					@display = 'error'
					@errorType = error

				.finally =>
					@page = @page + 1
			
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

			showMore :()->
				@init()

			DeleteAll:()->
				param = 
					"patientId": RefCode 

				notifyAPI.deleteAllNotification param
				.then (data)->
					console.log 'sucess notification seen data'
					console.log data
				,(error)->
					console.log 'error data'






]