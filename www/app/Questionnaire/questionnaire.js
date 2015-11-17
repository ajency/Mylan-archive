angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', '$window', 'Storage', function($scope, App, QuestionAPI, $stateParams, $window, Storage) {
    return $scope.view = {
      title: 'C-weight',
      data: [],
      go: 'no_pain',
      response: '',
      actionValue: {},
      getQuestion: function() {
        var options;
        Storage.login('get').then(function(value) {
          console.log('*****************');
          return console.log(value);
        });
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
        console.log('nextt questt');
        return console.log(this.go);
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
