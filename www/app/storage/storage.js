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
          localforage.setItem('quizDetail', params);
          break;
        case 'get':
          localforage.getItem('quizDetail');
          break;
        case 'remove':
          localforage.removeItem('quizDetail');
      }
      Storage.refcode = function(action, refcode) {};
      switch (action) {
        case 'set':
          return localforage.setItem('refcode', refcode);
        case 'get':
          return localforage.getItem('refcode');
      }
    };
    return Storage;
  }
]);
