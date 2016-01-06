angular.module('angularApp.questionnaire').controller('questionnaireCtr', [
  '$scope', 'QuestionAPI', '$routeParams', function($scope, QuestionAPI, $routeParams) {
    return $scope.view = {
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
        var options, responseId;
        this.display = 'loader';
        this.respStatus = $routeParams.respStatus;
        if (this.respStatus === 'lastQuestion') {

        } else if (this.respStatus === 'noValue') {
          responseId = '';
          options = {
            "responseId": responseId,
            "questionnaireId": 'EK9UXPhvP0',
            "patientId": '00011121'
          };
          return QuestionAPI.getQuestion(options).then((function(_this) {
            return function(data) {
              console.log('inside then');
              console.log(data);
              _this.data = data;
              return _this.display = 'noError';
            };
          })(this), (function(_this) {
            return function(error) {
              _this.display = 'error';
              return _this.errorType = error;
            };
          })(this));
        } else {

        }
      },
      loadNextQuestion: function(param) {
        return Storage.setData('responseId', 'get').then((function(_this) {
          return function(responseId) {
            CSpinner.show('', 'Please wait..');
            param.responseId = responseId;
            return QuestionAPI.saveAnswer(param).then(function(data) {
              App.resize();
              if (_this.readonly === true) {
                CToast.show('Your answer is saved');
              }
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
                App.navigate('summary', {
                  summary: responseId
                });
              }
              return _this.display = 'noError';
            }, function(error) {
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
      init: function() {
        console.log('insie questionnaire');
        return this.getQuestion();
      }
    };
  }
]);
