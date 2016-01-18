angular.module('PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage', 'PatientApp.Global', 'PatientApp.Auth', 'PatientApp.Quest', 'PatientApp.main', 'PatientApp.dashboard', 'PatientApp.contact', 'PatientApp.notification']).run([
  '$rootScope', 'App', 'User', '$timeout', function($rootScope, App, User, $timeout) {
    Parse.initialize(APP_ID, JS_KEY);
    $rootScope.App = App;
    App.navigate('init', {}, {
      animate: false,
      back: false
    });
    return $rootScope.$on('$stateChangeSuccess', function(ev, to, toParams, from, fromParams) {
      var bool, hideForStates;
      App.previousState = from.name;
      App.currentState = to.name;
      hideForStates = ['reset_password', 'setup_password', 'main_login'];
      bool = !_.contains(hideForStates, App.currentState);
      return App.menuButtonEnabled = bool;
    });
  }
]).config(['$stateProvider', function($stateProvider) {}]);
