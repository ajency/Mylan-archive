angular.module('PatientApp.Quest').controller('SummaryCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', 'Storage', function($scope, App, QuestionAPI, $stateParams, Storage) {
    return $scope.view = {
      title: 'C-weight',
      data: [],
      go: '',
      response: '',
      getSummary: function() {
        this.data = Storage.summary('get');
        console.log('summmmm');
        return console.log(this.data);
      },
      getSummaryApi: function(param) {
        return QuestionAPI.getSummary(param).then((function(_this) {
          return function(data) {
            console.log('--getSummaryApi---');
            _this.data = data.result;
            console.log(_this.data);
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
        var param, summarytype;
        summarytype = $stateParams.summary;
        if (summarytype === 'set') {
          return this.getSummary();
        } else {
          param = {
            'responseId': $stateParams.summary
          };
          return this.getSummaryApi(param);
        }
      },
      submitSummary: function() {
        return ionic.Platform.exitApp();
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
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('summary', {
      url: '/summary:summary',
      parent: 'parent-questionnaire',
      views: {
        "QuestionContent": {
          templateUrl: 'views/questionnaire/summary.html',
          controller: 'SummaryCtr'
        }
      }
    });
  }
]);
