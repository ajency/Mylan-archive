angular.module 'PatientApp.notification',[]

.controller 'notifyCtrl',['$scope', 'App', 'Storage', 'notifyAPI', '$rootScope', 'NotifyCount', 'CSpinner', 'CToast'
	, ($scope, App, Storage, notifyAPI, $rootScope, NotifyCount, CSpinner, CToast)->

		$scope.view =
			data : []
			display : 'noError'
			page : 0
			limit : 20
			refcode : ''
			canLoadMore : true
			refresh: false
			gotAllRequests: false
			disable : false

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
						if @refresh then @data = data
						else @data = @data.concat data

						if dataSize < @limit
							@canLoadMore = false
						else
							@canLoadMore = true

						
					else
						@canLoadMore = false
						@data = []

					@gotAllRequests = true if !@canLoadMore

					_.each @data, (value)->
						value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('DD-MM-YYYY hh:mm A')
						value['graceDateDisplay'] = moment(value.graceDate).format('DD-MM-YYYY hh:mm A')
					@onScrollComplete()	
					@disable = false
				, (error)=>
					@data = []
					@display = 'error'
					@errorType = error
				.finally =>
					@disable = false
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
				@disable = true
				@gotAllRequests = false
				@page = 0
				@canLoadMore = true
				@display = 'loader'
				@refresh = true
				@init()
				NotifyCount.getCount(@refcode)
				

			onInfiniteScroll : ->
				@disable = true
				@refresh = false
				Storage.setData 'refcode','get'
				.then (refcode)=>
					@refcode = refcode
					@init()

			onScrollComplete : ->
				$scope.$broadcast 'scroll.infiniteScrollComplete'

			DeleteAll:()->
				@refresh = true
				@canLoadMore = false
				# param = 
				# 	"patientId": @refcode 
				CSpinner.show '', 'Please wait..'
				objIds = _.pluck(@data, 'id')  
				param = 
				 	"notificationIds": objIds

				notifyAPI.deleteAllNotification param
				.then (data)=>
					App.notification.count = App.notification.count - data.length
					App.notification.badge = false if App.notification.count <= 0
					# @badge = false if @count <= 0
					@refresh = true
					# @data = []
					App.scrollTop()
					App.resize()
					@page = 0
					# @canLoadMore = true
					# @display = 'loader'
					@init()
					# App.notification.count = 0
					# App.notification.badge = false
				,(error)->
					if error == 'offline'
							CToast.show 'Check internet connection'
						else if error == 'server_error'
							CToast.showLongBottom 'Error in clearing Notification ,Server error'
						else
							CToast.showLongBottom 'Error in clearing Notification ,Server error'
				.finally ->
					CSpinner.hide()

			onPullToRefresh : ->
				@disable = true
				@gotAllRequests = false
				NotifyCount.getCount(@refcode)
				@page = 0
				@refresh = true
				@canLoadMore = false
				@init()

			deleteNotify:(id)->
				console.log('deletee notifyy')
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

		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			if !viewData.enableBack
				viewData.enableBack = true	


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

			
