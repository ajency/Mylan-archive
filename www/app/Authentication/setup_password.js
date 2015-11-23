angular.module('PatientApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', function($scope, App, Storage, $ionicLoading) {
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
            return this.passwordmissmatch = 'Passwords Do Not Match, Please Enter Again.';
          }
        }
      },
      clear: function() {
        return this.passwordmissmatch = "";
      },
      passwordHelp: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/error-view/Password-help.html',
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
