angular.module 'PatientApp.notification'

.factory 'notifyAPI', ['$q', '$http', 'App', ($q, $http, App, $stateParams)->

	notifyAPI = {}

	notifyAPI.getNotification = (param)->

		defer = $q.defer()		
		App.SendParseRequest('getAllNotifications', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	notifyAPI.setNotificationSeen = (param)->

		defer = $q.defer()		
		App.SendParseRequest('getAllNotifications', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	
	notifyAPI

]