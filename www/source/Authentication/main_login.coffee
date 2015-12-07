angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	 ,'refrencecodeValue','$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner'
	 , ($scope, App, Storage, refrencecodeValue,
	 	 $ionicLoading, AuthAPI, CToast, CSpinner)->

		
		$scope.view =
			temprefrencecode :''
			loginerror: ''
			password:''
			refrencecode: Storage.setRefernce('get')
			showPassword: false

			getrefcode :->
				Storage.refcode('get').then (value)->
						console.log value
				value	
				 

			refre :->
					@refrencecode = Storage.setRefernce('get')

						
			mainlogin : ->
				if @refrencecode =='' || @password ==''
					@loginerror = "Please Enter the credentials "

				else	
					if  _.isUndefined(@refrencecode) || _.isUndefined(@password) 
						@loginerror = "Please Enter valid credentials "
					else
						CSpinner.show '', 'Checking credentials please wait'
						AuthAPI.validateUser(@refrencecode,@password )
						.then (data)=>
							
							console.log data
							if data.code == 'successful_login'
								Storage.login 'set'
								Storage.hospital_data 'set', data.hospital
								Storage.setPatientId 'set', data.patient_id 
								Storage.setProjectId 'set', data.project_id 

								Storage.setData 'patientData','set', data

								CSpinner.hide()
								App.navigate "dashboard", {}, {animate: false, back: false}
							else
								CToast.show 'Please check credentials'
								@loginerror ="Entered password is not correct please try again "
								CSpinner.hide()
						, (error)=>
							CToast.show 'Please try again'
							CSpinner.hide()

			cleardiv :->
				@loginerror =""

			forgetRefcodeorPass:->
					$ionicLoading.show
						scope: $scope
						templateUrl:'views/error-view/Error-Screen-3.html'
						hideOnStateChange: true	

			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false

		
							

			
]
