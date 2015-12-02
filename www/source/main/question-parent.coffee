angular.module 'PatientApp.main'

.controller 'ParentCtr',['$scope', 'App', '$ionicLoading', 'Storage'
	, ($scope, App, $ionicLoading, Storage)->

		$scope.view =
			onBackClick : ->
				count = -1
				App.goBack count

			pause : ->
				# LoadingPopup.showLoadingPopup 'views/main/pause.html' use single method

				$ionicLoading.show
					scope: $scope
					templateUrl:'views/main/pause.html'
					hideOnStateChange: true	


			close : ->
				# LoadingPopup.showLoadingPopup 'views/main/cancel.html'

				$ionicLoading.show
					scope: $scope
					templateUrl:'views/main/cancel.html'
					hideOnStateChange: true	

			closePopup : ->
				$ionicLoading.hide()


			cancelQuiz : ->
				$ionicLoading.hide()
				Storage.quizDetails('remove')
				App.navigate 'dashboard', {}, {animate: false, back: false}

			exitApp : ->
				ionic.Platform.exitApp()
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'parent-questionnaire',
		url: '/parent-questionnaire'
		abstract: true
		templateUrl: 'views/main/question-parent.html'
		controller: 'ParentCtr'

]
