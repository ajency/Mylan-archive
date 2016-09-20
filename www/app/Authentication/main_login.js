(function() {
  angular.module('PatientApp.Auth').controller('main_loginCtr', [
    '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', 'CToast', 'CSpinner', '$ionicPlatform', 'RefcodeData', function($scope, App, Storage, $ionicLoading, AuthAPI, CToast, CSpinner, $ionicPlatform, RefcodeData) {
      var onDeviceBack, onHardwareBackLogin;
      $scope.view = {
        temprefrencecode: '',
        loginerror: '',
        password: '',
        readonly: '',
        refrencecode: RefcodeData,
        mainlogin: function() {
          if (this.refrencecode === '' || this.password === '') {
            return this.loginerror = "Please enter your credentials ";
          } else {
            if (_.isUndefined(this.refrencecode) || _.isUndefined(this.password)) {
              return this.loginerror = "Please enter valid credentials ";
            } else {
              CSpinner.show('', 'Checking credentials please wait');
              return AuthAPI.validateUser(this.refrencecode, this.password).then((function(_this) {
                return function(data) {
                  if (data.code === 'successful_login') {
                    return Parse.User.become(data.user).then(function(user) {
                      return Storage.setData('logged', 'set', true);
                    }).then(function() {
                      return Storage.setData('refcode', 'set', _this.refrencecode);
                    }).then(function() {
                      return Storage.setData('hospital_details', 'set', data.hospital);
                    }).then(function() {
                      return Storage.setData('patientData', 'set', data.questionnaire);
                    }).then(function() {
                      return App.navigate("dashboard", {}, {
                        animate: false,
                        back: false
                      });
                    }, function(error) {
                      return console.log(error);
                    });
                  } else if (data.code === 'limit_exceeded') {
                    return _this.loginerror = 'Cannot do setup more then 10 times';
                  } else if (data.code === 'invalid_login') {
                    _this.password = '';
                    if (_this.readonly === false) {
                      _this.refrencecode = '';
                    }
                    return _this.loginerror = 'Credentials entered are invalid';
                  } else if (data.code === 'password_not_set') {
                    return _this.loginerror = 'No password set for the reference code';
                  } else if (data.code === 'baseline_not_set') {
                    return _this.loginerror = 'Patient cannot be activated,due to missing activation data . Please contact your hospital administrator';
                  } else if (data.code === 'project_paused') {
                    return _this.loginerror = 'This project is paused. Please contact your hospital administrator';
                  } else if (data.code === 'login_attempts') {
                    return _this.loginerror = 'You have exceeded the maximum login attempts.Please contact your hospital administrator';
                  } else if (data.code === 'inactive_user') {
                    return _this.loginerror = 'This patient is inactive. Please contact your hospital administrator';
                  } else {
                    CToast.show('Please check credentials');
                    return _this.loginerror = "Password entered is incorrect, Please try again";
                  }
                };
              })(this), (function(_this) {
                return function(error) {
                  if (error === 'offline') {
                    return _this.loginerror = 'Please check net connection';
                  } else if (error === 'server_error') {
                    return _this.loginerror = 'Please try again';
                  }
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
          this.loginerror = '';
          return this.password = '';
        },
        call: function() {
          return App.callUs(MYLANPHONE);
        },
        onDeviceBack: function() {
          var count;
          if (App.previousState === 'setup_password') {
            return App.navigate("setup", {}, {
              animate: false,
              back: false
            });
          } else {
            count = -1;
            return App.goBack(count);
          }
        }
      };
      onDeviceBack = function() {
        var count;
        if (App.previousState === 'setup_password') {
          return App.navigate("setup", {}, {
            animate: false,
            back: false
          });
        } else {
          count = -1;
          return App.goBack(count);
        }
      };
      $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
        if (!viewData.enableBack) {
          viewData.enableBack = true;
        }
        $scope.view.reset();
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            $scope.view.refrencecode = refcode;
            if ($scope.view.refrencecode === null) {
              return $scope.view.readonly = false;
            } else {
              return $scope.view.readonly = true;
            }
          };
        })(this));
      });
      onHardwareBackLogin = null;
      $scope.$on('$ionicView.enter', function() {
        return onHardwareBackLogin = $ionicPlatform.registerBackButtonAction(onDeviceBack, 1000);
      });
      return $scope.$on('$ionicView.leave', function() {
        if (onHardwareBackLogin) {
          return onHardwareBackLogin();
        }
      });
    }
  ]);

}).call(this);
