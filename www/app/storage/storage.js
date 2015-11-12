angular.module('PatientApp.storage', []).factory('Storage', [
  function() {
    var Storage;
    Storage = {};
    Storage.setup = function(action) {
      switch (action) {
        case 'set':
          return localforage.setItem('app_setup_done', true);
        case 'get':
          return localforage.getItem('app_setup_done');
      }
    };
    Storage.login = function(action) {
      switch (action) {
        case 'set':
          return localforage.setItem('logged', true);
        case 'get':
          return localforage.getItem('logged');
      }
    };
    Storage.quizDetails = function(action, params) {
      switch (action) {
        case 'set':
          return localforage.setItem('quizDetail', params);
        case 'get':
          return localforage.getItem('quizDetail');
      }
    };
    return Storage;
  }
]);
