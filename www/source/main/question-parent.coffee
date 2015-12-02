angular.module 'PatientApp.main'

.controller 'ParentCtr',['$scope', 'App', '$ionicLoading', 'Storage'
	, ($scope, App, $ionicLoading, Storage)->

		$scope.view =
			onBackClick : ->
				count = -1
				App.goBack count

			pause : ->
				#@openLoadingPopup('views/main/cancel.html') use single method

				$ionicLoading.show
					scope: $scope
					templateUrl:'views/main/pause.html'
					hideOnStateChange: true	


			close : ->
				# @openLoadingPopup('views/main/cancel.html') use single method

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
				
			#use this single function as popup
			# openLoadingPopup : (templateUrl, options={})->
			# 	$ionicLoading.show
			# 		scope: $scope
			# 		templateUrl: templateUrl
			# 		hideOnStateChange: if !_.isUndefined(options.hideOnStateChange) then options.hideOnStateChange else true
]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'parent-questionnaire',
		url: '/parent-questionnaire'
		abstract: true
		templateUrl: 'views/main/question-parent.html'
		controller: 'ParentCtr'

]
