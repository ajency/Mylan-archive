angular.module('angularApp.questionnaire').controller('questionnaireCtr', [
  '$scope', 'QuestionAPI', '$routeParams', 'CToast', '$location', function($scope, QuestionAPI, $routeParams, CToast, $location) {
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
      limitTo: 5,
      showMoreButton: true,
      overlay: false,
      showMore: function() {
        this.limitTo = this.limitTo + 5;
        App.resize();
        if (this.data.length < this.limitTo) {
          return this.showMoreButton = false;
        }
      },
      CSpinnerShow: function() {
        return this.overlay = true;
      },
      isEmpty: function(pastAnswerObject) {
        return _.isEmpty(pastAnswerObject);
      },
      CSpinnerHide: function() {
        return this.overlay = false;
      },
      variables: function() {
        this.descriptiveAnswer = '';
        this.singleChoiceValue = '';
        return this.val_answerValue = {};
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
          return this.data.previousQuestionnaireAnswer.dateDisplay = moment(previousAns.date).format('MMMM Do YYYY');
        }
      },
      getQuestion: function() {
        var options, param, responseId;
        this.display = 'loader';
        this.respStatus = $routeParams.respStatus;
        if (this.respStatus === 'lastQuestion') {
          param = {
            "questionId": '',
            "options": [],
            "value": "",
            "responseId": $routeParams.responseId
          };
          return QuestionAPI.getPrevQuest(param).then((function(_this) {
            return function(data) {
              console.log('previous data');
              console.log(_this.data);
              _this.variables();
              _this.data = [];
              _this.data = data;
              _this.readonly = _this.data.editable;
              _this.pastAnswer();
              if (!_.isEmpty(_this.data.hasAnswer)) {
                _this.hasAnswerShow();
              }
              return _this.display = 'noError';
            };
          })(this), (function(_this) {
            return function(error) {
              _this.display = 'error';
              console.log(error);
              if (error === 'offline') {
                return CToast.show('Check net connection,answer not saved');
              } else {
                return CToast.show('Error ,try again');
              }
            };
          })(this));
        } else if (this.respStatus === 'noValue') {
          responseId = '';
          options = {
            "responseId": responseId,
            "questionnaireId": questionnaireIdd,
            "patientId": RefCode
          };
          return QuestionAPI.getQuestion(options).then((function(_this) {
            return function(data) {
              console.log('inside then');
              console.log(data);
              _this.data = data;
              _this.pastAnswer();
              return _this.display = 'noError';
            };
          })(this), (function(_this) {
            return function(error) {
              _this.display = 'error';
              return _this.errorType = error;
            };
          })(this));
        } else {
          responseId = this.respStatus;
          options = {
            "responseId": responseId,
            "questionnaireId": questionnaireIdd,
            "patientId": RefCode
          };
          return QuestionAPI.getQuestion(options).then((function(_this) {
            return function(data) {
              console.log('inside then');
              console.log(data);
              _this.data = data;
              if (!_.isUndefined(_this.data.status)) {
                if (_this.data.status === 'saved_successfully') {
                  CToast.show('This questionnaire was already answer');
                  $location.path('summary/' + responseId);
                } else if (_this.data.status === 'completed') {
                  CToast.show('This questionnaire is completed ');
                } else if (_this.data.status === 'missed') {
                  CToast.show('This questionnaire was Missed');
                }
              }
              _this.pastAnswer();
              return _this.display = 'noError';
            };
          })(this), (function(_this) {
            return function(error) {
              _this.display = 'error';
              return _this.errorType = error;
            };
          })(this));
        }
      },
      loadNextQuestion: function(param) {
        this.CSpinnerShow();
        return QuestionAPI.saveAnswer(param).then((function(_this) {
          return function(data) {
            console.log('******next question******');
            console.log(data);
            _this.variables();
            _this.data = [];
            _this.data = data;
            _this.readonly = true;
            if (!_.isEmpty(_this.data.hasAnswer)) {
              _this.hasAnswerShow();
              _this.readonly = _this.data.editable;
            }
            _this.pastAnswer();
            if (!_.isUndefined(_this.data.status)) {
              $location.path('summary/' + param.responseId);
            }
            return _this.display = 'noError';
          };
        })(this), (function(_this) {
          return function(error) {
            if (error === 'offline') {
              return CToast.show('Check net connection,answer not saved');
            } else {
              return CToast.show('Error in saving answer,try again');
            }
          };
        })(this))["finally"]((function(_this) {
          return function() {
            return _this.CSpinnerHide();
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
              "value": "",
              "responseId": this.data.responseId
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
              "value": valueInput[0].toString(),
              "responseId": this.data.responseId
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
            options = {
              "questionId": this.data.questionId,
              "options": selectedvalue,
              "value": "",
              "responseId": this.data.responseId
            };
            this.loadNextQuestion(options);
          }
        }
        if (this.data.questionType === 'descriptive') {
          if (this.descriptiveAnswer === '') {
            return CToast.show('Please Fill in the following');
          } else {
            options = {
              "questionId": this.data.questionId,
              "options": [],
              "value": this.descriptiveAnswer,
              "responseId": this.data.responseId
            };
            return this.loadNextQuestion(options);
          }
        }
      },
      loadPrevQuestion: function(param) {
        this.CSpinnerShow();
        return QuestionAPI.getPrevQuest(param).then((function(_this) {
          return function(data) {
            console.log('previous data');
            console.log(_this.data);
            _this.variables();
            _this.data = [];
            _this.data = data;
            _this.readonly = _this.data.editable;
            _this.pastAnswer();
            if (!_.isEmpty(_this.data.hasAnswer)) {
              _this.hasAnswerShow();
            }
            return console.log(_this.data);
          };
        })(this), (function(_this) {
          return function(error) {
            console.log(error);
            if (error === 'offline') {
              return CToast.show('Check net connection,answer not saved');
            } else {
              return CToast.show('Error ,try again');
            }
          };
        })(this))["finally"]((function(_this) {
          return function() {
            return _this.CSpinnerHide();
          };
        })(this));
      },
      prevQuestion: function() {
        var optionId, options, selectedvalue, value, valueInput;
        if (this.data.questionType === 'single-choice') {
          options = {
            "responseId": this.data.responseId,
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
            "responseId": this.data.responseId,
            "questionId": this.data.questionId,
            "options": selectedvalue === [] ? [] : selectedvalue,
            "value": ""
          };
          this.loadPrevQuestion(options);
        }
        if (this.data.questionType === 'descriptive') {
          options = {
            "responseId": this.data.responseId,
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
              if (!_.isUndefined(a) && !_.isEmpty(a) && !_.isNull(a)) {
                valueInput.push(a);
                return optionId.push(opt.id);
              }
            };
          })(this));
          console.log('***');
          console.log(optionId);
          if (_.isEmpty(optionId)) {
            optionId = [];
          } else {
            optionId = [optionId[0]];
          }
          if (_.isEmpty(valueInput)) {
            value = [];
          } else {
            value = valueInput[0].toString();
          }
          options = {
            "responseId": this.data.responseId,
            "questionId": this.data.questionId,
            "options": optionId,
            "value": value.toString()
          };
          return this.loadPrevQuestion(options);
        }
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.getQuestion();
      },
      init: function() {
        console.log('insie questionnaire');
        return this.getQuestion();
      },
      closeModal: function() {
        $('#pauseModal').modal('hide');
        $('.modal-backdrop').addClass('hidden');
        return $location.path('dashboard');
      }
    };
  }
]);
