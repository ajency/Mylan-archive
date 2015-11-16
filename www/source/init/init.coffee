
angular.module 'PatientApp.init', []

.controller 'InitCtrl', ['Storage','App','$scope', 'QuestionAPI','$q', (Storage, App, $scope, QuestionAPI,$q) ->
  
	Storage.setup('get').then (value) ->
		if _.isNull(value)
			App.navigate 'setup'
		else 
			Storage.login('get').then (value) ->
				if _.isNull(value)
					App.navigate 'main_login'
				else 
					Storage.quizDetails('get').then (quizDetail) ->
						if _.isNull(quizDetail)
							App.navigate 'dashboard', {}, {animate: false, back: false}
						else 
							console.log 'inside else'
							QuestionAPI.checkDueQuest quizDetail.quizID
							.then (data)=>
								if data == 'paused'
									App.navigate 'questionnaire', quizDetail.quizID
								else
									App.navigate 'dashboard', {}, {animate: false, back: false}
							, (error)=>
								console.log 'err'

]

.config ['$stateProvider', '$urlRouterProvider', ($stateProvider, $urlRouterProvider)->

	$stateProvider
		
		.state 'init',
			url: '/init'
			cache: false
			controller: 'InitCtrl'					
			templateUrl: 'views/init-view/init.html'

		.state 'setup_password',
			url: '/setup_password'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/authentication-view/Hospital-login.html'
					controller: 'setup_passwordCtr'

		.state 'main_login',
			url: '/main_login'
			templateUrl: 'views/authentication-view/Main-Screen-login.html'
			controller: 'main_loginCtr'
			resolve:
				refrencecodeValue : ($q, Storage)->
					defer = $q.defer()
					Storage.refcode 'get'
					.then (details)->
						defer.resolve details
					defer.promise	

		.state 'setup',
			url: '/setup'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/authentication-view/main-screen.html'
					controller: 'setupCtr'

		
	
	# $urlRouterProvider.otherwise '/setup'
]
