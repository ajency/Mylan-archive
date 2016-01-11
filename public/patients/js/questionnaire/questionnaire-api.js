angular.module('angularApp.questionnaire').factory('QuestionAPI', [
  '$q', '$http', 'App', function($q, $http, App) {
    var QuestionAPI;
    QuestionAPI = {};
    QuestionAPI.getSummary = function(options) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getSummary', options).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.getQuestion = function(options) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('startQuestionnaire', options).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.saveAnswer = function(options) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getNextQuestion', options).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.submitSummary = function(options) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('submitQuestionnaire', options).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.getPrevQuest = function(options) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getPreviousQuestion', options).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    return QuestionAPI;
  }
]);
