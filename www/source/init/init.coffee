
angular.module 'PatientApp.init', []

.controller 'InitCtrl', ['Storage','App','$scope', (Storage,App,$scope)->
  
	Storage.setup('get').then (value) ->
		console.log '---------'
		console.log value
		if _.isNull(value)
			console.log 'inside if'
			goto = 'setup'
			App.navigate goto
		else 
			console.log 'iee'
			Storage.login('get').then (value) ->
				goto = if _.isNull(value) then 'main_login' else 'questionnaire'
				App.navigate goto
 	# if _.isNull(value)
  #   return goto = 'setup'
  # 	else
  #   Storage.login('get').then (value) ->
  #     goto = if _.isNull(value) then 'main_login' else 'questionnaire'
  

	# App.navigate goto

  					
				

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
			templateUrl: 'views/authentication-view/Main-Screen-login.html'
			controller: 'main_loginCtr'

		
	
	# $urlRouterProvider.otherwise '/setup'
]
