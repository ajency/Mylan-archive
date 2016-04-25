angular.module 'angularApp.contact',[]

.controller 'contactCtrl',['$scope', 'App', '$routeParams', 'contactAPI', '$location', '$rootScope', 'CToast'
	, ($scope, App, $routeParams, contactAPI, $location, $rootScope, CToast)->

		$scope.view =
			data : []
			display : 'loader'
			page : 0
			noNotification : null
			limit : 10
			gotAllRequests: false
			email : hospitalEmail
			phone : hospitalPhone
			errorMsg : ''
			disable : false
			Address : hospitalAddress

			firstName : ''
			lastName : ''
			
			patientEmail : ''
			message : ''
			patientPhone : ''
			message : ''
			errorText : ''
			successText : ''

			onSubmit :()->
				firstName = (@firstName.match(/^[a-zA-Z]*$/));
				lastName = (@lastName.match(/^[a-zA-Z]*$/));
				phn = (@patientPhone.match(/^\d{10}$/));
				email = (@patientEmail.match(/\S+@\S+\.\S+/));
				@errorText = ''
				@successText = ''
				if @firstName == '' || firstName == null
					@errorText = 'Please enter valid first name'
				else if @lastName == '' || lastName == null
					@errorText = 'Please enter valid last name'
				else if @patientEmail == ''|| email == null
					@errorText = 'Please enter valid email'
				else if phn == null
					@errorText = 'Please Enter valid 10 digit phone number'
				else if @message == ''
					@errorText = 'Please Enter message'
				else  

					param = 
						"referenceCode" : RefCode
						"patientName": @firstName+ ' ' + @lastName
						"patientEmail" : @patientEmail
						"patientPhone" : @patientPhone
						"hospitalId" : hospitalIdd
						"projectId" : projectIdd
						"message": @message

					contactAPI.sendEmail param
						.then (data)=>
							@successText = 'Mail sent successfully.'
						,(error)=>
							@errorText = 'Error in sending mail, try again'
						.finally =>
							console.log 'error'



			init :() ->
				@errorMsg = ''
				param =
					"patientId" : RefCode
					"page" : @page
					"limit": @limit

				notifyAPI.getNotification param
				.then (data)=>	
					@disable = false
					@display = 'noError'
					dataSize = _.size data
					if dataSize > 0
						if dataSize < @limit
							@canLoadMore = false
						else
							@canLoadMore = true
					else
						@canLoadMore = false

					@gotAllRequests = true if !@canLoadMore
				
					@data = @data.concat data
					_.each @data, (value)->
						value['occurrenceDateDisplay'] = moment(value.occurrenceDate).format('DD-MM-YYYY hh:mm A')
						value['graceDateDisplay'] = moment(value.graceDate).format('DD-MM-YYYY hh:mm A')

				,(error) =>
					console.log 'inside notification page error'
					console.log error
					@display = 'error'
					@errorType = error

				.finally =>
					@page = @page + 1

				$rootScope.$broadcast 'notification:count'
			
			deleteNotify:(id)->
				@errorMsg = ''
				param = 
					"notificationId":id

				notifyAPI.deleteNotification param
				.then (data)=>
					idObject = _.findWhere(@data, {id: id}) 
					if idObject.hasSeen == false 
						$rootScope.$broadcast 'decrement:notification:count'

					spliceIndex = _.findIndex $scope.view.data, (request)->
						request.id is id
					$scope.view.data.splice(spliceIndex, 1) if spliceIndex isnt -1

					if @data.length < 5
						@init()
				,(error)->
					console.log 'error data'
				


				# App.notification.decrement()

			seenNotify:(id)->		
				param = 
					"notificationId":id

				notifyAPI.setNotificationSeen param
				.then (data)=>
					idObject = _.findWhere(@data, {id: id}) 
					if idObject.hasSeen == false 
						$rootScope.$broadcast 'decrement:notification:count'

					console.log data
				,(error)->
					console.log 'error data'
				$location.path('dashboard')

			onTapToRetry : ->
				@display = 'loader'
				@gotAllRequests = false
				@page = 0
				@init()

			deleteNotifcation:(id)->
			

			showMore :()->
				@init()

			DeleteAll:()->
				# param = 
				# 	"patientId": RefCode 
				@disable = true
				@errorMsg = ''

				objIds = _.pluck(@data, 'id')  
				param = 
				 	"notificationIds": objIds

				@display = 'loader' 	
				notifyAPI.deleteAllNotification param
				.then (data)=>
					@data = []
					@page = 0
					@canLoadMore = false
					@init()
					console.log 'sucess notification seen data'
					console.log data
				,(error)=>
					if error == 'offline'
						@errorMsg = 'Notification not clear , check your internet connection'
					else 
						@errorMsg = 'Notification not clear , try again'

					@display = 'noError' 
					
					console.log 'error data'
					# @display = 'error'
					# @errorType = error
				






]