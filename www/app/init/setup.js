angular.module('PatientApp.init').controller('setupCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner) {
    return $scope.view = {
      refcode: '',
      emptyfield: '',
      deviceOS: '',
      deviceType: '',
      accessType: '',
      deviceUUID: '',
      verifyRefCode: function() {
        var b;
        console.log(this.refcode);
        Storage.setRefernce('set', this.refcode);
        b = Storage.setRefernce('set', this.refcode);
        console.log(b);
        console.log(_.isEmpty(this.refcode));
        if (this.refcode === '' || _.isUndefined(this.refcode)) {
          return this.emptyfield = "Please Enter Valid Reference Code";
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
          if (App.isWebView()) {
            CSpinner.show('', 'Please wait...');
            return AuthAPI.validateRefCode(this.refcode).then((function(_this) {
              return function(data) {
                console.log(data);
                Storage.setHospitalData('set', data.hospitalData);
                Storage.setRefernce('set', _this.refcode);
                if (data.code === 'do_login') {
                  CSpinner.hide();
                  Storage.refcode('set', _this.refcode);
                  return App.navigate("main_login");
                } else if (data.code === 'set_password') {
                  CSpinner.hide();
                  Storage.refcode('set', _this.refcode);
                  return App.navigate("setup_password");
                } else {
                  CSpinner.hide();
                  return CToast.show('Please check refence code');
                }
              };
            })(this), (function(_this) {
              return function(error) {
                CToast.show('Please try again');
                return CSpinner.hide();
              };
            })(this));
          } else {
            Storage.refcode('set', this.refcode);
            return App.navigate("setup_password");
          }
        }
      },
      tologin: function() {
        return App.navigate("main_login");
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
