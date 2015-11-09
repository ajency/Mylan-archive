angular.module('PatientApp.main').controller('ParentCtr', ['$scope', 'App', 'Storage', 'QuestionAPI', function($scope, App, Storage, QuestionAPI) {}]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('parent-questionnaire', {
      url: '/parent-questionnaire',
      abstract: true,
      templateUrl: 'views/main/question-parent.html',
      controller: 'ParentCtr'
    });
  }
]);
