angular.module('PatientApp.init').controller('setupCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', function($scope, App, Storage, $ionicLoading) {
    return $scope.view = {
      refcode: '',
      verifyRefCode: function() {
        console.log(this.refcode);
        return App.navigate("setup_password");
      },
      tologin: function() {
        return Storage.setup('get').then(function(value) {
          var goto;
          goto = _.isNull(value) ? "setup" : "main_login";
          return App.navigate(goto);
        });
      },
      forgetRefcode: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/error-view/Error-Screen-2.html',
          hideOnStateChange: true
        });
      },
      hide: function() {
        return $ionicLoading.hide();
      }
    };
  }
]);
