angular.module('PatientApp.Quest').factory('QuestionAPI', [
  '$q', '$http', 'App', function($q, $http, App, $stateParams) {
    var QuestionAPI, actionMode;
    QuestionAPI = {};
    actionMode = {};
    QuestionAPI.getQuestion = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('startQuestionnaire', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.saveAnswer = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getNextQuestion', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.getSummary = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getSummary', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.submitSummary = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('submitQuestionnaire', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
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
    QuestionAPI.getNextQuest = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getNextQuestion', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.getPrevQuest = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('getPreviousQuestion', param).then(function(data) {
        return defer.resolve(data);
      }, (function(_this) {
        return function(error) {
          return defer.reject(error);
        };
      })(this));
      return defer.promise;
    };
    QuestionAPI.getFirstQuest = function(param) {
      var defer;
      defer = $q.defer();
      App.SendParseRequest('goToFirstQuestion', param).then(function(data) {
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
