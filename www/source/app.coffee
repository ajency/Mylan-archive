
angular.module 'PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage'
			,'PatientApp.Global','PatientApp.Auth','PatientApp.Quest'
			, 'PatientApp.main', 'PatientApp.dashboard', 'PatientApp.contact', 'PatientApp.notification', 'PatientApp.notificationCount']




.run ['$rootScope', 'App', 'User', '$timeout', '$ionicPlatform','$cordovaPushV5','Push' , ($rootScope, App, User, $timeout, $ionicPlatform,$cordovaPushV5,Push)->

	Parse.initialize APP_ID, JS_KEY


	$rootScope.App = App
	App.navigate 'init', {}, {animate: false, back: false}

	App.notification = 
		badge : false
		count : 0
		
		increment : ->
			@badge = true
			@count = @count + 1
		decrement : ->
			@count = @count - 1
			@badge = false if @count <= 0

	# App.disableNativeScroll()

	$ionicPlatform.ready ->
		options = undefined
		if window.cordova and window.cordova.plugins.Keyboard
			cordova.plugins.Keyboard.hideKeyboardAccessoryBar false
			cordova.plugins.Keyboard.disableScroll true
		Push.register
		ParsePushPlugin.on 'receivePN', (e) ->
			console.log 'notification received', e
			payload = Push.getPayload e
			Push.handlePayload(payload) if !_.isEmpty(e)
		# options =
		# 	android: senderID: 'DUMMY_SENDER_ID'
		# 	ios:
		# 		alert: 'true'
		# 		badge: 'true'
		# 		sound: 'true'
		# 	windows: {}
		# $cordovaPushV5.initialize(options).then ((success) ->
		# 	console.log 'Push Registration Success', success
		# 	$cordovaPushV5.register().then ((deviceToken) ->
		# 		console.log 'Successfully registered', deviceToken
		# 		$cordovaPushV5.onNotification()
		# 		$cordovaPushV5.onError()
		# 	), (error) ->
		# 		console.log 'Failed to registered'
		# 		console.log 'error object : ', error
		# ), (error) ->
		# 	console.log 'Push Registration Error'
		

	$rootScope.$on '$stateChangeSuccess', (ev, to, toParams, from, fromParams)->
		App.previousState = from.name
		App.currentState  = to.name

		hideForStates = ['reset_password', 'setup_password', 'main_login','questionnaire', 'summary']
		bool = !_.contains(hideForStates, App.currentState)
		App.menuButtonEnabled = bool

		App.questinnarieButton =  if App.currentState is 'questionnaire'  then true else false

]

.config ['$ionicConfigProvider', ($ionicConfigProvider)->
	$ionicConfigProvider.views.swipeBackEnabled false
	$ionicConfigProvider.views.forwardCache true
	$ionicConfigProvider.views.transition 'none'


	
]