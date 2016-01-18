angular.module('PatientApp.Quest').controller('ExitQuestionnaireCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', function($scope, App, Storage, QuestionAPI, DashboardAPI) {
    return $scope.view = {
      hospitalData: '',
      phone: '',
      exit: function() {
        return ionic.Platform.exitApp();
      },
      init: function() {
        return Storage.setData('hospital_details', 'get').then((function(_this) {
          return function(data) {
            _this.phone = phone;
            return App.callUs(data.phone);
          };
        })(this));
      },
      call: function() {
        return App.callUs(this.phone);
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('exit-questionnaire', {
      url: '/exit-questionnaire',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/questionnaire/exit.html',
          controller: 'ExitQuestionnaireCtrl'
        }
      }
    });
  }
]);
