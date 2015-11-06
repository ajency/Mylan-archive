angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'Storage', 'QuestionAPI', function($scope, App, Storage, QuestionAPI) {
    return $scope.view = {
      title: 'C-weight',
      data: [],
      getQuestion: function() {
        return QuestionAPI.get();
      },
      init: function() {
        return this.data = this.getQuestion();
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('questionnaire', {
      url: '/questionnaire',
      templateUrl: 'views/questionnaire/question.html',
      controller: 'questionnaireCtr'
    });
  }
]);
