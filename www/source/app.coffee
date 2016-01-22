
angular.module 'PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage'
			,'PatientApp.Global','PatientApp.Auth','PatientApp.Quest'
			, 'PatientApp.main', 'PatientApp.dashboard', 'PatientApp.contact', 'PatientApp.notification']




.run ['$rootScope', 'App', 'User', '$timeout', ($rootScope, App, User, $timeout)->

	Parse.initialize APP_ID, JS_KEY


	$rootScope.App = App
	App.navigate 'init', {}, {animate: false, back: false}


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