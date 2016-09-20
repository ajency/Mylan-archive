angular.module 'PatientApp.Global'


.factory 'Push', ['App', '$cordovaPushV5', '$rootScope'
	, (App, $cordovaPushV5, $rootScope)->

		Push = {}
		# onNotificationGCM = (e)->
		# 	console.log 'Received notification for Android'
		# 	console.log e
		# 	if e.event is 'message'
		# 		if not e.foreground
		# 			payload = e.payload.data
		# 			data = 
		# 				title: payload.header
		# 				alert: payload.message
		# 				productId: payload.productId
		# 				type: payload.type

		# 			handlePayload data

	
		# onNotificationAPN = (e)->
		# 	console.log 'Received notification for iOS'
		# 	console.log e
		# 	if e.foreground is "0"
		# 		handlePayload e
		Push =
			# androidConfig = "senderID": "DUMMY_SENDER_ID"
			# iosConfig     = "badge": true, "sound": true, "alert": true
			# options = 
			# 	android: senderID: '12345679'
			# 	ios:
			# 		alert: 'true'
			# 		badge: 'true'
			# 		sound: 'true'
			# 	windows: {}
			# if App.isWebView()
				# config = if App.isIOS() then iosConfig else androidConfig
				# $cordovaPushV5.register().then (registrationId) ->
					# console.log registrationId
				# $cordovaPushV5.register
				# .then (success)->
				# 	console.log 'Push Registration Success',success
				# , (error)->
				# 	console.log 'Push Registration Error'
			register : ->
				defer = $.Deferred()
				if window.ParsePushPlugin
					ParsePushPlugin.getInstallationId ((id) ->
						# note that the javascript client has its own installation id,
						# which is different from the device installation id.
						console.log 'device installationId: ' + id
						defer.resolve id
					), (e) ->
						console.log 'error'
						defer.reject e
					defer.promise()
			# defer = $.Deferred()

			# Parse.initialize APP_ID, CLIENT_KEY, ->
			# 	defer.resolve Push.bindPushNotificationEvents()
			# , (e)->
			# 	defer.reject e

			# defer.promise()


			# bindPushNotificationEvents : ->
			# 	@pushNotification = window.ParsePushPlugin

			# 	if App.isAndroid() then @bindGCMEventListener()
			# 	else if App.isIOS() then @bindAPNSEventListener()
			# 	else console.log "Unknown Platform"


			# bindGCMEventListener : ->
			# 	@pushNotification.register (result)->
			# 		console.log 'Android event success',result
			# 	, (error)->
			# 		console.log 'Android event error'

			# 	,{ "senderID":"dummy", "ecb":"onNotificationGCM","forceShow":"true" }


			# bindAPNSEventListener : ->
			# 	@pushNotification.register (result)->
			# 		console.log 'iOS event success'
			# 	, (error)->
			# 		console.log 'iOS event error'
					
			# 	,{ "badge":"true", "sound":"true", "alert":"true", "ecb":"onNotificationAPN" }

		Push.getPayload = (p)->
			console.log p
			payload = {}
			if App.isAndroid()
				console.log 'In android'
				payload = p
				if p.event is 'message'
					payload = p
					payload.foreground = p.foreground
					payload.coldstart = p.coldstart if _.has(p, 'coldstart')

			if App.isIOS()
				console.log 'In IOS'
				payload = p
				foreground = if p.foreground is "1" then true else false
				payload.foreground = foreground

			payload

		Push.handlePayload = (payload)->

			inAppNotification = ->
				console.log 'inApp ',payload
				$rootScope.$broadcast 'in:app:notification', payload: payload

			notificationClick = ->
				console.log 'notification '
				$rootScope.$broadcast 'push:notification:click', payload: payload

			if App.isAndroid()
				if payload.coldstart
					notificationClick()
				else if !payload.foreground and !_.isUndefined(payload.coldstart) and !payload.coldstart
					notificationClick()
				else if payload.foreground
					inAppNotification()
				else if !payload.foreground
					inAppNotification()
			
			else if App.isIOS()
				console.log 'ios'
				console.log '----'
				console.log payload
				console.log '----'
				if payload.foreground
					inAppNotification()
				else if !payload.foreground
					notificationClick()

		Push
]
