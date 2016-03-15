angular.module 'PatientApp.notification',[]

.controller 'notifyCtrl',['$scope', 'App', 'Storage', 'notifyAPI', '$rootScope', 'NotifyCount', 'CSpinner'
	, ($scope, App, Storage, notifyAPI, $rootScope, NotifyCount, CSpinner)->

		$scope.view =
			data : []
			display : 'noError'
			page : 0
			limit : 10
			refcode : ''
			canLoadMore : true
			refresh: false
			gotAllRequests: false

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

						if @refresh then @data = data
						else @data = @data.concat data
					else
						@canLoadMore = false

					@gotAllRequests = true if !@canLoadMore

					_.each @data, (value)->
						value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('MMMM Do YYYY')
						value['graceDateDisplay'] = moment(value.graceDate).format('MMMM Do YYYY')
					@onScrollComplete()	
				, (error)=>
					@data = []
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
				,(error)->
					console.log 'error data'

				idObject = _.findWhere(@data, {id: id}) 
				if idObject.hasSeen == false 
					App.notification.decrement()

			onTapToRetry : ->
				@gotAllRequests = false
				@page = 0
				@display = 'noError'

			onInfiniteScroll : ->
				@refresh = false
				Storage.setData 'refcode','get'
				.then (refcode)=>
					@refcode = refcode
					@init()

			onScrollComplete : ->
				$scope.$broadcast 'scroll.infiniteScrollComplete'

			DeleteAll:()->
				# param = 
				# 	"patientId": @refcode 
				CSpinner.show '', 'Please wait..'
				objIds = _.pluck(@data, 'id')  
				param = 
				 	"notificationIds": objIds

				notifyAPI.deleteAllNotification param
				.then (data)=>
					App.notification.count = App.notification.count - objIds.length
					App.notification.badge = false if App.notification.count <= 0
					# @badge = false if @count <= 0
					@data = []
					App.scrollTop()
					App.resize()
					@page = 0
					@canLoadMore = true
					@display = 'loader'
					@init()
					# App.notification.count = 0
					# App.notification.badge = false
				,(error)->
					if error == 'offline'
							CToast.show 'Check net connection'
						else if error == 'server_error'
							CToast.showLongBottom 'Error in clearing Notification ,Server error'
						else
							CToast.showLongBottom 'Error in clearing Notification ,Server error'
				.finally ->
					CSpinner.hide()

			onPullToRefresh : ->
				@gotAllRequests = false
				NotifyCount.getCount(@refcode)
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

			autoFetch : ->
				@gotAllRequests = false
				@page = 0
				@refresh = true
				@canLoadMore = false
				@init()


		$scope.$on '$ionicView.enter', ->
			Storage.setData 'refcode','get'
				.then (refcode)->
					NotifyCount.getCount(refcode)

		$rootScope.$on 'in:app:notification', (e, obj)->
			$scope.view.autoFetch()

					
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

			
