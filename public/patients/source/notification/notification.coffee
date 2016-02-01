angular.module 'angularApp.notification',[]

.controller 'notifyCtrl',['$scope', 'App'
	, ($scope, App)->

		$scope.view =
			data : []
			display : 'loader'

			init :() ->
				# Storage.setData 'refcode','get'
				# .then (refcode)=>
				# 	display : 'loader'
				# 	param =
				# 		"patientId" : refcode

				# 	notifyAPI.getNotification param
				# 	.then (data)=>	
				# 		console.log 'notification data'
				# 		console.log data
				# 		@display = 'noError'
				# 		@data = []
				# 		@data = data
				# 		_.each @data, (value)->
				# 			value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('MMMM Do YYYY')
				# 			value['graceDateDisplay'] = moment(value.graceDate).format('MMMM Do YYYY')
				# ,(error)=>
				# 	@display = 'error'
				# 	@errorType = error

			seenNotify:(id)->
				# App.notification.decrement()
				
				# console.log '********'
				# console.log id

				# App.navigate 'dashboard', {}, {animate: false, back: false}

				# param = 
				# 	"notificationId":id

				# notifyAPI.setNotificationSeen param
				# .then (data)->
				# 	console.log 'sucess data'
				# 	console.log data
				# ,(error)->
				# 	console.log 'error data'

			onTapToRetry : ->
				# @display = 'loader'
				# @init()

]