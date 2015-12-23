angular.module('PatientApp.Quest').controller('SummaryCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', 'Storage', 'CToast', 'CSpinner', function($scope, App, QuestionAPI, $stateParams, Storage, CToast, CSpinner) {
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
        CSpinner.show('', 'Please wait..');
        param = {
          responseId: this.responseId
        };
        return QuestionAPI.submitSummary(param).then((function(_this) {
          return function(data) {
            console.log('data');
            console.log('succ submiteed');
            CToast.show('submiteed successfully ');
            return App.navigate('exit-questionnaire');
          };
        })(this), (function(_this) {
          return function(error) {
            console.log('error');
            console.log(error);
            return CToast.show('Error in submitting questionnarie');
          };
        })(this))["finally"](function() {
          return CSpinner.hide();
        });
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
          return App.navigate('questionnaire', {
            respStatus: 'lastQuestion'
          });
        }
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
