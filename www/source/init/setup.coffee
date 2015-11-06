angular.module 'PatientApp.init'


.controller 'setupCtr', ['$scope', 'App', 'Storage','$ionicLoading'
	, ($scope, App, Storage,$ionicLoading)->




		$scope.view =
			refcode:''


			verifyRefCode : ->
					console.log @refcode
					App.navigate "setup_password"

			tologin : ->
					Storage.setup 'get'
					.then (value)->
						goto = if _.isNull value then "setup" else "main_login"
						App.navigate goto

			forgetRefcode:->
					$ionicLoading.show
						scope: $scope
						templateUrl:'views/error-view/Error-Screen-2.html'
						hideOnStateChange: true			
			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false			
				

				
]
	
