angular.module 'angularApp.dashboard'

.controller 'StartQuestionnaireCtrl', ['$scope', 'QuestionAPI', '$routeParams', '$location'
	, ($scope, QuestionAPI, $routeParams, $location)->

		$scope.view =
			startQuiz :(quizID) ->
				$location.path 'questionnaire/noValue'
				# App.navigate 'questionnaire', respStatus:'noValue'

]