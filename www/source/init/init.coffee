
angular.module 'PatientApp.init', []

.controller 'InitCtrl', ['Storage','App','$scope', 'QuestionAPI','$q', '$rootScope', 'Push'
	, (Storage, App, $scope, QuestionAPI, $q, $rootScope, Push) ->

		$rootScope.$on '$cordovaPush:notificationReceived', (e, p)->
			console.log 'notification received'
			payload = Push.getPayload p
			Push.handlePayload(payload) if !_.isEmpty(payload)
  
		Storage.login('get')
		.then (value) ->

			if _.isNull(value)
				App.navigate 'setup', {}, {animate: false, back: false}
			else 
				App.navigate 'dashboard', {}, {animate: false, back: false}
				# App.navigate 'start-questionnaire'

				# Storage.login('get').then (value) ->
				# 	if _.isNull(value)
				# 		App.navigate 'main_login', {}, {animate: false, back: false}
				# 	else 
				# 		Storage.quizDetails('get').then (quizDetail) ->
				# 			if _.isNull(quizDetail)
				# 				App.navigate 'dashboard', {}, {animate: false, back: false}
				# 			else 
				# 				console.log 'inside else'
				# 				QuestionAPI.checkDueQuest quizDetail.quizID
				# 				.then (data)=>
				# 					if data == 'paused'
				# 						App.navigate 'questionnaire', quizID:quizDetail.quizID, {animate: false, back: false}
				# 					else
				# 						App.navigate 'dashboard', {}, {animate: false, back: false}
				# 				, (error)=>
				# 					console.log 'err'

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
					resolve:
						HospitalData :($q, Storage)->
							defer = $q.defer()
							Storage.hospital_data 'get'
							.then (data)->
								defer.resolve data
							defer.promise
							
						RefcodeData :($q, Storage)->
							defer = $q.defer()
							Storage.setData 'refcode', 'get'
							.then (data)->
								defer.resolve data
							defer.promise

		.state 'main_login',
			url: '/main_login'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/authentication-view/Main-Screen-login.html'
					controller: 'main_loginCtr'
					resolve:
						RefcodeData :($q, Storage)->
							defer = $q.defer()
							Storage.setData 'refcode', 'get'
							.then (data)->
								defer.resolve data
							defer.promise

			

		.state 'setup',
			url: '/setup'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/authentication-view/main-screen.html'
					controller: 'setupCtr'

		.state 'reset_password',
			url: '/reset_password'
			parent: 'main'
			views: 
				"appContent":
					templateUrl: 'views/authentication-view/reset-password.html'
					controller: 'setup_passwordCtr'
					resolve:
						HospitalData :($q, Storage)->
							defer = $q.defer()
							Storage.setData 'hospital_details', 'get'
							.then (data)->
								defer.resolve data
							defer.promise

						RefcodeData :($q, Storage)->
							defer = $q.defer()
							Storage.setData 'refcode', 'get'
							.then (data)->
								defer.resolve data
							defer.promise


		
	
	# $urlRouterProvider.otherwise '/setup'
]
