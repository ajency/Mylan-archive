
angular.module 'PatientApp.init', []

.controller 'InitCtrl', ['Storage','App', (Storage,App)->
  
	Storage.setup 'get'
	.then (value)->
		goto = if _.isNull value then "setup" else "main_login"
		App.navigate goto, {}, {animate: false, back: false}
		


]

.config ['$stateProvider', '$urlRouterProvider', ($stateProvider, $urlRouterProvider)->

	$stateProvider
		
		.state 'init',
			url: '/init'
			cache: false
			controller: 'InitCtrl'					
			templateUrl: 'views/init-view/init.html'

		.state 'setup',
			url: '/setup'
			templateUrl: 'views/authentication-view/main-screen.html'
			controller: 'setupCtr'

		.state 'setup_password',
			url: '/setup_password'
			templateUrl: 'views/authentication-view/Hospital-login.html'
			controller: 'setup_passwordCtr'

		.state 'main_login',
			url: '/main_login'
			templateUrl: 'views/authentication-view/main-Screen-login.html'
			controller: 'main_loginCtr'

		
	
	# $urlRouterProvider.otherwise '/setup'
]
