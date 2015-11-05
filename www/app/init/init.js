angular.module('PatientApp.init', []).controller('InitCtrl', [
  'Storage', 'App', function(Storage, App) {
    return Storage.setup('get').then(function(value) {
      var goto;
      goto = _.isNull(value) ? "setup" : "setup_password";
      return App.navigate(goto, {}, {
        animate: false,
        back: false
      });
    });
  }
]).config([
  '$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
    return $stateProvider.state('init', {
      url: '/init',
      cache: false,
      controller: 'InitCtrl',
      templateUrl: 'views/init-view/init.html'
    }).state('setup', {
      url: '/setup',
      templateUrl: 'views/authentication-view/main-screen.html',
      controller: 'setupCtr'
    }).state('setup_password', {
      url: '/setup_password',
      templateUrl: 'views/authentication-view/Hospital-login.html',
      controller: 'setup_passwordCtr'
    });
  }
]);
