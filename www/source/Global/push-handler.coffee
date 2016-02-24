angular.module 'PatientApp.Global'


.factory 'Push', ['App', '$cordovaPush', '$rootScope'
	, (App, $cordovaPush, $rootScope)->

		Push = {}

		Push.register = ->
			androidConfig = "senderID": "DUMMY_SENDER_ID"
			iosConfig     = "badge": true, "sound": true, "alert": true

			if App.isWebView()
				config = if App.isIOS() then iosConfig else androidConfig

				$cordovaPush.register config
				.then (success)->
					console.log 'Push Registration Success'
				, (error)->
					console.log 'Push Registration Error'

		Push.getPayload = (p)->
			console.log p
			payload = {}
			if App.isAndroid()
				if p.event is 'message'
					payload = p.payload.data
					payload.foreground = p.foreground
					payload.coldstart = p.coldstart if _.has(p, 'coldstart')

			if App.isIOS()
				payload = p
				foreground = if p.foreground is "1" then true else false
				payload.foreground = foreground

			payload

		Push.handlePayload = (payload)->

			inAppNotification = ->
				console.log 'inApp '
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