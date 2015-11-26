(function() {
  angular.module('PatientApp', ['ionic', 'ngCordova', 'PatientApp.init', 'PatientApp.storage', 'PatientApp.Global', 'PatientApp.Auth', 'PatientApp.Quest', 'PatientApp.main', 'PatientApp.dashboard']).run([
    '$rootScope', 'App', 'User', '$timeout', function($rootScope, App, User, $timeout) {
      $rootScope.App = App;
      return App.navigate('init', {}, {
        animate: false,
        back: false
      });
    }
  ]).config(['$stateProvider', function($stateProvider) {}]);

}).call(this);
