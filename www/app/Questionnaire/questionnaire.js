angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', '$window', 'Storage', 'CToast', 'CSpinner', function($scope, App, QuestionAPI, $stateParams, $window, Storage, CToast, CSpinner) {
    $scope.view = {
      pastAnswerDiv: 0,
      title: 'C-weight',
      data: [],
      go: '',
      response: '',
      actionValue: {},
      errorType: 'No net connection',
      display: 'noError',
      getQuestion: function(questNo) {
        var options;
        options = {
          quizID: $stateParams.quizID,
          questNo: questNo
        };
        return QuestionAPI.getQuestion(options).then((function(_this) {
          return function(data) {
            return Storage.getNextQuestion('get').then(function(value) {
              if (value === 1) {
                data.questionType = 'mcq';
              } else if (value === 2) {
                data.questionType = 'scq';
                data.questionTittle = 'Has Your weight changed in the past month ?';
                data.option = {
                  0: {
                    id: '1',
                    answer: 'No pain',
                    value: 'no_pain',
                    checked: false
                  },
                  1: {
                    id: '2',
                    answer: 'Pain present but not needed for pain killer',
                    value: 'pain_present',
                    checked: false
                  }
                };
              } else {
                data.questionType = 'descr';
              }
              return _this.data = data;
            });
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
        this.data = '';
        Storage.getNextQuestion('get').then(function(value) {});
        return this.getQuestion();
      },
      navigate: function() {
        return Storage.getNextQuestion('get').then(function(value) {
          value++;
          if (value === 4) {
            return App.navigate('summary', {
              quizID: 111
            });
          } else {
            Storage.getNextQuestion('set', value);
            return $window.location.reload();
          }
        });
      },
      nextQuestion: function() {
        var error, sizeOfField, sizeOfTestboxAns;
        if (this.data.questionType === 'descr') {
          error = 0;
          sizeOfField = _.size(this.data.fields);
          sizeOfTestboxAns = _.size(this.val_answerValue);
          console.log('******----******');
          console.log(sizeOfTestboxAns);
          if (sizeOfTestboxAns === 0) {
            error = 1;
          } else {
            _.each(this.val_answerValue, function(value) {
              if (value === null) {
                return error = 1;
              }
            });
          }
          if (error === 1) {
            return CToast.show('Please enter the values');
          } else {
            return this.navigate();
          }
        } else if (this.data.questionType === 'scq') {
          if (this.go === '') {
            return CToast.show('Please select your answer');
          } else {
            return this.navigate();
          }
        } else if (this.data.questionType === 'mcq') {
          if (!_.contains(_.pluck(this.data.option, 'checked'), true)) {
            return CToast.show('Please select your answer');
          } else {
            return this.navigate();
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
        this.data = [];
        this.pastAnswerDiv = 0;
        return this.go = '';
      },
      onTapToRetry: function() {
        return console.log('onTapToRetry');
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
      cache: false,
      views: {
        "QuestionContent": {
          templateUrl: 'views/questionnaire/question.html',
          controller: 'questionnaireCtr'
        }
      }
    });
  }
]);
