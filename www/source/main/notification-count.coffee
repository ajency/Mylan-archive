angular.module 'PatientApp.notificationCount', []

.factory 'NotifyCount', ['notifyAPI', 'App', (notifyAPI, App) ->


  NotifyCount = {}

  NotifyCount.getCount = (refcode)->
  	param =
  		"patientId" : refcode

  	notifyAPI.getNotificationCount(param).then (data) ->
    if data > 0
      App.notification.count = data
      App.notification.badge = true
    else
      App.notification.badge = false
      App.notification.count = 0
      



  NotifyCount


]