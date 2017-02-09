angular.module 'PatientApp.Global'


.factory 'Push', ['App', '$rootScope','PushConfig','$q'
	, (App, $rootScope,PushConfig,$q)->

		Push = {}

		Push.register = ->
			console.log "PUSH REG"
			if ionic.Platform.isWebView()
				push = PushNotification.init PushConfig
				push.on 'notification' , (data) ->
					console.log 'notification received',data
					payload = Push.getPayload data
					Push.handlePayload(payload) if !_.isEmpty(payload)

		Push.getPayload = (p)->
			console.log p
			payload = {}
			payload = p.additionalData

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
