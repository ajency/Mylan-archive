angular.module 'PatientApp.Global'

.factory 'LoadingPopup', ['$ionicLoading', ($ionicLoading)->
	LoadingPopup = 
		showLoadingPopup : (templateUrl)->
			$ionicLoading.show
				scope: $scope
				templateUrl : templateUrl
				hideOnStateChange: true	
		hidePopup : ()->
			$ionicLoading.hide()
	LoadingPopup

]