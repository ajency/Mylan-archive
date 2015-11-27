angular.module('PatientApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', 'HospitalData', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner, HospitalData) {
    return $scope.view = {
      New_password: '',
      Re_password: '',
      passwordmissmatch: '',
      hospitalName: HospitalData.name,
      projectName: HospitalData.project,
      init: function() {},
      completesetup: function() {
        if ((this.New_password === '' || this.Re_password === '') || (_.isUndefined(this.New_password) && _.isUndefined(this.New_password))) {
          return this.passwordmissmatch = "Please Enter Valid 4 digit password";
        } else {
          if (angular.equals(this.New_password, this.Re_password)) {
            CSpinner.show('', 'Please wait..');
            return Storage.refcode('get').then((function(_this) {
              return function(refcode) {
                console.log(refcode);
                console.log(App.previousState);
                return AuthAPI.setPassword(refcode, _this.Re_password).then(function(data) {
                  CSpinner.hide();
                  console.log(data);
                  if (App.previousState === 'setup') {
                    return App.navigate("main_login");
                  } else {
                    return CToast.show('Your password is updated ');
                  }
                }, function(error) {
                  CToast.show('Please try again');
                  return CSpinner.hide();
                });
              };
            })(this));
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
