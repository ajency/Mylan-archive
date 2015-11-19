angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', '$window', 'Storage', function($scope, App, QuestionAPI, $stateParams, $window, Storage) {
    $scope.view = {
      pastAnswerDiv: 0,
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
        var inputs, options;
        inputs = document.getElementsByTagName('input');
        console.log('***************************');
        console.log(inputs[0].value);
        console.log('***************************');
        console.log(inputs[0].type);
        console.log(this.data.option);
        console.log('nextt questt');
        console.log(this.go);
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
      },
      showDiv: function() {
        return this.pastAnswerDiv = 1;
      },
      hideDiv: function() {
        return this.pastAnswerDiv = 0;
      },
      reInit: function() {
        return this.pastAnswerDiv = 0;
      }
    };
    return $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      return $scope.view.reInit();
    });
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
