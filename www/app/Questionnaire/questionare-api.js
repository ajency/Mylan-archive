angular.module('PatientApp.Quest').factory('QuestionAPI', [
  '$q', '$http', 'App', function($q, $http, App, $stateParams) {
    var QuestionAPI, actionMode;
    QuestionAPI = {};
    actionMode = {};
    QuestionAPI.getQuestion = function(options) {
      var defer, param, url;
      defer = $q.defer();
      url = PARSE_URL + '/startQuestionnaire';
      param = options;
      App.sendRequest(url, param, PARSE_HEADERS).then(function(data) {
        return defer.resolve(data.data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.saveAnswer = function(options) {
      var defer, param, url;
      defer = $q.defer();
      url = PARSE_URL + '/getNextQuestion';
      param = options;
      App.sendRequest(url, param, PARSE_HEADERS).then(function(data) {
        return defer.resolve(data.data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.getSummary = function(opts) {
      var data, defer, params;
      defer = $q.defer();
      params = {
        "userdId": '55',
        "quizID": opts.quizID
      };
      data = {
        summary: {
          0: {
            question: 'What is your current Statement best describes your pain ?',
            answer: 'pain is present ,but not needed for pain killer'
          },
          1: {
            question: 'Has your weight changed in the past month ?',
            answer: 'No change'
          },
          2: {
            question: 'Has your weight changed in the past month ?',
            answer: 'No change'
          }
        }
      };
      defer.resolve(data);
      return defer.promise;
    };
    QuestionAPI.submitSummary = function(opts) {
      var data, defer, params;
      defer = $q.defer();
      params = {
        "userdId": '55',
        "quizID": opts.quizID
      };
      data = 'success';
      defer.resolve(data);
      return defer.promise;
    };
    QuestionAPI.setAction = function(action, data) {
      if (data == null) {
        data = {};
      }
      switch (action) {
        case 'set':
          return _.each(data, function(val, index) {
            return actionMode[index] = val;
          });
        case 'get':
          return actionMode;
      }
    };
    QuestionAPI.checkDueQuest = function(opts) {
      var data, defer, params;
      defer = $q.defer();
      params = {
        "userdId": '55',
        "quizID": opts.quizID
      };
      data = 'paused';
      defer.resolve(data);
      return defer.promise;
    };
    QuestionAPI.getNextQuest = function(options) {
      var defer, param, url;
      defer = $q.defer();
      url = PARSE_URL + '/getNextQuestion';
      param = options;
      App.sendRequest(url, param, PARSE_HEADERS).then(function(data) {
        return defer.resolve(data.data);
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
