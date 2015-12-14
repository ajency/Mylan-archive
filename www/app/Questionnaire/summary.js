angular.module('PatientApp.Quest').controller('SummaryCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', 'Storage', function($scope, App, QuestionAPI, $stateParams, Storage) {
    return $scope.view = {
      title: 'C-weight',
      data: [],
      go: '',
      response: '',
      display: 'loader',
      getSummary: function() {
        this.display = 'noError';
        this.summary = Storage.summary('get');
        console.log('---summary---');
        console.log(this.summary);
        this.data = this.summary.summary;
        return this.responseId = this.summary.responseId;
      },
      getSummaryApi: function() {
        var param;
        param = {
          'responseId': $stateParams.summary
        };
        this.display = 'loader';
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
        this.summarytype = $stateParams.summary;
        if (this.summarytype === 'set') {
          return this.getSummary();
        } else {
          return this.getSummaryApi();
        }
      },
      submitSummary: function() {
        var param;
        param = {
          responseId: this.responseId
        };
        QuestionAPI.submitSummary(param).then((function(_this) {
          return function(data) {
            console.log('data');
            return console.log('succ submiteed');
          };
        })(this), (function(_this) {
          return function(error) {
            console.log('error');
            return console.log(error);
          };
        })(this));
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
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.getSummaryApi();
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
