angular.module 'angularApp.common', []

.factory 'App', ['$q', '$http', ($q, $http)->

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

]
