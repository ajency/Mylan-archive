angular.module('PatientApp.Auth').controller('main_loginCtr', [
  '$scope', 'App', 'Storage', 'refrencecodeValue', '$ionicLoading', function($scope, App, Storage, refrencecodeValue, $ionicLoading) {
    return $scope.view = {
      temprefrencecode: '',
      loginerror: '',
      password: '',
      refrencecode: '',
      getrefcode: function() {
        Storage.refcode('get').then(function(value) {
          return console.log(value);
        });
        return value;
      },
      refre: function() {
        console.log(refrencecodeValue);
        return this.refrencecode = refrencecodeValue;
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
      },
      forgetRefcodeorPass: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/error-view/Error-Screen-3.html',
          hideOnStateChange: true
        });
      },
      hide: function() {
        $ionicLoading.hide();
        return {
          hideOnStateChange: false
        };
      }
    };
  }
]);
