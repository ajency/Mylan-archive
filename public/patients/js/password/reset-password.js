angular.module('angularApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', 'CToast', 'AuthAPI', function($scope, App, Storage, CToast, AuthAPI) {
    return $scope.view = {
      New_password: '',
      Re_password: '',
      passwordmissmatch: '',
      hospitalName: 'HospitalData.name',
      projectName: questionnaireName,
      hospitalLogoDisplay: hospitalLogo,
      hospitalNamedisplay: hospitalName,
      ReDcodeDispaly: 'RefcodeData',
      show: false,
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
        if ((this.New_password === '' || this.Re_password === '') || ((_.isUndefined(this.New_password) && _.isUndefined(this.New_password)) || (boolPassword === false) || (boolRePassword === false) || passtext.length < 4 || repasstext.length < 4)) {
          this.passwordmissmatch = "Please enter valid 4 digit password";
          this.New_password = "";
          return this.Re_password = "";
        } else {
          if (angular.equals(this.New_password, this.Re_password)) {
            this.show = true;
            return AuthAPI.setPassword(RefCode, this.Re_password).then((function(_this) {
              return function(data) {
                return CToast.showVaild('notify-css', 'Your password is updated ');
              };
            })(this), (function(_this) {
              return function(error) {
                if (error === 'offline') {
                  return _this.passwordmissmatch = 'Please check your internet connection';
                } else if (error === 'server_error') {
                  return _this.passwordmissmatch = 'Error in setting password,server error';
                } else {
                  return _this.passwordmissmatch = 'Error in setting password,try again';
                }
              };
            })(this))["finally"]((function(_this) {
              return function() {
                return _this.show = false;
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
      hide: function() {
        $ionicLoading.hide();
        return {
          hideOnStateChange: false
        };
      }
    };
  }
]);
