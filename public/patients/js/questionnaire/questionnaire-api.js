angular.module('angularApp.questionnaire').factory('QuestionAPI', [
  '$q', '$http', 'App', function($q, $http, App) {
    var QuestionAPI;
    QuestionAPI = {};
    QuestionAPI.getSummary = function(id) {
      var PARSE_HEADERS, PARSE_URL, defer, param, url;
      defer = $q.defer();
      PARSE_URL = 'https://api.parse.com/1/functions';
      url = PARSE_URL + '/getSummary';
      param = {
        'responseId': id
      };
      PARSE_HEADERS = {
        headers: {
          "X-Parse-Application-Id": 'MQiH2NRh0G6dG51fLaVbM0i7TnxqX2R1pKs5DLPA',
          "X-Parse-REST-API-KeY": 'I4yEHhjBd4e9x28MvmmEOiP7CzHCVXpJxHSu5Xva'
        }
      };
      $http.post(url, param, PARSE_HEADERS).then(function(data) {
        return defer.resolve(data.data);
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
    return QuestionAPI;
  }
]);
