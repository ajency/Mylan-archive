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
				# options = 
				# 	responseId: $stateParams.responseID

				# QuestionAPI.getSummary options
				# .then (data)=>
				# 	console.log data
				# 	@data = data 
				# , (error)=>
				# 	console.log 'err'
					
			init : ->
				@getSummary()

			submitSummary : ->
				options = 
					quizID: $stateParams.quizID

				QuestionAPI.submitSummary options
				.then (data)=>
					Storage.getNextQuestion 'set' , 1
					Storage.quizDetails('remove')
					App.navigate 'dashboard', {}, {animate: false, back: false}
				, (error)=>
					console.log 'err'

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
			url: '/summary'
			parent: 'parent-questionnaire'
			views: 
				"QuestionContent":
					templateUrl: 'views/questionnaire/summary.html'
					controller: 'SummaryCtr'
]
