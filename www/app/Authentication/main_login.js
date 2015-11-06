angular.module('PatientApp.Auth').controller('main_loginCtr', [
  '$scope', 'App', 'Storage', function($scope, App, Storage) {
    return $scope.view = {
      mainlogin: function() {
        return App.navigate("dashboard");
      }
    };
  }
]);
