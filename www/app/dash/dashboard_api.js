angular.module('PatientApp.dashboard').factory('DashboardAPI', [
  '$q', '$http', 'App', '$stateParams', function($q, $http, App, $stateParams) {
    var DashboardAPI;
    DashboardAPI = {};
    DashboardAPI.get = function() {
      var params;
      params = {
        array: {
          0: {
            response_id: '101',
            date_time: '20-10-2015|15.30',
            status: 'Upcomming',
            action: '',
            quizId: '105'
          },
          1: {
            response_id: '102',
            date_time: '20-10-2015|15.30',
            status: 'Due',
            action: 'Start',
            quizId: '106'
          },
          2: {
            response_id: '103',
            date_time: '20-10-2015|15.30',
            status: 'Missed',
            action: '',
            quizId: '107'
          },
          3: {
            response_id: '104',
            date_time: '20-10-2015|15.30',
            status: 'Submitted',
            action: 'View',
            quizId: '108'
          },
          4: {
            response_id: '105',
            date_time: '20-10-2015|15.30',
            status: 'Submitted',
            action: 'View',
            quizId: '109'
          }
        }
      };
      return params;
    };
    return DashboardAPI;
  }
]);
