angular.module('PatientApp.Quest').factory('QuestionAPI', [
  '$q', '$http', 'App', '$stateParams', function($q, $http, App, $stateParams) {
    var QuestionAPI;
    QuestionAPI = {};
    QuestionAPI.get = function() {
      var params;
      console.log('stateparam');
      console.log($stateParams.quizID);
      params = {
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
      return params;
    };
    QuestionAPI.startQuiz = function() {
      var data, defer, params;
      defer = $q.defer();
      params = {
        userdId: '55'
      };
      data = defer.resolve('data.data.result');
      return defer.promise;
    };
    return QuestionAPI;
  }
]);
