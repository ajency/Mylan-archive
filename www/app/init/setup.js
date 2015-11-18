angular.module('PatientApp.init').controller('setupCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', function($scope, App, Storage, $ionicLoading, AuthAPI) {
    return $scope.view = {
      refcode: '',
      emptyfield: '',
      deviceOS: '',
      deviceType: '',
      accessType: '',
      deviceUUID: '',
      verifyRefCode: function() {
        console.log(this.refcode);
        console.log(_.isEmpty(this.refcode));
        if (this.refcode === '' || _.isUndefined(this.refcode)) {
          return this.emptyfield = "Please Enter Valid Refrence Code";
        } else {
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
          AuthAPI.validateRefCode(this.refcode, this.deviceUUID, this.deviceType, this.deviceOS, this.accessType);
          Storage.refcode('set', this.refcode);
          return App.navigate("setup_password");
        }
      },
      tologin: function() {
        return Storage.setup('get').then(function(value) {
          var goto;
          goto = _.isNull(value) ? "setup" : "main_login";
          return App.navigate(goto);
        });
      },
      forgetRefcode: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/error-view/Error-Screen-2.html',
          hideOnStateChange: true
        });
      },
      HelpRefcode: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/error-view/RefCode-help-1.html',
          hideOnStateChange: true
        });
      },
      hide: function() {
        $ionicLoading.hide();
        return {
          hideOnStateChange: false
        };
      },
      clear: function() {
        return this.emptyfield = "";
      }
    };
  }
]);
