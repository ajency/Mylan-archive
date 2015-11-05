angular.module('PatientApp.init').controller('setupCtr', [
  '$scope', 'App', 'Storage', function($scope, App, Storage) {
    return $scope.view = {
      verifyRefCode: function() {
        return Storage.setup('set').then(function() {
          return App.navigate("setup_password", {}, {
            animate: false,
            back: false
          });
        });
      },
      tologin: function() {
        return App.navigate("main_login", {}, {
          animate: false,
          back: false
        });
      }
    };
  }
]);
