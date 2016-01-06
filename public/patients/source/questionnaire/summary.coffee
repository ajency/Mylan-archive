angular.module 'angularApp.questionnaire',[]

.controller 'summaryController', ['$scope', 'QuestionAPI', '$routeParams', ($scope, QuestionAPI, $routeParams)->

	$scope.view =
		data : []

		init :() -> 
			id = $routeParams.responseId
			console.log '******'
			console.log id
			console.log 'inside init'
			QuestionAPI.getSummary(id)
			.then (data)=>

				@data = data.result
				console.log 'inside then'
				console.log @data
				@display = 'noError'
			,(error)=>
				@display = 'error'
				@errorType = error

]


