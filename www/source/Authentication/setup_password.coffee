angular.module 'PatientApp.Auth',[]

.controller 'setup_passwordCtr',['$scope', 'App', 'Storage','$ionicLoading','AuthAPI'
	, ($scope, App, Storage,$ionicLoading,AuthAPI)->


		  	
		$scope.view =
			New_password:''
			Re_password: ''
			passwordmissmatch:''

			completesetup : ->
				
					console.log @New_password
					console.log @Re_password

					if (@New_password =='' ||  @Re_password =='' ) || ((_.isUndefined(@New_password) && _.isUndefined(@New_password)))
						@passwordmissmatch = "Please Enter Valid 4 digit password"
								
					else			
						if angular.equals(@New_password, @Re_password)
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

                        AuthAPI.sendPassword @New_password ,@deviceUUID,@deviceType,@deviceOS,@accessType
                        Storage.setup 'set'
                          .then ->
                          console.log 'setup done'
                          App.navigate "main_login"
						else	
							@passwordmissmatch = 'Passwords Do Not Match, Please Enter Again.'

			clear:->
					@passwordmissmatch= ""	

			passwordHelp:->
					$ionicLoading.show
						scope: $scope
						templateUrl:'views/error-view/Password-help.html'
						hideOnStateChange: true						
			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false	
					



					




		 



]
