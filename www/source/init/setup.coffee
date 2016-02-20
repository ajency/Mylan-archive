angular.module 'PatientApp.init'


.controller 'setupCtr', ['$scope', 'App', 'Storage','$ionicLoading','AuthAPI','CToast', 'CSpinner', 'LoadingPopup'
	, ($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner, LoadingPopup)->
		
		$scope.view =
			refcode:''
			emptyfield:''
			deviceOS:''
			deviceUUID:''

			verifyRefCode : ->
				if @refcode =='' || _.isUndefined(@refcode)
					@emptyfield = "Please Enter Valid Reference Code"	
				else
					@deviceUUID = App.deviceUUID()
					if App.isAndroid() 
					 	@deviceOS = "Android"
					if App.isIOS() 
					 	@deviceOS = "IOS"
					CSpinner.show '', 'Please wait...'  
					AuthAPI.validateRefCode @refcode, @deviceUUID ,@deviceOS
					.then (data)=>
						@data = data
						Storage.setData 'hospital_details', 'set', @data.hospitalData
					.then () =>
						Storage.setData 'refcode', 'set', @refcode
					.then ()=>
						if @data.code == 'do_login'
							App.navigate "main_login"
						else if @data.code == 'set_password'
							App.navigate "setup_password"
						else if @data.code == 'limit_exceeded'
							@emptyfield = 'Cannot do setup more then 5 times'
						else 
							@emptyfield = 'Please check reference code'
					, (error)=>
						if error == 'offline'
							@emptyfield = 'Please check internet connection'
						else if error == 'server_error'
							@emptyfield = 'Please try again'
					.finally ()->
						CSpinner.hide()

			tologin : ->
				Storage.setData 'refcode', 'remove'
				App.navigate "main_login"

			forgetRefcode:->
				$ionicLoading.show
					scope: $scope
					templateUrl:'views/error-view/Error-Screen-2.html'
					hideOnStateChange: true	

			HelpRefcode:->
				$ionicLoading.show
					scope: $scope
					templateUrl:'views/error-view/RefCode-help-1.html'
					hideOnStateChange: true

			hide:->
				$ionicLoading.hide();
			      
			clear:->
				@emptyfield="" 
				
]
	
