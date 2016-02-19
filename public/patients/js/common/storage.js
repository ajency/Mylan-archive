angular.module('angularApp.storage', []).factory('Storage', [
  function() {
    var Storage, questionnaireData, startQuestion, summaryData;
    Storage = {};
    summaryData = {};
    questionnaireData = {};
    startQuestion = {};
    Storage.summary = function(action, data) {
      switch (action) {
        case 'set':
          return summaryData = data;
        case 'get':
          return summaryData;
      }
    };
    Storage.questionnaire = function(action, data) {
      switch (action) {
        case 'set':
          return questionnaireData = data;
        case 'get':
          return questionnaireData;
      }
    };
    Storage.startQuestionnaire = function(action, data) {
      switch (action) {
        case 'set':
          return startQuestion = data;
        case 'get':
          return startQuestion;
      }
    };
    return Storage;
  }
]);
