angular.module('PatientApp.init').controller('setupCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', 'LoadingPopup', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner, LoadingPopup) {
    return $scope.view = {
      refcode: '',
      emptyfield: '',
      deviceOS: '',
      deviceUUID: '',
      verifyRefCode: function() {
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
          CSpinner.show('', 'Please wait...');
          return AuthAPI.validateRefCode(this.refcode, this.deviceUUID, this.deviceOS).then((function(_this) {
            return function(data) {
              return Storage.setData('hospital_details', 'set', data.hospitalData).then(function() {
                return Storage.setData('refcode', 'set', _this.refcode).then(function() {
                  if (data.code === 'do_login') {
                    return App.navigate("main_login");
                  } else if (data.code === 'set_password') {
                    return App.navigate("setup_password");
                  } else if (data.code === 'limit_exceeded') {
                    return _this.emptyfield = 'Cannot do setup more then 5 times';
                  } else {
                    return _this.emptyfield = 'Please check reference code';
                  }
                });
              });
            };
          })(this), (function(_this) {
            return function(error) {
              return _this.emptyfield = 'Please try again';
            };
          })(this))["finally"](function() {
            return CSpinner.hide();
          });
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
        return $ionicLoading.hide();
      },
      clear: function() {
        return this.emptyfield = "";
      }
    };
  }
]);
