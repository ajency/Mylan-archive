angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'Storage', 'QuestionAPI', '$stateParams', '$window', function($scope, App, Storage, QuestionAPI, $stateParams, $window) {
    return $scope.view = {
      title: 'C-weight',
      data: [],
      go: '',
      response: '',
      actionValue: {},
      getQuestion: function() {
        var options;
        options = {
          quizID: $stateParams.quizID
        };
        return QuestionAPI.getQuestion(options).then((function(_this) {
          return function(data) {
            return _this.data = data;
          };
        })(this), (function(_this) {
          return function(error) {
            return console.log('err');
          };
        })(this));
      },
      getPrevQuestion: function() {
        var options;
        options = {
          quizID: $stateParams.quizID,
          questionId: this.actionValue.questionId
        };
        return QuestionAPI.getQuestion(options).then((function(_this) {
          return function(data) {
            return _this.data = data;
          };
        })(this), (function(_this) {
          return function(error) {
            return console.log('err');
          };
        })(this));
      },
      init: function() {
        console.log('init');
        this.actionValue = QuestionAPI.setAction('get');
        console.log(this.actionValue);
        if (_.isEmpty(this.actionValue) || this.actionValue.mode === 'next') {
          return this.getQuestion();
        } else {
          return this.getPrevQuestion();
        }
      },
      nextQuestion: function() {
        var options;
        options = {
          quizID: $stateParams.quizID,
          questionId: this.data.questionId,
          answerId: this.go,
          action: 'submitted'
        };
        return QuestionAPI.saveAnswer(options).then((function(_this) {
          return function(data) {
            var action, v;
            action = {
              questionId: _this.data.questionId,
              mode: 'next'
            };
            QuestionAPI.setAction('set', action);
            v = QuestionAPI.setAction('get');
            console.log(v);
            _this.response = data;
            if (_this.response.type === 'nextQuestion') {
              return $window.location.reload();
            } else {
              return App.navigate('summary', {
                quizID: _this.response.quizID
              });
            }
          };
        })(this), (function(_this) {
          return function(error) {
            return console.log('err');
          };
        })(this));
      },
      prevQuestion: function() {
        var action;
        action = {
          questionId: this.data.questionId,
          mode: 'prev'
        };
        QuestionAPI.setAction('set', action);
        return this.init();
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('questionnaire', {
      url: '/questionnaire:quizID',
      parent: 'parent-questionnaire',
      views: {
        "QuestionContent": {
          templateUrl: 'views/questionnaire/question.html',
          controller: 'questionnaireCtr'
        }
      }
    });
  }
]);
