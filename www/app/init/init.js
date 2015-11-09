angular.module('PatientApp.init', []).controller('InitCtrl', [
  'Storage', 'App', '$scope', function(Storage, App, $scope) {
    return Storage.setup('get').then(function(value) {
      var goto;
      console.log('---------');
      console.log(value);
      if (_.isNull(value)) {
        console.log('inside if');
        goto = 'setup';
        return App.navigate(goto);
      } else {
        console.log('iee');
        return Storage.login('get').then(function(value) {
          goto = _.isNull(value) ? 'main_login' : 'dashboard';
          return App.navigate(goto);
        });
      }
    });
  }
]).config([
  '$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
    return $stateProvider.state('init', {
      url: '/init',
      cache: false,
      controller: 'InitCtrl',
      templateUrl: 'views/init-view/init.html'
    }).state('setup_password', {
      url: '/setup_password',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/authentication-view/Hospital-login.html',
          controller: 'setup_passwordCtr'
        }
      }
    }).state('main_login', {
      url: '/main_login',
      templateUrl: 'views/authentication-view/Main-Screen-login.html',
      controller: 'main_loginCtr'
    }).state('setup', {
      url: '/setup',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/authentication-view/main-screen.html',
          controller: 'setupCtr'
        }
      }
    });
  }
]);
