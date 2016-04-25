var app;

app = angular.module('angularApp', ['ngRoute', 'angularApp.dashboard', 'angularApp.questionnaire', 'angularApp.common', 'angularApp.notification', 'angularApp.storage', 'angularApp.Auth', 'angularApp.contact']).run([
  '$rootScope', 'App', function($rootScope, App) {
    return $rootScope.$on('$routeChangeSuccess', function(event, current, previous, rejection) {
      if (!_.isUndefined(current.$$route)) {
        App.currentState = current.$$route.controller;
      }
      if (!_.isUndefined(previous)) {
        if (!_.isUndefined(previous.$$route)) {
          return App.previousState = previous.$$route.controller;
        }
      }
    });
  }
]).config([
  '$routeProvider', function($routeProvider) {
    return $routeProvider.when('/dashboard', {
      url: '/dashboard',
      templateUrl: 'patients/views/dashboard.html',
      controller: 'dashboardController'
    }).when('/summary', {
      url: '/summary',
      templateUrl: 'patients/views/summary.html',
      controller: 'summaryController'
    }).when('/start-questionnaire', {
      url: '/start-questionnaire',
      templateUrl: 'patients/views/start-questionnaire.html',
      controller: 'StartQuestionnaireCtrl'
    }).when('/questionnaire', {
      url: '/questionnaire',
      templateUrl: 'patients/views/question.html',
      controller: 'questionnaireCtr'
    }).when('/notification', {
      url: '/notification',
      templateUrl: 'patients/views/notification.html',
      controller: 'notifyCtrl'
    }).when('/reset-password', {
      url: '/reset-password',
      templateUrl: 'patients/views/set-password.html',
      controller: 'setup_passwordCtr'
    }).when('/contact', {
      url: '/contact',
      templateUrl: 'patients/views/contact.html',
      controller: 'contactCtrl'
    }).otherwise({
      redirectTo: '/dashboard'
    });
  }
]);
