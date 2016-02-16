angular.module 'PatientApp.notification'

.factory 'notifyAPI', ['$q', '$http', 'App', ($q, $http, App, $stateParams)->

	notifyAPI = {}

	notifyAPI.getNotification = (param)->

		defer = $q.defer()		
		App.SendParseRequest('getPatientNotifications', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	notifyAPI.setNotificationSeen = (param)->
		defer = $q.defer()		
		App.SendParseRequest('hasSeenNotification', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	notifyAPI.getNotificationCount = (param)->

		defer = $q.defer()		
		App.SendParseRequest('getPatientNotificationCount', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	notifyAPI.deleteAllNotification = (param)->

		defer = $q.defer()		
		App.SendParseRequest('clearAllNotifications', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise

	notifyAPI.deleteNotification = (param)->

		defer = $q.defer()		
		App.SendParseRequest('clearNotification', param)
		.then (data)->
			defer.resolve data
		, (error)=>
			defer.reject error
			
		defer.promise


	
	notifyAPI

]