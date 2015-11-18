angular.module('PatientApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', function($scope, App, Storage) {
    return $scope.view = {
      New_password: '',
      Re_password: '',
      passwordmissmatch: '',
      completesetup: function() {
        console.log(this.New_password);
        console.log(this.Re_password);
        if ((this.New_password === '' || this.Re_password === '') || (_.isUndefined(this.New_password) && _.isUndefined(this.New_password))) {
          return this.passwordmissmatch = "Please Enter Valid 4 digit password";
        } else {
          if (angular.equals(this.New_password, this.Re_password)) {
            Storage.setup('set').then(function() {});
            console.log('setup done');
            return App.navigate("main_login");
          } else {
            return this.passwordmissmatch = 'Password Do Not Match Enter Again';
          }
        }
      },
      clear: function() {
        return this.passwordmissmatch = "";
      }
    };
  }
]);
