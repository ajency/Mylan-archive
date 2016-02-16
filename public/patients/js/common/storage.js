angular.module('angularApp.storage', []).factory('Storage', [
  function() {
    var Storage, questionnaireData, summaryData;
    Storage = {};
    summaryData = {};
    questionnaireData = {};
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
    return Storage;
  }
]);
