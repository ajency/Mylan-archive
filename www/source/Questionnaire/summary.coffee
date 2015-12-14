angular.module 'PatientApp.Quest'

.controller 'SummaryCtr',['$scope', 'App', 'QuestionAPI','$stateParams', 
	'Storage'
	, ($scope, App, QuestionAPI, $stateParams, Storage)->

		$scope.view =
			title: 'C-weight'
			data : []
			go : ''
			response : ''

			getSummary : ->
				@data = Storage.summary('get')
				console.log 'summmmm'
				console.log @data

			getSummaryApi :(param)->
				QuestionAPI.getSummary param
				.then (data)=>
					console.log '--getSummaryApi---'
					@data = data.result
					console.log @data
					@display = 'noError'
				,(error)=>
					@display = 'error'
					@errorType = error
					
			init : ->
				summarytype = $stateParams.summary
				if summarytype == 'set'
					@getSummary()
				else 
					param =
						'responseId' : $stateParams.summary
					@getSummaryApi(param)

			submitSummary : ->
				ionic.Platform.exitApp()

			prevQuestion : ->
				valueAction = QuestionAPI.setAction 'get'
				action =
					questionId : valueAction.questionId
					mode : 'prev'
				QuestionAPI.setAction 'set', action
				App.navigate 'questionnaire', quizID: $stateParams.quizID

		
]


.config ['$stateProvider', ($stateProvider)->

	$stateProvider

	.state 'summary',
			url: '/summary:summary'
			parent: 'parent-questionnaire'
			views: 
				"QuestionContent":
					templateUrl: 'views/questionnaire/summary.html'
					controller: 'SummaryCtr'
]
