angular.module 'angularApp.common', []

.factory 'App', ['$q', '$http', '$location', '$rootScope', ($q, $http, $location, $rootScope)->

	App =
		previousState: ''
		currentState: ''
		test : 4555

		parseErrorCode :(error)->
			errType = ''
			errMsg = error.message
			if error.code == 100
				errType = 'offline'
			else if error.code == 141
				errType = 'server_error'
			else if errMsg.code == 101
				errType = 'server_error'
			else if errMsg.code == 124
				errType = 'offline'
			else if error.code == 209
				error = 'server_connection'
				$rootScope.$broadcast 'on:session:expiry'
			errType
				

		errorCode : (error) ->
			error = ''
			if error.code == '100'
				error = 'server_connection'
			else
				error = 'server_error'
			error	

		SendParseRequest :(cloudFun, param)->

			defer = $q.defer()
			Parse.Cloud.run cloudFun, param,	
				success: (result) ->
					defer.resolve result
				error: (error) =>
					console.log 'inside error common function'
					console.log error
					defer.reject @parseErrorCode error

			defer.promise

		
		

		navigate : (path , param )->
			# $location.path 'questionnaire/noValue'

			location = path
			if !_.isEmpty(param)
				location += '/'+param

			console.log '***********'
			console.log location 	
			$location.path location

		sendRequest :(url,params,headers,timeout)->
				defer = $q.defer()

				if !_.isUndefined(timeout)
					headers['timeout'] = timeout

				
				$http.post url,  params, headers
				.then (data)->
					defer.resolve data
				, (error)=>
					defer.reject @errorCode(error)
				

				defer.promise

]
