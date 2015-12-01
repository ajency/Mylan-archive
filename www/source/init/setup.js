(function() {
  angular.module('PatientApp.init').controller('setupCtr', [
    '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner) {
      return $scope.view = {
        refcode: '',
        emptyfield: '',
        deviceOS: '',
        deviceType: '',
        accessType: '',
        deviceUUID: '',
        last: '',
        verifyRefCode: function() {
          var b;
          Storage.setRefernce('set', this.refcode);
          b = Storage.setRefernce('set', this.refcode);
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
          $ionicLoading.hide();
          return {
            hideOnStateChange: false
          };
        },
        clear: function() {
          return this.emptyfield = "";
        },
        myFunction: function($event) {
          var a;
          console.log('--');
          a = $('#simple').val();
          console.log(a);
          if (a.length > 3) {
            return $event.preventDefault();
          }
        }
      };
    }
  ]);

}).call(this);
