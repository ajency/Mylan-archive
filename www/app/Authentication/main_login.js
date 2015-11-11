angular.module('PatientApp.Auth').controller('main_loginCtr', [
  '$scope', 'App', 'Storage', function($scope, App, Storage) {
    return $scope.view = {
      refrencecode: '',
      loginerror: '',
      password: '',
      mainlogin: function() {
        if (this.refrencecode === '' || this.password === '') {
          return this.loginerror = "Please Enter the credentials ";
        } else {
          if (_.isUndefined(this.refrencecode) || _.isUndefined(this.password)) {
            return this.loginerror = "Please Enter valid credentials ";
          } else {
            Storage.login('set').then(function() {});
            return App.navigate("dashboard");
          }
        }
      },
      cleardiv: function() {
        return this.loginerror = "";
      }
    };
  }
]);
