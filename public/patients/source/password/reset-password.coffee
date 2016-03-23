angular.module 'angularApp.Auth',[]


.controller 'setup_passwordCtr',['$scope', 'App', 'Storage'
	, 'CToast', 'AuthAPI'
	, ($scope, App, Storage, CToast, AuthAPI)->
  	
		$scope.view =
			New_password:''
			Re_password: ''
			passwordmissmatch:''
			hospitalName : 'HospitalData.name'
			projectName : questionnaireName
			hospitalLogoDisplay : hospitalLogo
			hospitalNamedisplay : hospitalName
			ReDcodeDispaly : 'RefcodeData'
			show : false

			reset :()->
				@New_password = ''
				@Re_password = ''
				@passwordmissmatch = ''

			completesetup : ->
					passtext = $('.password').val()
					reg = new RegExp('^[0-9]+$')
					boolPassword = reg.test(passtext)
					repasstext = $('.repassword').val()
					boolRePassword = reg.test(repasstext)

					if (@New_password =='' ||  @Re_password =='' ) || ((_.isUndefined(@New_password) && _.isUndefined(@New_password)) || (boolPassword == false) ||(boolRePassword == false) || passtext.length < 4 || repasstext.length < 4 )
						@passwordmissmatch = "Please enter valid 4 digit password"	
						@New_password = ""
						@Re_password = ""

					else	

						if angular.equals(@New_password, @Re_password)
                        	@show = true
                        	
	                        AuthAPI.setPassword(RefCode, @Re_password)
	                        .then (data)=>
	                        	
	                        	CToast.showVaild 'notify-css','Your password is updated '
	                        , (error)=>
	                        	if error == 'offline'
	                        		@passwordmissmatch ='Please check your internet connection'
	                        	else if error == 'server_error'
	                        		@passwordmissmatch = 'Error in setting password,server error'
	                        	else
	                        		@passwordmissmatch = 'Error in setting password,try again'
	                        .finally ()=>
	                        	@show = false
						else	
							@passwordmissmatch = 'Passwords do not match, please enter again'

			clear:->
				@passwordmissmatch= ""	

							
			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false	
					
		

]