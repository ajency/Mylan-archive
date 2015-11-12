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
    Storage.refcode = function(action, ref) {
      switch (action) {
        case 'set':
          return localforage.setItem('refcode', ref);
        case 'get':
          return localforage.getItem('refcode');
      }
    };
    return Storage;
  }
]);
