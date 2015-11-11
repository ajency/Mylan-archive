angular.module('PatientApp.main').controller('ParentCtr', [
  '$scope', 'App', function($scope, App) {
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
    return $stateProvider.state('parent-questionnaire', {
      url: '/parent-questionnaire',
      abstract: true,
      templateUrl: 'views/main/question-parent.html',
      controller: 'ParentCtr'
    });
  }
]);
