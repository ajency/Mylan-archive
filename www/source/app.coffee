
angular.module 'PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage'
			,'PatientApp.Global','PatientApp.Auth','PatientApp.Quest'
			, 'PatientApp.main', 'PatientApp.dashboard', 'PatientApp.contact', 'PatientApp.notification', 'PatientApp.notificationCount']




.run ['$rootScope', 'App', 'User', '$timeout', ($rootScope, App, User, $timeout)->

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


	$rootScope.$on '$stateChangeSuccess', (ev, to, toParams, from, fromParams)->
		App.previousState = from.name
		App.currentState  = to.name

		hideForStates = ['reset_password', 'setup_password', 'main_login','questionnaire', 'summary']
		bool = !_.contains(hideForStates, App.currentState)
		App.menuButtonEnabled = bool

		App.questinnarieButton =  if App.currentState is 'questionnaire'  then true else false

]

.config ['$stateProvider', ($stateProvider)->


	
]