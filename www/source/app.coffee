
angular.module 'PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage'
			,'PatientApp.Global','PatientApp.Auth','PatientApp.Quest'
			, 'PatientApp.main', 'PatientApp.dashboard', 'PatientApp.contact', 'PatientApp.notification', 'PatientApp.notificationCount']

.constant 'PushConfig',
    android:
      # senderID: "704918846341"
      senderID: "44338280692"
    ios:
      # senderID: "704918846341"
      senderID: "44338280692"
      gcmSandbox: true
      alert: true
      badge: true
      sound: false


.run ['$rootScope', 'App', 'User', '$timeout', '$ionicPlatform', 'PushConfig' , ($rootScope, App, User, $timeout, $ionicPlatform,PushConfig)->


	Parse.initialize APP_ID
	Parse.serverURL = PARSE_URL

	$rootScope.App = App

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
	  # Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
	  # for form inputs)
		if window.cordova and window.cordova.plugins.Keyboard
			cordova.plugins.Keyboard.hideKeyboardAccessoryBar false
			cordova.plugins.Keyboard.disableScroll true
			
	$rootScope.$on '$stateChangeSuccess', (ev, to, toParams, from, fromParams)->
		App.previousState = from.name
		App.currentState  = to.name

		hideForStates = ['reset_password', 'setup_password', 'main_login','questionnaire', 'summary']
		bool = !_.contains(hideForStates, App.currentState)
		App.menuButtonEnabled = bool

		App.questinnarieButton =  if App.currentState is 'questionnaire'  then true else false
	
	App.navigate 'init', {}, {animate: false, back: false}

]

.config ['$ionicConfigProvider', ($ionicConfigProvider)->
	$ionicConfigProvider.views.swipeBackEnabled false
	$ionicConfigProvider.views.forwardCache true
	$ionicConfigProvider.views.transition 'none'


	
]