angular.module('PatientApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', 'HospitalData', 'RefcodeData', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner, HospitalData, RefcodeData) {
    $scope.view = {
      New_password: '',
      Re_password: '',
      passwordmissmatch: '',
      hospitalName: HospitalData.name,
      projectName: HospitalData.project,
      hospitalLogo: HospitalData.logoUrl,
      hospitalName: HospitalData.name,
      ReDcodeDispaly: RefcodeData,
      reset: function() {
        this.New_password = '';
        this.Re_password = '';
        return this.passwordmissmatch = '';
      },
      completesetup: function() {
        var boolPassword, boolRePassword, passtext, reg, repasstext;
        passtext = $('.password').val();
        reg = new RegExp('^[0-9]+$');
        boolPassword = reg.test(passtext);
        repasstext = $('.repassword').val();
        boolRePassword = reg.test(repasstext);
        console.log('--');
        console.log(boolPassword);
        console.log(boolRePassword);
        console.log(passtext.length <= 4);
        console.log(this.Re_password);
        if ((this.New_password === '' || this.Re_password === '') || ((_.isUndefined(this.New_password) && _.isUndefined(this.New_password)) || (boolPassword === false) || (boolRePassword === false) || passtext.length < 4 || repasstext.length < 4)) {
          return this.passwordmissmatch = "Please enter valid 4 digit password";
        } else {
          if (angular.equals(this.New_password, this.Re_password)) {
            CSpinner.show('', 'Please wait..');
            return Storage.setData('refcode', 'get').then((function(_this) {
              return function(refcode) {
                console.log(refcode);
                console.log(App.previousState);
                return AuthAPI.setPassword(refcode, _this.Re_password).then(function(data) {
                  console.log(data);
                  if (App.previousState === 'setup') {
                    return App.navigate("main_login");
                  } else {
                    return CToast.show('Your password is updated ');
                  }
                }, function(error) {
                  if (error === 'offline') {
                    return _this.passwordmissmatch = 'Please check your internet connection';
                  } else if (error === 'server_error') {
                    return _this.passwordmissmatch = 'Error in setting password,server error';
                  } else {
                    return _this.passwordmissmatch = 'Error in setting password,try again';
                  }
                })["finally"](function() {
                  return CSpinner.hide();
                });
              };
            })(this));
          } else {
            return this.passwordmissmatch = 'Passwords do not match, please enter again';
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
    return $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      return $scope.view.reset();
    });
  }
]);
