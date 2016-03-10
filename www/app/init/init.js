angular.module('PatientApp.init', []).controller('InitCtrl', [
  'Storage', 'App', '$scope', 'QuestionAPI', '$q', '$rootScope', 'Push', function(Storage, App, $scope, QuestionAPI, $q, $rootScope, Push) {
    $rootScope.$on('$cordovaPush:notificationReceived', function(e, p) {
      var payload;
      console.log('notification received');
      payload = Push.getPayload(p);
      if (!_.isEmpty(payload)) {
        return Push.handlePayload(payload);
      }
    });
    return Storage.login('get').then(function(value) {
      if (_.isNull(value)) {
        return App.navigate('setup', {}, {
          animate: false,
          back: false
        });
      } else {
        return App.navigate('dashboard', {}, {
          animate: false,
          back: false
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
          controller: 'setup_passwordCtr',
          resolve: {
            HospitalData: function($q, Storage) {
              var defer;
              defer = $q.defer();
              Storage.hospital_data('get').then(function(data) {
                return defer.resolve(data);
              });
              return defer.promise;
            },
            RefcodeData: function($q, Storage) {
              var defer;
              defer = $q.defer();
              Storage.setData('refcode', 'get').then(function(data) {
                return defer.resolve(data);
              });
              return defer.promise;
            }
          }
        }
      }
    }).state('main_login', {
      url: '/main_login',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/authentication-view/Main-Screen-login.html',
          controller: 'main_loginCtr'
        }
      }
    }).state('setup', {
      url: '/setup',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/authentication-view/main-screen.html',
          controller: 'setupCtr'
        }
      }
    }).state('reset_password', {
      url: '/reset_password',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/authentication-view/reset-password.html',
          controller: 'setup_passwordCtr',
          resolve: {
            HospitalData: function($q, Storage) {
              var defer;
              defer = $q.defer();
              Storage.setData('hospital_details', 'get').then(function(data) {
                return defer.resolve(data);
              });
              return defer.promise;
            },
            RefcodeData: function($q, Storage) {
              var defer;
              defer = $q.defer();
              Storage.setData('refcode', 'get').then(function(data) {
                return defer.resolve(data);
              });
              return defer.promise;
            }
          }
        }
      }
    });
  }
]);
