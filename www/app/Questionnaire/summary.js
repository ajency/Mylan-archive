angular.module('PatientApp.Quest').controller('SummaryCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', 'Storage', 'CToast', 'CSpinner', '$ionicPlatform', function($scope, App, QuestionAPI, $stateParams, Storage, CToast, CSpinner, $ionicPlatform) {
    var deregister, onDeviceBackSummary;
    $scope.view = {
      title: 'C-weight',
      data: [],
      go: '',
      response: '',
      display: 'loader',
      hideButton: '',
      getSummaryApi: function() {
        var param;
        this.hideButton = App.previousState === 'dashboard' ? true : false;
        param = {
          'responseId': $stateParams.summary
        };
        this.display = 'loader';
        return QuestionAPI.getSummary(param).then((function(_this) {
          return function(data) {
            console.log('****name***');
            console.log(data);
            _this.data = data;
            _.each(_this.data, function(value) {
              var a;
              a = value.input;
              if (!_.isUndefined(a)) {
                return value['type'] = 'input';
              } else {
                return value['type'] = 'option';
              }
            });
            return _this.display = 'noError';
          };
        })(this), (function(_this) {
          return function(error) {
            _this.display = 'error';
            return _this.errorType = error;
          };
        })(this));
      },
      init: function() {
        return this.getSummaryApi();
      },
      submitSummary: function() {
        CToast.showLongBottom('Questionnaire cannot be submitted. This is a test app.');
        return App.navigate('exit-questionnaire');
      },
      prevQuestion: function() {
        var action, valueAction;
        valueAction = QuestionAPI.setAction('get');
        action = {
          questionId: valueAction.questionId,
          mode: 'prev'
        };
        QuestionAPI.setAction('set', action);
        return App.navigate('questionnaire', {
          quizID: $stateParams.quizID
        });
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.getSummaryApi();
      },
      back: function() {
        if (App.previousState === 'dashboard') {
          return App.navigate('dashboard');
        } else {
          return Storage.setData('responseId', 'set', $stateParams.summary).then(function() {
            return App.navigate('questionnaire', {
              respStatus: 'lastQuestion'
            });
          });
        }
      }
    };
    onDeviceBackSummary = function() {
      return $scope.view.back();
    };
    deregister = null;
    $scope.$on('$ionicView.enter', function() {
      console.log('$ionicView.enter.summary');
      return deregister = $ionicPlatform.registerBackButtonAction(onDeviceBackSummary, 1000);
    });
    return $scope.$on('$ionicView.leave', function() {
      console.log('$ionicView.enter.leave summary');
      if (deregister) {
        return deregister();
      }
    });
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('summary', {
      url: '/summary:summary',
      parent: 'main',
      cache: false,
      views: {
        "appContent": {
          templateUrl: 'views/questionnaire/summary.html',
          controller: 'SummaryCtr'
        }
      }
    });
  }
]);
