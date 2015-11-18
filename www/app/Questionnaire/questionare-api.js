angular.module('PatientApp.Quest').factory('QuestionAPI', [
  '$q', '$http', 'App', function($q, $http, App, $stateParams) {
    var QuestionAPI, actionMode;
    QuestionAPI = {};
    actionMode = {};
    QuestionAPI.getQuestion = function(opts) {
      var data, defer, params, questionId;
      defer = $q.defer();
      questionId = '';
      if (!_.isUndefined(opts.questionId)) {
        questionId = opts.questionId;
      }
      params = {
        "userdId": '55',
        "quizID": opts.quizID,
        "questionId": questionId
      };
      data = {
        questionId: '112',
        questionType: 'scq',
        questionTittle: 'which Statement best describes your pain',
        option: {
          0: {
            id: '1',
            answer: 'No Pain',
            value: 'no_pain',
            checked: true
          },
          1: {
            id: '2',
            answer: 'Pain present but not needed for pain killer',
            value: 'pain_present',
            checked: false
          },
          2: {
            id: '3',
            answer: 'Pain present, and i take ocassional pain releiving medication',
            value: 'take_medication',
            checked: false
          }
        },
        pastAnswer: 'Pain present, and i take ocassional pain releiving medication',
        submitedDate: '5-11-2015',
        previousAnswered: '1',
        previousQuestion: 'true'
      };
      defer.resolve(data);
      return defer.promise;
    };
    QuestionAPI.saveAnswer = function(opts) {
      var data, defer, params;
      defer = $q.defer();
      params = {
        "userdId": '55',
        "quizID": opts.quizID,
        "questionId": opts.questionId,
        "answerId": opts.answerId,
        "action": opts.action
      };
      data = {
        'type': 'summary',
        'quizID': '111'
      };
      defer.resolve(data);
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
            question: 'Which statement best describes your pain',
            answer: 'pain is present ,but not needed for pain killer'
          },
          1: {
            question: 'Which statement best describes your pain',
            answer: 'pain is present ,but not needed for pain killer'
          },
          2: {
            question: 'Which statement best describes your pain',
            answer: 'pain is present ,but not needed for pain killer'
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
    return QuestionAPI;
  }
]);
