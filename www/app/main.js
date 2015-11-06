angular.module('PatientApp.main', []).controller('MainCtr', ['$scope', 'App', 'Storage', 'QuestionAPI', function($scope, App, Storage, QuestionAPI) {}]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('main', {
      url: '/main',
      abstract: true,
      templateUrl: 'views/main.html'
    });
  }
]);
