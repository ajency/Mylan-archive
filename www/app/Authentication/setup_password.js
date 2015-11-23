angular.module('PatientApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', function($scope, App, Storage, $ionicLoading, AuthAPI) {
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
            this.deviceUUID = App.deviceUUID();
            if (App.isAndroid()) {
              this.deviceOS = "Android";
            }
            if (App.isIOS()) {
              this.deviceOS = "IOS";
            }
            if (App.isWebView()) {
              this.deviceType = "Mobile";
              this.accessType = "App";
            } else {
              if (!App.isAndroid() && !App.isIOS()) {
                this.deviceType = "Desktop";
                this.accessType = "Browser";
              }
            }
            AuthAPI.sendPassword(this.New_password, this.deviceUUID, this.deviceType, this.deviceOS, this.accessType);
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
