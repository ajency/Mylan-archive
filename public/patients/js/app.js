var app;

app = angular.module('angularApp', ['ngRoute', 'angularApp.dashboard', 'angularApp.questionnaire', 'angularApp.common']).run([
  '$rootScope', 'App', function($rootScope, App) {
    return $rootScope.$on('$routeChangeSuccess', function(event, current, previous, rejection) {});
  }
]).config([
  '$routeProvider', function($routeProvider) {
    return $routeProvider.when('/dashboard', {
      url: '/dashboard',
      templateUrl: 'patients/views/dashboard.html',
      controller: 'dashboardController'
    }).when('/summary/:responseId', {
      url: '/summary',
      templateUrl: 'patients/views/summary.html',
      controller: 'summaryController'
    }).when('/start-questionnaire', {
      url: '/start-questionnaire',
      templateUrl: 'patients/views/start-questionnaire.html',
      controller: 'StartQuestionnaireCtrl'
    }).when('/questionnaire/:respStatus/:responseId', {
      url: '/questionnaire',
      templateUrl: 'patients/views/question.html',
      controller: 'questionnaireCtr'
    }).when('notification', {
      url: '/notification',
      templateUrl: 'patients/views/notify.html',
      controller: 'notifyCtrl'
    }).otherwise({
      redirectTo: '/dashboard'
    });
  }
]);
