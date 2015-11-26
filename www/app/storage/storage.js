(function() {
  angular.module('PatientApp.storage', []).factory('Storage', [
    function() {
      var Storage, fetchArray, fetchItem, ref, removeItem, setItem, storeArray, userInfo;
      Storage = {};
      ref = '';
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
            return localforage.setItem('nextQuestion', questionNo);
          case 'get':
            return localforage.getItem('nextQuestion');
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
      Storage.storageOperation = function(options, cb) {
        if (options == null) {
          options = {};
        }
        if (cb == null) {
          cb = {};
        }
        switch (action) {
          case 'set':
            if (_.isFunction(cb)) {
              return cb.call();
            } else {
              if (_.isArray(options)) {
                return storeArray(options);
              } else {
                return lsetItem(options.name, options.value);
              }
            }
            break;
          case 'get':
            if (_.isFunction(cb)) {
              return cb.call();
            } else {
              if (_.isArray(options.name)) {
                return fetchItem(options.name);
              } else {
                return fetchItem(options.name);
              }
            }
            break;
          case 'remove':
            if (_.isFunction(cb)) {
              return cb.call();
            } else {
              return removeItem(options);
            }
        }
      };
      setItem = function(name, value) {
        return localforage.setItem(name, value);
      };
      fetchItem = function(name) {
        return localforage.getItem(name);
      };
      storeArray = function(options) {
        return angular.forEach(options, function(option) {
          return setItem(option.name, option.value);
        });
      };
      fetchArray = function(names) {
        var data;
        data = [];
        angular.forEach(names, function(name) {
          return data.push(fetchItem(name));
        });
        return data;
      };
      removeItem = function(options) {
        if (_.isArray(options.name)) {
          return angular.forEach(options.name, function(name) {
            return localforage.removeItem(name);
          });
        } else {
          return localforage.removeItem(options.name);
        }
      };
      return Storage;
    }
  ]);

}).call(this);
