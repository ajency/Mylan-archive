angular.module 'angularApp.dashboard'

.controller 'StartQuestionnaireCtrl', ['$scope', 'QuestionAPI', '$routeParams', '$location', 'Storage'
	, ($scope, QuestionAPI, $routeParams, $location, Storage)->

		$scope.view =
			startQuiz :(quizID) ->

				questionnaireData = 
					respStatus : 'noValue'
					responseId : ''

				Storage.questionnaire 'set', questionnaireData

				$location.path 'questionnaire'
				# App.navigate 'questionnaire', respStatus:'noValue'

]