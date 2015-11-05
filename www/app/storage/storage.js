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
    return Storage;
  }
]);
