angular.module('PatientApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', function($scope, App, Storage) {
    return $scope.view = {
      completesetup: function() {
        return App.navigate("main_login", {}, {
          animate: false,
          back: false
        });
      }
    };
  }
]);
