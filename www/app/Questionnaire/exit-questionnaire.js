angular.module('PatientApp.Quest').controller('ExitQuestionnaireCtrl', [
  '$scope', 'App', 'Storage', 'QuestionAPI', 'DashboardAPI', '$ionicPlatform', function($scope, App, Storage, QuestionAPI, DashboardAPI, $ionicPlatform) {
    var deregisterExit, onDeviceBackExit;
    $scope.view = {
      hospitalData: '',
      phone: '',
      exit: function() {
        return ionic.Platform.exitApp();
      },
      init: function() {
        return Storage.setData('hospital_details', 'get').then((function(_this) {
          return function(data) {
            return _this.phone = data.phone;
          };
        })(this));
      },
      call: function() {
        return App.callUs(this.phone);
      }
    };
    onDeviceBackExit = function() {
      var count;
      switch (App.currentState) {
        case 'exit-questionnaire':
          return App.navigate("dashboard", {}, {
            animate: false,
            back: false
          });
        default:
          count = -1;
          return App.goBack(count);
      }
    };
    deregisterExit = null;
    $scope.$on('$ionicView.enter', function() {
      console.log('$ionicView.enter questionarie');
      return deregisterExit = $ionicPlatform.registerBackButtonAction(onDeviceBackExit, 1000);
    });
    return $scope.$on('$ionicView.leave', function() {
      console.log('$ionicView.leave exit ....');
      if (deregisterExit) {
        return deregisterExit();
      }
    });
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
