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
      descriptiveAnswer: '',
      variables: function() {
        this.descriptiveAnswer = '';
        this.singleChoiceValue = '';
        return this.val_answerValue = {};
      },
      getLocal: function() {
        var defer;
        defer = $q.defer();
        Storage.getNextQuestion('get').then(function(details) {
          return defer.resolve(details);
        });
        return defer.promise;
      },
      getQuestion: function(questNo) {
        this.display = 'loader';
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            return Storage.setData('patientData', 'get').then(function(patientData) {
              var options;
              _this.patientId = patientData.patient_id;
              options = {
                "responseId": '',
                "questionnaireId": patientData.questionnaire.id,
                "patientId": refcode
              };
              return QuestionAPI.getQuestion(options).then(function(data) {
                console.log('inside then');
                console.log(data);
                _this.data = data.result;
                Storage.setData('responseId', 'set', data.result.responseId);
                return _this.display = 'noError';
              }, function(error) {
                _this.display = 'error';
                return _this.errorType = error;
              });
            });
          };
        })(this));
      },
      init: function() {
        this.data = '';
        return this.getQuestion();
      },
      navigate: function() {
        return App.navigate('summary', {
          quizID: 111
        });
      },
      loadNextQuestion: function(param) {
        return Storage.setData('responseId', 'get').then((function(_this) {
          return function(responseId) {
            CSpinner.show('', 'Please wait..');
            param.responseId = responseId;
            return QuestionAPI.saveAnswer(param).then(function(data) {
              var summary;
              App.resize();
              CToast.show('Your answer is saved');
              console.log('******');
              console.log('next question');
              console.log(data);
              _this.variables();
              _this.data = [];
              _this.data = data.result;
              console.log('---loadNextQuestion---');
              console.log(_this.data.hasAnswer);
              if (!_.isEmpty(_this.data.hasAnswer)) {
                console.log('not emty hasAnswer');
                _this.hasAnswerShow();
              }
              if (!_.isUndefined(_this.data.status)) {
                summary = {};
                summary['summary'] = _this.data.summary;
                summary['responseId'] = responseId;
                Storage.summary('set', summary);
                App.navigate('summary', {
                  summary: 'set'
                });
              }
              return _this.display = 'noError';
            }, function(error) {
              console.log('inside save error');
              console.log(error);
              if (error === 'offline') {
                return CToast.showLongBottom('Check net connection,answer not saved');
              } else {
                return CToast.show('Error in saving answer,try again');
              }
            })["finally"](function() {
              return CSpinner.hide();
            });
          };
        })(this));
      },
      nextQuestion: function() {
        var error, optionId, options, selectedvalue, sizeOfField, sizeOfTestboxAns, valueInput;
        if (this.data.questionType === 'single-choice') {
          if (this.singleChoiceValue === '') {
            CToast.show('Please select atleast one answer');
          } else {
            options = {
              "questionId": this.data.questionId,
              "options": [this.singleChoiceValue],
              "value": ""
            };
            this.loadNextQuestion(options);
          }
        }
        if (this.data.questionType === 'input') {
          error = 0;
          sizeOfField = _.size(this.data.options);
          sizeOfTestboxAns = _.size(this.val_answerValue);
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
            CToast.show('Please enter the values');
          } else {
            valueInput = [];
            optionId = [];
            console.log('uuuu0');
            console.log(this.val_answerValue);
            console.log('uuuu0');
            console.log(this.data.options);
            _.each(this.data.options, (function(_this) {
              return function(opt) {
                var a;
                a = _this.val_answerValue[opt.option];
                if (!_.isUndefined(a) && a !== '') {
                  valueInput.push(a);
                  return optionId.push(opt.id);
                }
              };
            })(this));
            options = {
              "questionId": this.data.questionId,
              "options": [optionId[0]],
              "value": valueInput[0]
            };
            this.loadNextQuestion(options);
          }
        }
        if (this.data.questionType === 'multi-choice') {
          console.log('------multi-choice optionss -----');
          console.log(this.data.options);
          if (!_.contains(_.pluck(this.data.options, 'checked'), true)) {
            CToast.show('Please select your answer');
          } else {
            selectedvalue = [];
            _.each(this.data.options, function(opt) {
              if (opt.checked === true) {
                return selectedvalue.push(opt.id);
              }
            });
          }
          console.log('selectedvalue');
          console.log(selectedvalue);
          options = {
            "questionId": this.data.questionId,
            "options": selectedvalue,
            "value": ""
          };
          this.loadNextQuestion(options);
        }
        if (this.data.questionType === 'descriptive') {
          if (this.descriptiveAnswer === '') {
            return CToast.show('Please Fill in the following');
          } else {
            options = {
              "questionId": this.data.questionId,
              "options": [],
              "value": this.descriptiveAnswer
            };
            return this.loadNextQuestion(options);
          }
        }
      },
      prevQuestion: function() {
        CSpinner.show('', 'Please wait..');
        return Storage.setData('responseId', 'get').then((function(_this) {
          return function(responseId) {
            var param;
            param = {
              "responseId": responseId,
              "questionId": _this.data.questionId,
              "options": [],
              "value": ""
            };
            return QuestionAPI.getPrevQuest(param).then(function(data) {
              var ObjId;
              console.log('previous data');
              console.log(data);
              _this.variables();
              _this.data = [];
              _this.data = data.result;
              if (_this.data.questionType === 'descriptive') {
                _this.descriptiveAnswer = _this.data.hasAnswer.value;
              }
              if (_this.data.questionType === 'single-choice') {
                _this.singleChoiceValue = _this.data.hasAnswer.option[0];
              }
              if (_this.data.questionType === 'multi-choice') {
                _.each(_this.data.options, function(value) {
                  if (_.contains(_this.data.hasAnswer.option, value.id)) {
                    return value['checked'] = true;
                  }
                });
              }
              if (_this.data.questionType === 'input') {
                ObjId = _.findWhere(_this.data.options, {
                  id: _this.data.hasAnswer.option[0]
                });
                console.log('objjj id');
                console.log(ObjId);
                console.log('valAnswer1');
                console.log(_this.val_answerValue);
                _this.val_answerValue[ObjId.option] = _this.data.hasAnswer.value;
                console.log('valAnswer2');
                console.log(_this.val_answerValue);
              }
              return console.log(_this.data);
            }, function(error) {
              console.log(error);
              if (error === 'offline') {
                return CToast.showLongBottom('Check net connection,answer not saved');
              } else {
                return CToast.show('Error ,try again');
              }
            })["finally"](function() {
              return CSpinner.hide();
            });
          };
        })(this));
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
        this.display = 'loader';
        console.log('onTapToRetry');
        return this.getQuestion();
      },
      isEmpty: function(pastAnswerObject) {
        return _.isEmpty(pastAnswerObject);
      },
      pastDate: function(date) {
        return moment(date).format('MMMM Do YYYY');
      },
      pastAnswer: function(previousQuestionnaireAnswer, optionId) {
        var indexOf, optId;
        optId = _.pluck(optionId, 'id');
        indexOf = optId.indexOf(previousQuestionnaireAnswer);
        indexOf++;
        return indexOf;
      },
      pastAnswerLabel: function(optId) {
        var ObjId;
        if (!_.isEmpty(optId)) {
          ObjId = _.findWhere(this.data.options, {
            id: optId
          });
          return ObjId.option;
        }
      },
      hasAnswerShow: function() {
        var ObjId;
        if (this.data.questionType === 'descriptive') {
          this.descriptiveAnswer = this.data.hasAnswer.value;
        }
        if (this.data.questionType === 'single-choice') {
          this.singleChoiceValue = this.data.hasAnswer.option[0];
        }
        if (this.data.questionType === 'multi-choice') {
          _.each(this.data.options, (function(_this) {
            return function(value) {
              if (_.contains(_this.data.hasAnswer.option, value.id)) {
                return value['checked'] = true;
              }
            };
          })(this));
        }
        if (this.data.questionType === 'input') {
          ObjId = _.findWhere(this.data.options, {
            id: this.data.hasAnswer.option[0]
          });
          console.log('objjj id');
          console.log(ObjId);
          console.log('valAnswer1');
          console.log(this.val_answerValue);
          this.val_answerValue[ObjId.option] = this.data.hasAnswer.value;
          console.log('valAnswer2');
          return console.log(this.val_answerValue);
        }
      }
    };
    $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      return $scope.view.reInit();
    });
    return $scope.$on('$ionicView.afterEnter', function(event, viewData) {});
  }
]).controller('PastAnswerCtrl', [
  '$scope', function($scope) {
    var date, indexOf, optId;
    console.log('Request time');
    console.log($scope.view.data.previousQuestionnaireAnswer);
    optId = _.pluck($scope.view.data.options, 'id');
    console.log(optId);
    indexOf = optId.indexOf($scope.view.data.previousQuestionnaireAnswer.optionId[0]);
    indexOf++;
    $scope.view.data.lastOption = indexOf;
    date = $scope.view.data.previousQuestionnaireAnswer.date.iso;
    return $scope.view.data.submitedDate = moment(date).format('MMMM Do YYYY');
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
