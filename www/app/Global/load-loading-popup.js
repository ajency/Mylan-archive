(function() {
  angular.module('PatientApp.Global').factory('LoadingPopup', [
    '$ionicLoading', function($ionicLoading) {
      var LoadingPopup;
      LoadingPopup = {
        showLoadingPopup: function(templateUrl) {
          return $ionicLoading.show({
            scope: $scope,
            templateUrl: templateUrl,
            hideOnStateChange: true
          });
        },
        hidePopup: function() {
          return $ionicLoading.hide();
        }
      };
      return LoadingPopup;
    }
  ]);

}).call(this);
