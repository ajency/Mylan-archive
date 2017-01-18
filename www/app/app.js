(function() {
  angular.module('PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage', 'PatientApp.Global', 'PatientApp.Auth', 'PatientApp.Quest', 'PatientApp.main', 'PatientApp.dashboard', 'PatientApp.contact', 'PatientApp.notification', 'PatientApp.notificationCount']).run([
    '$rootScope', 'App', 'User', '$timeout', '$ionicPlatform', function($rootScope, App, User, $timeout, $ionicPlatform) {
      Parse.initialize(APP_ID, JS_KEY);
      $rootScope.App = App;
      App.navigate('init', {}, {
        animate: false,
        back: false
      });
      App.notification = {
        badge: false,
        count: 0,
        increment: function() {
          this.badge = true;
          return this.count = this.count + 1;
        },
        decrement: function() {
          this.count = this.count - 1;
          if (this.count <= 0) {
            return this.badge = false;
          }
        }
      };
      $ionicPlatform.ready(function() {
        if (window.cordova && window.cordova.plugins.Keyboard) {
          cordova.plugins.Keyboard.hideKeyboardAccessoryBar(false);
          return cordova.plugins.Keyboard.disableScroll(true);
        }
      });
      return $rootScope.$on('$stateChangeSuccess', function(ev, to, toParams, from, fromParams) {
        var bool, hideForStates;
        App.previousState = from.name;
        App.currentState = to.name;
        hideForStates = ['reset_password', 'setup_password', 'main_login', 'questionnaire', 'summary'];
        bool = !_.contains(hideForStates, App.currentState);
        App.menuButtonEnabled = bool;
        return App.questinnarieButton = App.currentState === 'questionnaire' ? true : false;
      });
    }
  ]).config([
    '$ionicConfigProvider', function($ionicConfigProvider) {
      $ionicConfigProvider.views.swipeBackEnabled(false);
      $ionicConfigProvider.views.forwardCache(true);
      return $ionicConfigProvider.views.transition('none');
    }
  ]);

}).call(this);
