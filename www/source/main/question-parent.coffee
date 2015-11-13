angular.module 'PatientApp.main'

.controller 'ParentCtr',['$scope', 'App', '$ionicLoading'
	, ($scope, App, $ionicLoading)->

		$scope.view =
			onBackClick : ->
				count = -1
				App.goBack count

			pause : ->
				$ionicLoading.show
					scope: $scope
					templateUrl:'views/main/pause.html'
					hideOnStateChange: true	


			close : ->
				$ionicLoading.show
					scope: $scope
					templateUrl:'views/main/cancel.html'
					hideOnStateChange: true	




]

.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'parent-questionnaire',
		url: '/parent-questionnaire'
		abstract: true
		templateUrl: 'views/main/question-parent.html'
		controller: 'ParentCtr'

]
