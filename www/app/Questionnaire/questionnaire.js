angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', '$window', 'Storage', 'CToast', 'CSpinner', '$q', '$timeout', function($scope, App, QuestionAPI, $stateParams, $window, Storage, CToast, CSpinner, $q, $timeout) {
    $scope.view = {
      pastAnswerDiv: 0,
      title: 'C-weight',
      data: [],
      singleChoiceValue: '',
      response: '',
      actionValue: {},
      errorType: '',
      display: 'loader',
      infoBox: true,
      getLocal: function() {
        var defer;
        defer = $q.defer();
        Storage.getNextQuestion('get').then(function(details) {
          return defer.resolve(details);
        });
        return defer.promise;
      },
      getQuestion: function(questNo) {
        this.display = 'noError';
        return Storage.setData('patientData', 'get').then((function(_this) {
          return function(patientData) {
            var options;
            _this.patientId = patientData.patient_id;
            options = {
              "responseId": '',
              "questionnaireId": patientData.questionnaire.id,
              "patientId": parseInt(patientData.patient_id)
            };
            return QuestionAPI.getQuestion(options).then(function(data) {
              console.log('inside then');
              console.log(data);
              _this.data = data.result;
              return _this.display = 'noError';
            }, function(error) {
              _this.display = 'error';
              return _this.errorType = error;
            });
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
        return this.getQuestion();
      },
      navigate: function() {
        return this.getLocal().then((function(_this) {
          return function(result) {
            var value;
            value = result;
            value = parseInt(value);
            value++;
            Storage.getNextQuestion('set', value);
            if (value === 4) {
              CSpinner.hide();
              return App.navigate('summary', {
                quizID: 111
              });
            } else {
              Storage.getNextQuestion('set', value);
              return $timeout(function() {
                CSpinner.hide();
                return $window.location.reload();
              }, 500);
            }
          };
        })(this));
      },
      nextQuestion: function() {
        var options, selectedvalue;
        console.log('nextQuestion');
        console.log(this.data);
        selectedvalue = [];
        _.each(this.data.options, function(opt) {
          if (opt.checked === true) {
            return selectedvalue.push(opt.id);
          }
        });
        console.log('nextquestt');
        console.log(selectedvalue);
        if (this.data.question.type === 'single-choice') {
          if (this.singleChoiceValue === '') {
            return CToast.show('Please select atleast one answer');
          } else {
            options = {
              "responseId": this.data.response,
              "patientId": this.patientId,
              "questionId": this.data.question.id,
              "options": [this.singleChoiceValue],
              "value": ""
            };
            Storage.setData('responseId', 'set', this.data.response);
            CSpinner.show('', 'Please wait..');
            return QuestionAPI.saveAnswer(options).then((function(_this) {
              return function(data) {
                var nextQuest;
                CToast.show('Your answer is saved');
                _this.display = 'loader';
                nextQuest = {
                  "questionnaireId": _this.data.id,
                  "questionIds": [_this.data.question.id],
                  "patientId": _this.patientId,
                  "responseId": _this.data.response
                };
                return QuestionAPI.getNextQuest(nextQuest);
              };
            })(this)).then((function(_this) {
              return function(data) {
                console.log('******');
                console.log('next question');
                console.log(data);
                _this.data = [];
                _this.data = data.result;
                return _this.display = 'noError';
              };
            })(this), (function(_this) {
              return function(error) {
                console.log('inside save error');
                console.log(error);
                return CToast.show('Error in saving your answer');
              };
            })(this))["finally"](function() {
              return CSpinner.hide();
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
        this.data = [];
        this.pastAnswerDiv = 0;
        this.go = '';
        return this.display = 'loader';
      },
      onTapToRetry: function() {
        return console.log('onTapToRetry');
      }
    };
    $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      return $scope.view.reInit();
    });
    return $scope.$on('$ionicView.afterEnter', function(event, viewData) {});
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
