angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	 ,'$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', '$ionicPlatform'
	 , ($scope, App, Storage,
	 	 $ionicLoading, AuthAPI, CToast, CSpinner, $ionicPlatform)->

		$scope.view =
			temprefrencecode :''
			loginerror: ''
			password:''
			readonly : ''
			

			mainlogin : ->
				if @refrencecode =='' || @password ==''
					@loginerror = "Please enter your credentials "
				else	
					if  _.isUndefined(@refrencecode) || _.isUndefined(@password) 
						@loginerror = "Please enter valid credentials "
					else
						CSpinner.show '', 'Checking credentials please wait'
						AuthAPI.validateUser(@refrencecode,@password )
						.then (data)=>
							console.log data
							if data.code == 'successful_login'
								Parse.User.become data.user
								.then (user)=>
									Storage.setData 'logged','set', true
								.then ()=> 
									Storage.setData 'refcode','set', @refrencecode
								.then ()=>
									Storage.setData 'hospital_details', 'set', data.hospital
								.then ()=>
									Storage.setData 'patientData', 'set', data.questionnaire
								.then ()=>
									App.navigate "dashboard", {}, {animate: false, back: false}
								, (error)->
									console.log 'in error'
									console.log error
							else if data.code == 'limit_exceeded'
								@loginerror = 'Cannot do setup more then 5 times'
							else if data.code == 'invalid_login'
								@password = ''
								if @readonly == false 
									 @refrencecode = ''
								@loginerror = 'Credentials entered are invalid'
							else if data.code == 'password_not_set'
								@loginerror = 'No password set for the reference code'
							else if data.code == 'baseline_not_set'
								@loginerror = 'Please contact hospital administrator for further login'
							else
								CToast.show 'Please check credentials'
								@loginerror = "Password entered is incorrect, Please try again"
						, (error)=>
							if error == 'offline'
								@loginerror = 'Please check net connection'
							else if error == 'server_error'
								@loginerror = 'Please try again'
						.finally ()->
							CSpinner.hide()

			cleardiv :->
				@loginerror = ""

			forgetRefcodeorPass:->
					$ionicLoading.show
						scope: $scope
						templateUrl:'views/error-view/Error-Screen-3.html'
						hideOnStateChange: true	

			hide:->
			        $ionicLoading.hide();
			        hideOnStateChange: false

			reset:->
				@loginerror = ''
				@password = ''

			call:()->
				App.callUs(MYLANPHONE)

			onDeviceBack:->
				console.log 'ondevice backk'
				if App.previousState == 'setup_password'
					App.navigate "setup", {}, {animate: false, back: false}
				else
					count = -1
					App.goBack count



		onDeviceBack = ->
			console.log 'ondevice backk'
			if App.previousState == 'setup_password'
				App.navigate "setup", {}, {animate: false, back: false}
			else
				count = -1
				App.goBack count

		$scope.$on '$ionicView.beforeEnter', (event, viewData)->
			if !viewData.enableBack
				viewData.enableBack = true
				
			$scope.view.reset();
			Storage.setData 'refcode', 'get'
			.then (refcode)=>
				$scope.view.refrencecode = refcode
				if $scope.view.refrencecode == null
					$scope.view.readonly = false
				else
					$scope.view.readonly = true

		onHardwareBackLogin = null 

		$scope.$on '$ionicView.enter', ->
			console.log '$ionicView.enter questionarie'
			onHardwareBackLogin = $ionicPlatform.registerBackButtonAction onDeviceBack, 1000
		

		$scope.$on '$ionicView.leave', ->
			console.log '$ionicView.leave'
			if onHardwareBackLogin then onHardwareBackLogin()
		
]
