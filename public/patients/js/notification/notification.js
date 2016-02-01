angular.module('angularApp.notification', []).controller('notifyCtrl', [
  '$scope', 'App', function($scope, App) {
    return $scope.view = {
      data: [],
      display: 'loader',
      init: function() {},
      seenNotify: function(id) {},
      onTapToRetry: function() {}
    };
  }
]);
