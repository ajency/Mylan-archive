angular.module('PatientApp.main', []).controller('MainCtr', [
  '$scope', 'App', 'Storage', 'QuestionAPI', function($scope, App, Storage, QuestionAPI) {
    return $scope.view = {
      onBackClick: function() {
        var count;
        count = -1;
        return App.goBack(count);
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('main', {
      url: '/main',
      abstract: true,
      templateUrl: 'views/main.html',
      controller: 'MainCtr'
    });
  }
]);
