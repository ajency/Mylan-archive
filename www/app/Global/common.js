angular.module('PatientApp.Global', []).factory('App', [
  '$state', '$ionicHistory', '$window', '$q', '$http', '$cordovaNetwork', '$cordovaPreferences', '$ionicScrollDelegate', function($state, $ionicHistory, $window, $q, $http, $cordovaNetwork, $cordovaPreferences, $ionicScrollDelegate) {
    var App;
    return App = {
      start: true,
      validateEmail: /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/,
      onlyNumbers: /^\d+$/,
      menuEnabled: {
        left: false,
        right: false
      },
      previousState: '',
      currentState: '',
      navigate: function(state, params, opts) {
        var animate, back;
        if (params == null) {
          params = {};
        }
        if (opts == null) {
          opts = {};
        }
        if (!_.isEmpty(opts)) {
          animate = _.has(opts, 'animate') ? opts.animate : false;
          back = _.has(opts, 'back') ? opts.back : false;
          $ionicHistory.nextViewOptions({
            disableAnimate: !animate,
            disableBack: !back
          });
        }
        return $state.go(state, params);
      },
      goBack: function(count) {
        return $ionicHistory.goBack(count);
      },
      isAndroid: function() {
        return ionic.Platform.isAndroid();
      },
      isIOS: function() {
        return ionic.Platform.isIOS();
      },
      isWebView: function() {
        return ionic.Platform.isWebView();
      },
      isOnline: function() {
        if (this.isWebView()) {
          return $cordovaNetwork.isOnline();
        } else {
          return navigator.onLine;
        }
      },
      deviceUUID: function() {
        if (this.isWebView()) {
          return device.uuid;
        } else {
          return 'DUMMYUUID';
        }
      },
      hideKeyboardAccessoryBar: function() {
        if ($window.cordova && $window.cordova.plugins.Keyboard) {
          return $cordovaKeyboard.hideAccessoryBar(true);
        }
      },
      errorCode: function(error) {
        error = '';
        if (error.status === '0') {
          error = 'timeout';
        } else {
          error = 'server_error';
        }
        return error;
      },
      sendRequest: function(url, params, headers, timeout) {
        var defer;
        defer = $q.defer();
        if (!_.isUndefined(timeout)) {
          headers['timeout'] = timeout;
        }
        if (this.isOnline()) {
          $http.post(url, params, headers).then(function(data) {
            return defer.resolve(data);
          }, (function(_this) {
            return function(error) {
              return defer.reject(_this.errorCode(error));
            };
          })(this));
        } else {
          defer.reject('offline');
        }
        return defer.promise;
      },
      cordovaPreference: function(key, myMagicValue) {
        var defer;
        defer = $q.defer();
        return $cordovaPreferences.store(key, myMagicValue).then(function(data) {
          console.log('cordovva');
          console.log(data);
          return defer.resolve(data);
        }, function(error) {
          console.log(error);
          return defer.reject(error);
        });
      },
      reteriveCordovaPreference: function() {
        var defer;
        defer = $q.defer();
        return $cordovaPreferences.fetch('int').then(function(data) {
          console.log('sucess data--' + data);
          return defer.resolve(data);
        }, (function(_this) {
          return function(error) {
            return defer.reject(error);
          };
        })(this));
      },
      resize: function() {
        return $ionicScrollDelegate.resize();
      },
      getInstallationId: function() {
        var defer;
        defer = $q.defer();
        if (this.isWebView()) {
          parsePlugin.getInstallationId(function(installationId) {
            return defer.resolve(installationId);
          }, function(error) {
            return defer.reject(error);
          });
        } else {
          defer.resolve('DUMMY_INSTALLATION_ID');
        }
        return defer.promise;
      },
      scrollTop: function() {
        return $ionicScrollDelegate.scrollTop(true);
      },
      scrollBottom: function() {
        return $ionicScrollDelegate.scrollBottom(true);
      },
      SendParseRequest: function(cloudFun, param) {
        var defer;
        defer = $q.defer();
        Parse.Cloud.run(cloudFun, param, {
          success: function(result) {
            return defer.resolve(result);
          },
          error: (function(_this) {
            return function(error) {
              console.log('inside error common function');
              console.log(error);
              return defer.reject(_this.errorCode(error));
            };
          })(this)
        });
        return defer.promise;
      }
    };
  }
]);
