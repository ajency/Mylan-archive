angular.module 'PatientApp.init'


.controller 'setupCtr', ['$scope', 'App', 'Storage','$ionicLoading','AuthAPI'
	, ($scope, App, Storage,$ionicLoading,AuthAPI)->
		
		$scope.view =
			refcode:''
			emptyfield:''
			deviceOS:''
			deviceType:''
			accessType:''
			deviceUUID:''

			

			verifyRefCode : ->
					console.log @refcode
					console.log _.isEmpty(@refcode)
					if @refcode =='' || _.isUndefined(@refcode)
						@emptyfield = "Please Enter Valid Refrence Code"	

					else
						@deviceUUID = App.deviceUUID()
					    
			   if App.isAndroid()
							@deviceOS = "Android"

						if App.isIOS()
							@deviceOS = "IOS"

						if App.isWebView()
			               @deviceType = "Mobile"
			               @accessType = "App"
						else
							if !App.isAndroid() && !App.isIOS()
				                @deviceType = "Desktop"
				                @accessType = "Browser"	  

						AuthAPI.validateRefCode @refcode ,@deviceUUID,@deviceType,@deviceOS,@accessType

						Storage.refcode 'set',@refcode
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

			HelpRefcode:->
					$ionicLoading.show
						scope: $scope
						templateUrl:'views/error-view/RefCode-help-1.html'
						hideOnStateChange: true						
			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false	

			clear:->
					@emptyfield=""        		
				

				
]
	
