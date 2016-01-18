angular.module 'PatientApp.Auth'

.controller 'main_loginCtr',['$scope', 'App', 'Storage'
	 ,'$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner'
	 , ($scope, App, Storage,
	 	 $ionicLoading, AuthAPI, CToast, CSpinner)->

		$scope.view =
			temprefrencecode :''
			loginerror: ''
			password:''
			readonly : ''
			

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
							else
								CToast.show 'Please check credentials'
								@loginerror = "Password entered is incorrect, Please try again"
						, (error)=>
							CToast.show 'Please try again'
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
		
]
