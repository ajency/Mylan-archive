angular.module('PatientApp.Auth').controller('main_loginCtr', [
  '$scope', 'App', 'Storage', 'refrencecodeValue', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', function($scope, App, Storage, refrencecodeValue, $ionicLoading, AuthAPI, CToast, CSpinner) {
    $scope.view = {
      temprefrencecode: '',
      loginerror: '',
      password: '',
      refrencecode: refrencecodeValue,
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
                  return Parse.User.become(data.user).then(function(user) {
                    console.log('succ user');
                    console.log(user);
                    return Storage.setData('logged', 'set', true).then(function() {
                      return Storage.setData('refcode', 'set', _this.refrencecode).then(function() {
                        return Storage.setData('hospital_details', 'set', data.hospital).then(function() {
                          return Storage.setData('patientData', 'set', data.questionnaire).then(function() {
                            return App.navigate("dashboard", {}, {
                              animate: false,
                              back: false
                            });
                          });
                        });
                      });
                    });
                  }, function(error) {
                    console.log('in error');
                    return console.log(error);
                  });
                } else {
                  CToast.show('Please check credentials');
                  return _this.loginerror = "Password entered is incorrect, Please try again";
                }
              };
            })(this), (function(_this) {
              return function(error) {
                return CToast.show('Please try again');
              };
            })(this))["finally"](function() {
              return CSpinner.hide();
            });
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
      },
      reset: function() {
        var loginerror, password;
        loginerror = '';
        return password = '';
      }
    };
    return $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      return $scope.view.reset();
    });
  }
]);
