angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->

		$scope.view =
			refrencecode:''
			loginerror: ''
			password:''

			mainlogin : ->
				if @refrencecode =='' || @password ==''
					@loginerror = "Please Enter the credentials "

				else	
					if  _.isUndefined(@refrencecode) || _.isUndefined(@password) 
						@loginerror = "Please Enter valid credentials "
					else
							
						Storage.login 'set'
						.then ->
						App.navigate "dashboard"
			
			cleardiv :->
				@loginerror =""
					

			
]
