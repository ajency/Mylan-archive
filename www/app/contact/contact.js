angular.module('PatientApp.contact', []).controller('contactCtrl', [
  '$scope', 'App', 'Storage', function($scope, App, Storage) {
    return $scope.view = {
      pastAnswerDiv: 0,
      call: function() {
        return Storage.setData('hospital_details', 'get').then((function(_this) {
          return function(data) {
            return App.callUs(data.phone);
          };
        })(this));
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('contact', {
      url: '/contact',
      parent: 'main',
      views: {
        "appContent": {
          templateUrl: 'views/contact/contact.html',
          controller: 'contactCtrl'
        }
      }
    });
  }
]);
