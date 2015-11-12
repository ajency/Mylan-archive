angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	, ($scope, App, Storage)->

		
		$scope.view =
			
			loginerror: ''
			password:''

		

			getrefcode :->
				Storage.refcode 'get'
					.then (value)->
						console.log value
					refrencecode =value

			refre :->
				refrencecode = @getrefcode()


			check_reflength :->
				console.log  @refrencecode.toString().length
				if @refrencecode.toString().length == 8
					console.log  @refrencecode.toString().length
					preventDefault()
						
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
