angular.module 'angularApp.questionnaire',[]

.controller 'summaryController', ['$scope', 'QuestionAPI', '$routeParams', 'CToast'
	, ($scope, QuestionAPI, $routeParams, CToast)->

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

			submitSummary : ->
					# CSpinner.show '', 'Please wait..'
					param = 
						responseId : $routeParams.responseId
					QuestionAPI.submitSummary param
					.then (data)=>
						console.log 'data'
						console.log 'succ submiteed'
						CToast.show 'submiteed successfully '
						# App.navigate 'exit-questionnaire'
					,(error)=>
						console.log 'error'
						console.log error
						CToast.show 'Error in submitting questionnarie'
					.finally ->
						# CSpinner.hide()

]


