angular.module('PatientApp', ['ionic', 'PatientApp.init', 'PatientApp.storage', 'PatientApp.Global', 'PatientApp.Auth', 'PatientApp.Quest']).run([
  '$rootScope', 'App', 'User', '$timeout', function($rootScope, App, User, $timeout) {
    $rootScope.App = App;
    return App.navigate('init');
  }
]).config(['$stateProvider', function($stateProvider) {}]);
