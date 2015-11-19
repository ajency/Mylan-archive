angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', '$window', 'Storage', function($scope, App, QuestionAPI, $stateParams, $window, Storage) {
    $scope.view = {
      pastAnswerDiv: 0,
      title: 'C-weight',
      data: [],
      go: '',
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
        var error, sizeOfField, sizeOfTestboxAns;
        if (this.data.questionType === 'descr') {
          error = 0;
          sizeOfField = _.size(this.data.fields);
          sizeOfTestboxAns = _.size(this.val_answerValue);
          if (sizeOfTestboxAns !== sizeOfField) {
            error = 1;
          } else {
            _.each(this.val_answerValue, function(value) {
              if (value === null) {
                return error = 1;
              }
            });
          }
          if (error === 1) {
            return console.log('please enter all the values');
          } else {
            return App.navigate('summary', {
              quizID: this.response.quizID
            });
          }
        } else if (this.data.questionType === 'scq') {
          if (this.go === '') {
            return console.log('please select value');
          } else {
            return App.navigate('summary', {
              quizID: this.response.quizID
            });
          }
        } else if (this.data.questionType === 'mcq') {
          if (!_.contains(_.pluck(this.data.option, 'checked'), true)) {
            return console.log('please select value');
          } else {
            return App.navigate('summary', {
              quizID: this.response.quizID
            });
          }
        }
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
        this.pastAnswerDiv = 0;
        return this.go = '';
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
