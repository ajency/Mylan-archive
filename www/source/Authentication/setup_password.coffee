angular.module 'PatientApp.Auth',[]

.controller 'setup_passwordCtr',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->


		  	
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
							Storage.setup 'set'
							.then ->
							console.log 'setup done'
							App.navigate "main_login"
		
						else	
							@passwordmissmatch = 'Password Do Not Match Enter Again'

			clear:->
					@passwordmissmatch= ""				



					




		 



]
