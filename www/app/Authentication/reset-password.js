angular.module('PatientApp.Auth').controller('resetPasswordCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner) {
    return $scope.view = {
      New_password: '',
      Re_password: '',
      passwordmissmatch: '',
      hospitalName: 'HospitalData.name',
      projectName: 'HospitalData.project',
      init: function() {
        var value;
        value = Storage.setHospitalData('get');
        this.hospitalName = value['name'];
        return this.projectName = value['project'];
      },
      completesetup: function() {
        var refrencecode;
        if ((this.New_password === '' || this.Re_password === '') || (_.isUndefined(this.New_password) && _.isUndefined(this.New_password))) {
          return this.passwordmissmatch = "Please Enter Valid 4 digit password";
        } else {
          if (angular.equals(this.New_password, this.Re_password)) {
            CSpinner.show('', 'Checking credentials please wait');
            refrencecode = Storage.setRefernce('get');
            return AuthAPI.setPassword(refrencecode, this.Re_password).then((function(_this) {
              return function(data) {
                CSpinner.hide();
                return CToast.show('Password successfully updated');
              };
            })(this), (function(_this) {
              return function(error) {
                CToast.show('Please try again');
                return CSpinner.hide();
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
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('reset_password', {
      url: '/reset_password',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/authentication-view/reset-password.html',
          controller: 'resetPasswordCtr'
        }
      }
    });
  }
]);
