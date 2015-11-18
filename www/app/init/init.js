angular.module('PatientApp.init', []).controller('InitCtrl', [
  'Storage', 'App', '$scope', 'QuestionAPI', '$q', function(Storage, App, $scope, QuestionAPI, $q) {
    return Storage.setup('get').then(function(value) {
      if (_.isNull(value)) {
        return App.navigate('setup', {}, {
          animate: false,
          back: false
        });
      } else {
        return Storage.login('get').then(function(value) {
          if (_.isNull(value)) {
            return App.navigate('main_login', {}, {
              animate: false,
              back: false
            });
          } else {
            return Storage.quizDetails('get').then(function(quizDetail) {
              if (_.isNull(quizDetail)) {
                return App.navigate('dashboard', {}, {
                  animate: false,
                  back: false
                });
              } else {
                console.log('inside else');
                return QuestionAPI.checkDueQuest(quizDetail.quizID).then((function(_this) {
                  return function(data) {
                    if (data === 'paused') {
                      return App.navigate('questionnaire', {
                        quizID: quizDetail.quizID
                      }, {
                        animate: false,
                        back: false
                      });
                    } else {
                      return App.navigate('dashboard', {}, {
                        animate: false,
                        back: false
                      });
                    }
                  };
                })(this), (function(_this) {
                  return function(error) {
                    return console.log('err');
                  };
                })(this));
              }
            });
          }
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
      controller: 'main_loginCtr',
      resolve: {
        refrencecodeValue: function($q, Storage) {
          var defer;
          defer = $q.defer();
          Storage.refcode('get').then(function(details) {
            return defer.resolve(details);
          });
          return defer.promise;
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
    });
  }
]);
