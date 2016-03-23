angular.module 'angularApp.common', []

.factory 'App', ['$q', '$http', '$location', ($q, $http, $location)->

	App =
		previousState: ''
		currentState: ''
		test : 4555

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
					defer.reject @errorCode error

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
