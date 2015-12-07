angular.module('PatientApp.Auth').controller('main_loginCtr', [
  '$scope', 'App', 'Storage', 'refrencecodeValue', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', function($scope, App, Storage, refrencecodeValue, $ionicLoading, AuthAPI, CToast, CSpinner) {
    return $scope.view = {
      temprefrencecode: '',
      loginerror: '',
      password: '',
      refrencecode: Storage.setRefernce('get'),
      showPassword: false,
      getrefcode: function() {
        Storage.refcode('get').then(function(value) {
          return console.log(value);
        });
        return value;
      },
      refre: function() {
        return this.refrencecode = Storage.setRefernce('get');
      },
      mainlogin: function() {
        if (this.refrencecode === '' || this.password === '') {
          return this.loginerror = "Please Enter the credentials ";
        } else {
          if (_.isUndefined(this.refrencecode) || _.isUndefined(this.password)) {
            return this.loginerror = "Please Enter valid credentials ";
          } else {
            CSpinner.show('', 'Checking credentials please wait');
            return AuthAPI.validateUser(this.refrencecode, this.password).then((function(_this) {
              return function(data) {
                console.log(data);
                if (data.code === 'successful_login') {
                  Storage.login('set');
                  Storage.hospital_data('set', data.hospital);
                  Storage.setPatientId('set', data.patient_id);
                  Storage.setProjectId('set', data.project_id);
                  Storage.setData('patientData', 'set', data);
                  CSpinner.hide();
                  return App.navigate("dashboard", {}, {
                    animate: false,
                    back: false
                  });
                } else {
                  CToast.show('Please check credentials');
                  _this.loginerror = "Entered password is not correct please try again ";
                  return CSpinner.hide();
                }
              };
            })(this), (function(_this) {
              return function(error) {
                CToast.show('Please try again');
                return CSpinner.hide();
              };
            })(this));
          }
        }
      },
      cleardiv: function() {
        return this.loginerror = "";
      },
      forgetRefcodeorPass: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/error-view/Error-Screen-3.html',
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
