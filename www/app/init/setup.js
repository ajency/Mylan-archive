angular.module('PatientApp.init').controller('setupCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', 'LoadingPopup', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner, LoadingPopup) {
    return $scope.view = {
      refcode: '',
      emptyfield: '',
      deviceOS: '',
      deviceType: '',
      accessType: '',
      deviceUUID: '',
      last: '',
      verifyRefCode: function() {
        Storage.setRefernce('set', this.refcode);
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
              Storage.setHospitalData('set', data.hospitalData);
              Storage.hospital_data('set', data.hospitalData);
              Storage.setRefernce('set', _this.refcode);
              if (data.code === 'do_login') {
                CSpinner.hide();
                return Storage.refcode('set', _this.refcode).then(function() {
                  return App.navigate("main_login");
                });
              } else if (data.code === 'set_password') {
                CSpinner.hide();
                return Storage.refcode('set', _this.refcode).then(function() {
                  return App.navigate("setup_password");
                });
              } else if (data.code === 'limit_exceeded') {
                CSpinner.hide();
                return CToast.show('Cannot do setup more then 5 times');
              } else {
                CSpinner.hide();
                return CToast.show('Please check reference code');
              }
            };
          })(this), (function(_this) {
            return function(error) {
              CToast.show('Please try again');
              return CSpinner.hide();
            };
          })(this));
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
