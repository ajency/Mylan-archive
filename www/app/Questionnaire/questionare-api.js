angular.module('PatientApp.Quest').factory('QuestionAPI', [
  '$q', '$http', 'App', function($q, $http, App, $stateParams) {
    var QuestionAPI;
    QuestionAPI = {};
    QuestionAPI.getQuestion = function(opts) {
      var data, defer, params;
      defer = $q.defer();
      params = {
        "userdId": '55',
        "quizID": opts.quizID
      };
      data = {
        questionId: '112',
        questionType: 'mcq',
        questionTittle: 'which Statement best describes your pain',
        option: {
          0: {
            id: '1',
            answer: 'No Pain'
          },
          1: {
            id: '2',
            answer: 'Pain present but not needed for pain killer'
          },
          2: {
            id: '3',
            answer: 'Pain present, and i take ocassional pain releiving medication'
          }
        },
        pastAnswer: 'Pain present, and i take ocassional pain releiving medication',
        submitedDate: '5-11-2015'
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
      data = 'a';
      console.log('***********');
      console.log(params);
      defer.resolve(data);
      return defer.promise;
    };
    return QuestionAPI;
  }
]);
