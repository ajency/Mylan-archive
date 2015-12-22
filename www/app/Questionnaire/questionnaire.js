angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', '$window', 'Storage', 'CToast', 'CSpinner', '$q', '$timeout', '$ionicPlatform', function($scope, App, QuestionAPI, $stateParams, $window, Storage, CToast, CSpinner, $q, $timeout, $ionicPlatform) {
    var onDeviceBack;
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
      flag: true,
      readonly: true,
      variables: function() {
        this.descriptiveAnswer = '';
        this.singleChoiceValue = '';
        return this.val_answerValue = {};
      },
      getQuestion: function() {
        this.display = 'loader';
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            _this.refcode = refcode;
            return Storage.setData('patientData', 'get');
          };
        })(this)).then((function(_this) {
          return function(patientData) {
            var options, responseId;
            _this.respStatus = $stateParams.respStatus;
            if (_this.respStatus === 'noValue') {
              responseId = '';
            } else {
              responseId = $stateParams.respStatus;
            }
            options = {
              "responseId": responseId,
              "questionnaireId": patientData.id,
              "patientId": _this.refcode
            };
            return QuestionAPI.getQuestion(options).then(function(data) {
              console.log('inside then');
              console.log(data);
              _this.data = data.result;
              _this.pastAnswer();
              Storage.setData('responseId', 'set', data.result.responseId);
              return _this.display = 'noError';
            }, function(error) {
              _this.display = 'error';
              return _this.errorType = error;
            });
          };
        })(this));
      },
      init: function() {
        return this.getQuestion();
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
              console.log('******next question******');
              console.log(data);
              _this.variables();
              _this.data = [];
              _this.data = data.result;
              _this.readonly = true;
              if (!_.isEmpty(_this.data.hasAnswer)) {
                _this.hasAnswerShow();
                _this.readonly = _this.data.editable;
              }
              _this.pastAnswer();
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
              "value": valueInput[0].toString()
            };
            this.loadNextQuestion(options);
          }
        }
        if (this.data.questionType === 'multi-choice') {
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
      loadPrevQuestion: function(param) {
        return Storage.setData('responseId', 'get').then((function(_this) {
          return function(responseId) {
            CSpinner.show('', 'Please wait..');
            param.responseId = responseId;
            return QuestionAPI.getPrevQuest(param).then(function(data) {
              console.log('previous data');
              console.log(_this.data);
              _this.variables();
              _this.data = [];
              _this.data = data.result;
              _this.readonly = _this.data.editable;
              _this.pastAnswer();
              if (!_.isEmpty(_this.data.hasAnswer)) {
                _this.hasAnswerShow();
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
      prevQuestion: function() {
        var optionId, options, selectedvalue, valueInput;
        if (this.data.questionType === 'single-choice') {
          options = {
            "questionId": this.data.questionId,
            "options": this.singleChoiceValue === '' ? [] : [this.singleChoiceValue],
            "value": ""
          };
          this.loadPrevQuestion(options);
        }
        if (this.data.questionType === 'multi-choice') {
          selectedvalue = [];
          _.each(this.data.options, function(opt) {
            if (opt.checked === true) {
              return selectedvalue.push(opt.id);
            }
          });
          options = {
            "questionId": this.data.questionId,
            "options": selectedvalue === [] ? [] : selectedvalue,
            "value": ""
          };
          this.loadPrevQuestion(options);
        }
        if (this.data.questionType === 'descriptive') {
          options = {
            "questionId": this.data.questionId,
            "options": [],
            "value": this.descriptiveAnswer === '' ? '' : this.descriptiveAnswer
          };
          this.loadPrevQuestion(options);
        }
        if (this.data.questionType === 'input') {
          valueInput = [];
          optionId = [];
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
            "options": optionId === [] ? [] : [optionId[0]],
            "value": valueInput === [] ? [] : valueInput[0].toString()
          };
          return this.loadPrevQuestion(options);
        }
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
      pastAnswer: function() {
        var ObjId, optionSelectedArray, pluckId, previousAns, sortedArray;
        previousAns = this.data.previousQuestionnaireAnswer;
        if (!_.isEmpty(previousAns)) {
          if (this.data.questionType === 'input') {
            if (!_.isEmpty(previousAns.optionId[0])) {
              ObjId = _.findWhere(this.data.options, {
                id: previousAns.optionId[0]
              });
              ObjId.option;
              this.data.previousQuestionnaireAnswer['label'] = ObjId.option;
            }
          }
          if (this.data.questionType === 'single-choice' || this.data.questionType === 'multi-choice') {
            console.log('have an');
            optionSelectedArray = [];
            sortedArray = _.sortBy(this.data.options, 'score');
            pluckId = _.pluck(sortedArray, 'id');
            _.each(previousAns.optionId, (function(_this) {
              return function(value) {
                var a;
                a = _.indexOf(pluckId, value);
                if (a !== -1) {
                  a++;
                  return optionSelectedArray.push(a);
                }
              };
            })(this));
            this.data.previousQuestionnaireAnswer['label'] = optionSelectedArray.toString();
          }
          return this.data.previousQuestionnaireAnswer.date = moment(previousAns.date.iso).format('MMMM Do YYYY');
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
          return this.val_answerValue[ObjId.option] = parseInt(this.data.hasAnswer.value);
        }
      }
    };
    onDeviceBack = function() {
      if ($scope.view.data.previous === false || _.isElement($scope.view.data)) {
        return App.navigate('dashboard', {}, {
          animate: false,
          back: false
        });
      } else {
        return $scope.view.prevQuestion();
      }
    };
    $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      return $scope.view.reInit();
    });
    $scope.$on('$ionicView.enter', function() {
      return $ionicPlatform.onHardwareBackButton(onDeviceBack);
    });
    return $scope.$on('$ionicView.leave', function() {
      return $ionicPlatform.offHardwareBackButton(onDeviceBack);
    });
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('questionnaire', {
      url: '/questionnaire:respStatus',
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
