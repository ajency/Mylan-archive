angular.module('PatientApp.main', []).controller('MainCtr', [
  '$scope', 'App', 'Storage', 'QuestionAPI', '$ionicLoading', function($scope, App, Storage, QuestionAPI, $ionicLoading) {
    return $scope.view = {
      onBackClick: function() {
        var count;
        switch (App.currentState) {
          case 'exit-questionnaire':
            return App.navigate("dashboard", {}, {
              animate: false,
              back: false
            });
          default:
            count = -1;
            return App.goBack(count);
        }
      },
      resetPassword: function() {
        return App.navigate('reset_password');
      },
      contact: function() {
        return App.navigate('contact');
      },
      update: function() {
        return App.navigate('notification');
      },
      pause: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/main/pause.html',
          hideOnStateChange: true
        });
      },
      exitApp: function() {
        return ionic.Platform.exitApp();
      },
      closePopup: function() {
        return $ionicLoading.hide();
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
