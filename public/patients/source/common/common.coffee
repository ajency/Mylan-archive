angular.module 'angularApp.common', []

.factory 'App', ['$q', '$http', '$location', ($q, $http, $location)->

	App =
		SendParseRequest :(cloudFun, param)->


			defer = $q.defer()

			Parse.Cloud.run cloudFun, param,	
				success: (result) ->
					console.log 'common function resulttt'
					console.log result
					defer.resolve result
				error: (error) ->
					console.log 'inside error'
					console.log error
					defer.reject error

			defer.promise
		

		navigate : (path , param )->
			# $location.path 'questionnaire/noValue'

			location = path
			if !_.isEmpty(param)
				location += '/'+param

			console.log '***********'
			console.log location 	
			$location.path location

]
