angular.module('PatientApp.init').controller('setupCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', 'AuthAPI', function($scope, App, Storage, $ionicLoading, AuthAPI) {
    return $scope.view = {
      refcode: '',
      emptyfield: '',
      deviceOS: '',
      deviceType: '',
      accessType: '',
      deviceUUID: '',
      verifyRefCode: function() {
        console.log(this.refcode);
        console.log(_.isEmpty(this.refcode));
        if (this.refcode === '' || _.isUndefined(this.refcode)) {
          return this.emptyfield = "Please Enter Valid Reference Code";
        } else {
          this.deviceUUID = App.deviceUUID();
          Storage.refcode('set', this.refcode);
          return App.navigate("setup_password");
        }
      },
      tologin: function() {
        return App.navigate("main_login");
      },
      forgetRefcode: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/error-view/Error-Screen-2.html',
          hideOnStateChange: true
        });
      },
      HelpRefcode: function() {
        return $ionicLoading.show({
          scope: $scope,
          templateUrl: 'views/error-view/RefCode-help-1.html',
          hideOnStateChange: true
        });
      },
      hide: function() {
        $ionicLoading.hide();
        return {
          hideOnStateChange: false
        };
      },
      clear: function() {
        return this.emptyfield = "";
      }
    };
  }
]);
