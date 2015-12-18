angular.module('PatientApp.Auth', []).controller('setup_passwordCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', 'HospitalData', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner, HospitalData) {
    $scope.view = {
      New_password: '',
      Re_password: '',
      passwordmissmatch: '',
      hospitalName: HospitalData.name,
      projectName: HospitalData.project,
      reset: function() {
        this.New_password = '';
        this.Re_password = '';
        this.passwordmissmatch = '';
        App.ContactUsEnabled = false;
        App.UpdatesEnabled = false;
        return App.resetPassword = false;
      },
      completesetup: function() {
        if ((this.New_password === '' || this.Re_password === '') || (_.isUndefined(this.New_password) && _.isUndefined(this.New_password))) {
          return this.passwordmissmatch = "Please Enter Valid 4 digit password";
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
                  return CToast.show('Please try again');
                })["finally"](function() {
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
    $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      return $scope.view.reset();
    });
    return $scope.$on('$ionicView.afterLeave', function(event, viewData) {
      App.ContactUsEnabled = true;
      App.UpdatesEnabled = true;
      return App.resetPassword = true;
    });
  }
]);
