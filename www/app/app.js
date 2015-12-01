angular.module('PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage', 'PatientApp.Global', 'PatientApp.Auth', 'PatientApp.Quest', 'PatientApp.main', 'PatientApp.dashboard']).run([
  '$rootScope', 'App', 'User', '$timeout', function($rootScope, App, User, $timeout) {
    Parse.initialize(APP_ID, JS_KEY);
    $rootScope.App = App;
    App.navigate('init', {}, {
      animate: false,
      back: false
    });
    return $rootScope.$on('$stateChangeSuccess', function(ev, to, toParams, from, fromParams) {
      App.previousState = from.name;
      return App.currentState = to.name;
    });
  }
]).config(['$stateProvider', function($stateProvider) {}]);
