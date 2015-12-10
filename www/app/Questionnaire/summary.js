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
      init: function() {
        return this.getSummary();
      },
      submitSummary: function() {
        var options;
        options = {
          quizID: $stateParams.quizID
        };
        return QuestionAPI.submitSummary(options).then((function(_this) {
          return function(data) {
            Storage.getNextQuestion('set', 1);
            Storage.quizDetails('remove');
            return App.navigate('dashboard', {}, {
              animate: false,
              back: false
            });
          };
        })(this), (function(_this) {
          return function(error) {
            return console.log('err');
          };
        })(this));
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
      url: '/summary',
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
