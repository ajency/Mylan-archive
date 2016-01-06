angular.module('angularApp.dashboard').controller('StartQuestionnaireCtrl', [
  '$scope', 'QuestionAPI', '$routeParams', '$location', function($scope, QuestionAPI, $routeParams, $location) {
    return $scope.view = {
      startQuiz: function(quizID) {
        return $location.path('questionnaire/noValue');
      }
    };
  }
]);
