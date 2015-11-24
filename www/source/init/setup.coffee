angular.module 'PatientApp.init'


.controller 'setupCtr', ['$scope', 'App', 'Storage','$ionicLoading','AuthAPI','CToast', 'CSpinner'
	, ($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner)->
		
		$scope.view =
			refcode:''
			emptyfield:''
			deviceOS:''
			deviceType:''
			accessType:''
			deviceUUID:''

			

			verifyRefCode : ->

				console.log @refcode

				Storage.setRefernce('set', @refcode)
				b = Storage.setRefernce('set', @refcode)
				console.log b

				console.log _.isEmpty(@refcode)
				if @refcode =='' || _.isUndefined(@refcode)
					@emptyfield = "Please Enter Valid Reference Code"	

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
					if App.isWebView()
							
						CSpinner.show '', 'Please wait...'
				            # @refcode ,@deviceUUID,@deviceType,@deviceOS,@accessType   
						AuthAPI.validateRefCode @refcode 
						.then (data)=>
							console.log data
							Storage.setHospitalData 'set', data.hospitalData 
							Storage.setRefernce 'set', @refcode
							if data.code == 'do_login'
								CSpinner.hide()
								Storage.refcode 'set',@refcode
								App.navigate "main_login"
							else if data.code == 'set_password'
								CSpinner.hide() 
								Storage.refcode 'set',@refcode
								App.navigate "setup_password"
								# data.code == 'invalid_reference_code'
							else 
								CSpinner.hide()
								CToast.show 'Please check refence code'
							# Storage.refcode 'set',@refcode
							# App.navigate "setup_password"
							# CSpinner.hide()
						, (error)=>
							CToast.show 'Please try again'
							CSpinner.hide()
					else
						Storage.refcode 'set',@refcode
						App.navigate "setup_password"


					# Storage.refcode 'set',@refcode
					# App.navigate "setup_password"
						
					
							

						

			tologin : ->
					# Storage.setup 'get'
					# .then (value)->
					# 	goto = if _.isNull value then "setup" else "main_login"
					# 	App.navigate goto
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
			        hideOnStateChange: false	

			clear:->
					@emptyfield=""        		
				

				
]
	
