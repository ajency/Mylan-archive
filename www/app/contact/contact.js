(function() {
  angular.module('PatientApp.contact', []).controller('contactCtrl', [
    '$scope', 'App', 'Storage', function($scope, App, Storage) {
      return $scope.view = {
        pastAnswerDiv: 0,
        hospitalDetails: null,
        init: function() {
          console.log("contact init");
          return Storage.setData('hospital_details', 'get').then((function(_this) {
            return function(data) {
              _this.hospitalDetails = data;
              console.log("view", _this);
              return $scope.$apply(function() {
                return {};
              });
            };
          })(this));
        },
        call: function() {
          return App.callUs(this.hospitalDetails.phone);
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

}).call(this);
