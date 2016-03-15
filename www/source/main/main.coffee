angular.module 'PatientApp.main', []

.controller 'MainCtr',['$scope', 'App', 'Storage', 'notifyAPI', '$ionicLoading', 'Push', '$rootScope'
	, ($scope, App, Storage, notifyAPI, $ionicLoading, Push, $rootScope)->

		$scope.view =

			init : ->
				console.log 'inittt...'
				Push.register()
				

			getNotificationCount :->
				Storage.setData 'refcode','get'
				.then (refcode)=>
					param =
						"patientId" : refcode

					notifyAPI.getNotificationCount param
					.then (data)=>	
						console.log 'notificato data'
						console.log data
						if data > 0
							App.notification.count = data
							App.notification.badge = true

			onBackClick : ->
				switch App.currentState
					when 'main_login'
						if App.previousState == 'setup_password'
							App.navigate "setup", {}, {animate: false, back: false}
						else
							count = -1
							App.goBack count
					when 'exit-questionnaire'
						App.navigate "dashboard", {}, {animate: false, back: false}
					else
						count = -1
						App.goBack count

			resetPassword : ->
				App.navigate 'reset_password'

			contact : ->
				App.navigate 'contact'

			update : ->
				App.navigate 'notification'

			pause : ->
				$ionicLoading.show
					scope: $scope
					templateUrl:'views/main/pause.html'
					hideOnStateChange: true	

			exitApp : ->
				ionic.Platform.exitApp()

			closePopup : ->
				$ionicLoading.hide()

		$rootScope.$on 'in:app:notification', (e, obj)->
			if App.notification.count is 0
				$scope.view.getNotificationCount()
			else App.notification.increment()

		# $rootScope.$on 'notification:count:update', (e, obj)->
		# 	console.log 'notificcation count uopdate'
		# 	$scope.view.getNotificationCount()
			
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'main',
		url: '/main'
		abstract: true
		templateUrl: 'views/main.html'
		controller: 'MainCtr'

]
