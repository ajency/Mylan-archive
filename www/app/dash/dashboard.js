angular.module('PatientApp.dashboard', []).controller('DashboardCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', function($scope, App, Storage, QuestionAPI) {
    return $scope.view = {
      title: 'C-weight',
      data: [],
      navigate: function() {
        return App.navigate("questionnaire");
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('dashboard', {
      url: '/dashboard',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/dashboard/dashboard.html',
          controller: 'DashboardCtrl'
        }
      }
    });
  }
]);
