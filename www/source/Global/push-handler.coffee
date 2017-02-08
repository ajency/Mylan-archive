angular.module 'PatientApp.Global'


.factory 'Push', ['App', '$rootScope','PushConfig'
	, (App, $rootScope,PushConfig)->

		Push = {}

		Push.register = ->
			androidConfig = "senderID": "DUMMY_SENDER_ID"
			iosConfig     = "badge": true, "sound": true, "alert": true

			PushPlugin = PushNotification.init PushConfig

			PushPlugin.on 'notification' , (data) ->
				console.log 'notification received',data
				payload = Push.getPayload data
				Push.handlePayload(payload) if !_.isEmpty(payload)

		Push.getPayload = (p)->
			console.log p
			payload = {}
			if App.isAndroid()
				payload = p.additionalData

			if App.isIOS()
				payload = p
				foreground = if p.foreground is "1" then true else false
				payload.foreground = foreground

			payload

		Push.handlePayload = (payload)->
			console.log payload, 'Handle PayLoad'
			inAppNotification = ->
				console.log 'inApp '
				$rootScope.$broadcast 'in:app:notification', payload: payload

			notificationClick = ->
				console.log 'notification '
				$rootScope.$broadcast 'push:notification:click', payload: payload

			if App.isAndroid()
				if payload.coldstart
					notificationClick()
				else if !payload.foreground and !payload.coldstart
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
