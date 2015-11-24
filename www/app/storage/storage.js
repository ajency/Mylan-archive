angular.module('PatientApp.storage', []).factory('Storage', [
  function() {
    var Storage, nextQuestion, ref, userInfo;
    Storage = {};
    ref = '';
    nextQuestion = 0;
    userInfo = {};
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
        case 'remove':
          return localforage.removeItem('quizDetail');
      }
    };
    Storage.refcode = function(action, refcode) {
      switch (action) {
        case 'set':
          return localforage.setItem('refcode', refcode);
        case 'get':
          return localforage.getItem('refcode');
      }
    };
    Storage.hospital_data = function(action, hospital_data) {
      switch (action) {
        case 'set':
          return localforage.setItem('hospital_details', hospital_data);
        case 'get':
          return localforage.getItem('hospital_details');
      }
    };
    Storage.user_data = function(action, user_data) {
      switch (action) {
        case 'set':
          return localforage.setItem('user_details', user_data);
        case 'get':
          return localforage.getItem('user_details');
      }
    };
    Storage.getNextQuestion = function(action, questionNo) {
      switch (action) {
        case 'set':
          return nextQuestion = questionNo;
        case 'get':
          return nextQuestion;
      }
    };
    Storage.setRefernce = function(action, param) {
      switch (action) {
        case 'set':
          return ref = param;
        case 'get':
          return ref;
      }
    };
    Storage.setHospitalData = function(action, data) {
      switch (action) {
        case 'set':
          return _.each(data, function(val, index) {
            return userInfo[index] = val;
          });
        case 'get':
          return userInfo;
      }
    };
    return Storage;
  }
]);
