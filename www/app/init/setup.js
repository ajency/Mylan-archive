angular.module('PatientApp.init').controller('setupCtr', [
  '$scope', 'App', 'Storage', '$ionicLoading', function($scope, App, Storage, $ionicLoading) {
    return $scope.view = {
      refcode: '',
      emptyfield: '',
      verifyRefCode: function() {
        console.log(this.refcode);
        console.log(_.isEmpty(this.refcode));
        if (this.refcode === '' || _.isUndefined(this.refcode)) {
          return this.emptyfield = "Please Enter Valid Refrence Code";
        } else {
          return App.navigate("setup_password");
        }
      },
      tologin: function() {
        return Storage.setup('get').then(function(value) {
          var goto;
          goto = _.isNull(value) ? "setup" : "main_login";
          return App.navigate(goto);
        });
      },
      forgetRefcode: function() {
        $ionicLoading.show;
        return {
          scope: $scope,
          templateUrl: 'views/error-view/Error-Screen-2.html',
          hideOnStateChange: true
        };
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
