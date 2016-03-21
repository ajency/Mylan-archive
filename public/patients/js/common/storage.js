angular.module('angularApp.storage', []).factory('Storage', [
  function() {
    var Storage, questStatus, questionnaireData, startQuestion, summaryData;
    Storage = {};
    summaryData = {};
    questionnaireData = {};
    startQuestion = {};
    questStatus = '';
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
    Storage.getQuestStatus = function(action, status) {
      switch (action) {
        case 'set':
          return questStatus = status;
        case 'get':
          return questStatus;
      }
    };
    return Storage;
  }
]);
