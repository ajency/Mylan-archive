angular.module('PatientApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', function($scope, App, Storage) {
    return $scope.view = {
      New_password: '',
      Re_password: '',
      completesetup: function() {
        return Storage.setup('set').then(function() {
          console.log(this.New_password);
          console.log(this.Re_password);
          return App.navigate("main_login");
        });
      }
    };
  }
]);
