angular.module('PatientApp.Global', []).factory('App', [
  '$state', '$ionicHistory', '$window', function($state, $ionicHistory, $window) {
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
      }
    };
  }
]);
