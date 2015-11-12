angular.module('PatientApp.Auth').controller('main_loginCtr', [
  '$scope', 'App', 'Storage', function($scope, App, Storage) {
    return $scope.view = {
      loginerror: '',
      password: '',
      getrefcode: function() {
        return Storage.refcode('get').then(function(value) {
          return console.log(value);
        });
      },
      refre: function() {
        var refrencecode;
        return refrencecode = this.getrefcode();
      },
      check_reflength: function() {
        console.log(this.refrencecode.toString().length);
        if (this.refrencecode.toString().length === 8) {
          console.log(this.refrencecode.toString().length);
          return preventDefault();
        }
      },
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
