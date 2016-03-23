angular.module 'angularApp.notification',[]

.controller 'notifyCtrl',['$scope', 'App', '$routeParams', 'notifyAPI', '$location', '$rootScope'
	, ($scope, App, $routeParams, notifyAPI, $location, $rootScope)->

		$scope.view =
			data : []
			display : 'loader'
			page : 0
			noNotification : null
			limit : 10
			gotAllRequests: false
			email : hospitalEmail
			phone : hospitalPhone

			init :() ->
				
				param =
					"patientId" : RefCode
					"page" : @page
					"limit": @limit

				notifyAPI.getNotification param
				.then (data)=>	
					@display = 'noError'
					dataSize = _.size data
					if dataSize > 0
						if dataSize < @limit
							@canLoadMore = false
						else
							@canLoadMore = true
					else
						@canLoadMore = false

					@gotAllRequests = true if !@canLoadMore
				
					@data = @data.concat data
					_.each @data, (value)->
						value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('DD-MM-YYYY hh:mm A')
						value['graceDateDisplay'] = moment(value.graceDate).format('DD-MM-YYYY hh:mm A')

				,(error)=>
					@display = 'error'
					@errorType = error

				.finally =>
					@page = @page + 1

				$rootScope.$broadcast 'notification:count'
			
			deleteNotify:(id)->
				param = 
					"notificationId":id

				notifyAPI.deleteNotification param
				.then (data)=>
					idObject = _.findWhere(@data, {id: id}) 
					if idObject.hasSeen == false 
						$rootScope.$broadcast 'decrement:notification:count'

					spliceIndex = _.findIndex $scope.view.data, (request)->
						request.id is id
					$scope.view.data.splice(spliceIndex, 1) if spliceIndex isnt -1

					if @data.length < 5
						@init()
				,(error)->
					console.log 'error data'
				


				# App.notification.decrement()

			seenNotify:(id)->		
				param = 
					"notificationId":id

				notifyAPI.setNotificationSeen param
				.then (data)->
					console.log data
				,(error)->
					console.log 'error data'
				$location.path('dashboard')

			onTapToRetry : ->
				@display = 'loader'
				@gotAllRequests = false
				@page = 0
				@init()

			deleteNotifcation:(id)->
			

			showMore :()->
				@init()

			DeleteAll:()->
				# param = 
				# 	"patientId": RefCode 

				objIds = _.pluck(@data, 'id')  
				param = 
				 	"notificationIds": objIds

				@display = 'loader' 	
				notifyAPI.deleteAllNotification param
				.then (data)=>
					@data = []
					@page = 0
					@canLoadMore = false
					@init()
					console.log 'sucess notification seen data'
					console.log data
				,(error)->
					console.log 'error data'






]