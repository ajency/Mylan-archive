angular.module('PatientApp.main').controller('ParentCtr', [
  '$scope', 'App', '$ionicLoading', 'Storage', function($scope, App, $ionicLoading, Storage) {
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
      },
      closePopup: function() {
        return $ionicLoading.hide();
      },
      cancelQuiz: function() {
        $ionicLoading.hide();
        Storage.quizDetails('remove');
        return App.navigate('dashboard', {}, {
          animate: false,
          back: false
        });
      },
      exitApp: function() {
        return ionic.Platform.exitApp();
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
