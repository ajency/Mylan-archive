angular.module('PatientApp.main').controller('ParentCtr', [
  '$scope', 'App', '$ionicLoading', function($scope, App, $ionicLoading) {
    return $scope.view = {
      onBackClick: function() {
        var count;
        count = -1;
        return App.goBack(count);
      },
      pause: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/main/pause.html',
          hideOnStateChange: true
        });
      },
      close: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/main/cancel.html',
          hideOnStateChange: true
        });
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
