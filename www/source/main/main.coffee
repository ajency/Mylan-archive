angular.module 'PatientApp.main', []

.controller 'MainCtr',['$scope', 'App', 'Storage', 'QuestionAPI', '$ionicLoading'
	, ($scope, App, Storage, QuestionAPI, $ionicLoading)->

		$scope.view =

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
				# LoadingPopup.showLoadingPopup 'views/main/pause.html' use single method

				$ionicLoading.show
					scope: $scope
					templateUrl:'views/main/pause.html'
					hideOnStateChange: true	

			exitApp : ->
				ionic.Platform.exitApp()

			closePopup : ->
				$ionicLoading.hide()

			


]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'main',
		url: '/main'
		abstract: true
		templateUrl: 'views/main.html'
		controller: 'MainCtr'

]
