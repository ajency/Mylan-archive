angular.module 'PatientApp.Auth',[]

.controller 'setup_passwordCtr',['$scope', 'App', 'Storage','$ionicLoading','AuthAPI'
	, 'CToast', 'CSpinner', 'HospitalData'
	, ($scope, App, Storage,$ionicLoading,AuthAPI, CToast, CSpinner, HospitalData)->
  	
		$scope.view =
			New_password:''
			Re_password: ''
			passwordmissmatch:''
			hospitalName : HospitalData.name
			projectName : HospitalData.project
			hospitalLogo : HospitalData.logo 
			hospitalName : HospitalData.name

			reset :()->
				@New_password = ''
				@Re_password = ''
				@passwordmissmatch = ''

			completesetup : ->
					passtext = document.getElementById("password").value
				
					reg = new RegExp('^[0-9]+$')
					
					boolPassword = reg.test(passtext)

					repasstext = document.getElementById("repassword").value
					
					
					
					boolRePassword = reg.test(repasstext)

					console.log '--'
					console.log boolPassword

					if (@New_password =='' ||  @Re_password =='' ) || ((_.isUndefined(@New_password) && _.isUndefined(@New_password)) || (boolPassword == false) ||(boolRePassword == false))
						@passwordmissmatch = "Please Enter Valid 4 digit password"		
					else			
						if angular.equals(@New_password, @Re_password)
                        	CSpinner.show '', 'Please wait..'
                        	Storage.setData 'refcode', 'get'
                        	.then (refcode) =>
                        		console.log refcode
                        		console.log App.previousState
		                        AuthAPI.setPassword(refcode, @Re_password)
		                        .then (data)=>
		                        	console.log data
		                        	if App.previousState == 'setup' then  App.navigate "main_login" else CToast.show 'Your password is updated '
		                        , (error)=>
		                        	if error == 'offline'
		                        		@passwordmissmatch ='Check net connection'
		                        	else if error == 'server_error'
		                        		@passwordmissmatch = 'Error in setting password,server error'
		                        	else
		                        		@passwordmissmatch = 'Error in setting password,try again'
		                        .finally ()->
		                        	CSpinner.hide()
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
					
		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			$scope.view.reset();

]
