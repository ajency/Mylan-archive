angular.module 'PatientApp.notification',[]

.controller 'notifyCtrl',['$scope', 'App', 'Storage', 'notifyAPI', '$rootScope'
	, ($scope, App, Storage, notifyAPI, $rootScope)->

		$scope.view =
			data : []
			display : 'loader'
			page : 0
			limit : 10
			refcode : ''
			canLoadMore : true
			refresh: false

			init :() ->
				
				param =
					"patientId" : @refcode
					"page" : @page
					"limit" : @limit

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

					if @refresh then @data = data
					else @data = @data.concat data

					
					_.each @data, (value)->
						value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('MMMM Do YYYY')
						value['graceDateDisplay'] = moment(value.graceDate).format('MMMM Do YYYY')
					@onScrollComplete()	
				, (error)=>
					@display = 'error'
					@errorType = error
				.finally =>
					@page = @page + 1
					$scope.$broadcast 'scroll.refreshComplete'
					$scope.$broadcast 'scroll.infiniteScrollComplete'

				

			seenNotify:(id)->
				App.navigate 'dashboard', {}, {animate: false, back: false}
				param = 
					"notificationId":id
				notifyAPI.setNotificationSeen param
				.then (data)->
					console.log 'sucess data'
					console.log data
				,(error)->
					console.log 'error data'

				idObject = _.findWhere(@data, {id: id}) 
				if idObject.hasSeen == false 
					App.notification.decrement()

			onTapToRetry : ->
				@display = 'loader'
				@init()


			onInfiniteScroll : ->
				@refresh = false

				Storage.setData 'refcode','get'
				.then (refcode)=>
					@refcode = refcode

					console.log 'iii'
					@init()

			onScrollComplete : ->
				$scope.$broadcast 'scroll.infiniteScrollComplete'

			DeleteAll:()->
				param = 
					"patientId": @refcode 
				notifyAPI.deleteAllNotification param
				.then (data)=>
					console.log 'sucess notification seen data'
					console.log data
					@data = []
					App.notification.count = 0
					App.notification.badge = false
				,(error)->
					console.log 'error data'
					if error == 'offline'
							CToast.show 'Check net connection'
						else if error == 'server_error'
							CToast.showLongBottom 'Error in clearing Notification ,Server error'
						else
							CToast.showLongBottom 'Error in clearing Notification ,Server error'


			onPullToRefresh : ->
				@page = 0
				@refresh = true
				@canLoadMore = false
				@init()

			deleteNotify:(id)->
				param = 
					"notificationId":id

				notifyAPI.deleteNotification param
				.then (data)=>
					spliceIndex = _.findIndex $scope.view.data, (request)->
						request.id is id
					$scope.view.data.splice(spliceIndex, 1) if spliceIndex isnt -1

				idObject = _.findWhere(@data, {id: id}) 
				if idObject.hasSeen == false 
					App.notification.decrement()

			getNotificationCount:()->
				$rootScope.$broadcast 'notification:count:update'
					

]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'notification',
			url: '/notification'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/notification/notification.html'
					controller: 'notifyCtrl'
					

]

			
