angular.module('angularApp.questionnaire').controller('questionnaireCtr', [
  '$scope', 'QuestionAPI', '$routeParams', 'CToast', '$location', 'Storage', function($scope, QuestionAPI, $routeParams, CToast, $location, Storage) {
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
      popTitle: '',
      responseId: '',
      firstText: '',
      secondText: '',
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
        this.firstText = 'notSelected';
        this.secondText = 'notSelected';
        this.descriptiveAnswer = '';
        this.singleChoiceValue = '';
        return this.val_answerValue = {};
      },
      hasAnswerShow: function() {
        var kgsSelected;
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
          kgsSelected = [];
          _.each(this.data.hasAnswer.option, (function(_this) {
            return function(value) {
              var bool, labelKg, str;
              str = value.label;
              str = str.toLowerCase();
              labelKg = ['kg', 'kgs'];
              bool = _.contains(labelKg, str);
              if (bool === true) {
                return kgsSelected.push(1);
              }
            };
          })(this));
          if (kgsSelected.length === 0) {
            this.firstText = 'notSelected';
            this.secondText = 'selected';
          } else {
            this.firstText = 'selected';
            this.secondText = 'notSelected';
          }
          return _.each(this.data.hasAnswer.option, (function(_this) {
            return function(val) {
              return _this.val_answerValue[val.label] = Number(val.value);
            };
          })(this));
        }
      },
      pastAnswer: function() {
        var optionSelectedArray, pluckId, previousAns, sortedArray;
        previousAns = this.data.previousQuestionnaireAnswer;
        if (!_.isEmpty(previousAns)) {
          if (this.data.questionType === 'input') {
            console.log('1');
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
            optionSelectedArray.sort();
            this.data.previousQuestionnaireAnswer['label'] = optionSelectedArray.toString();
          }
          return this.data.previousQuestionnaireAnswer.dateDisplay = moment(previousAns.date).format('MMMM Do YYYY');
        }
      },
      vlaidateInput: function() {
        var InputReturn, arryObj, error, kgValid, lbValid, optionId, options, sizeOfField, sizeOfTestboxAns, stValid, validArr, valueArr, valueInput, weightInput, weightKeys, weigthValueArray;
        InputReturn = true;
        valueArr = [];
        validArr = [];
        error = 0;
        sizeOfField = _.size(this.data.options);
        sizeOfTestboxAns = _.size(this.val_answerValue);
        kgValid = true;
        lbValid = true;
        stValid = false;
        weightInput = 0;
        if (sizeOfTestboxAns === 0) {
          error = 1;
        } else {
          _.each(this.val_answerValue, function(value) {
            var valid;
            value = value.toString();
            if (value === null || value === '') {
              return valueArr.push(1);
            } else {
              valid = value.match(/^(?![0.]+$)\d+(\.\d{1,2})?$/gm);
              if (valid === null) {
                return validArr.push(1);
              }
            }
          });
          if (valueArr.length === _.size(this.val_answerValue)) {
            error = 1;
          }
        }
        if (!_.isEmpty(this.val_answerValue)) {
          weightKeys = _.keys(this.val_answerValue);
          weigthValueArray = _.values(this.val_answerValue);
          _.each(weightKeys, function(val) {
            var lowerCase, valid;
            lowerCase = val.toLowerCase();
            if (_.contains(['kg', 'kgs'], lowerCase)) {
              weightInput = 1;
              valid = weigthValueArray[_.indexOf(weightKeys, val)].toString().match(/^(?![0.]+$)\d+(\.\d{1,2})?$/gm);
              if (valid === null) {
                kgValid = false;
              }
            }
            lowerCase = val.toLowerCase();
            if (_.contains(['lb', 'lbs'], lowerCase)) {
              weightInput = 1;
              valid = weigthValueArray[_.indexOf(weightKeys, val)].toString().match(/^-?\d*(\.\d+)?$/);
              if (valid === null) {
                lbValid = false;
              }
            }
            lowerCase = val.toLowerCase();
            if (_.contains(['st', 'sts'], lowerCase)) {
              weightInput = 1;
              valid = weigthValueArray[_.indexOf(weightKeys, val)].toString().match(/^(?![0.]+$)\d+(\.\d{1,2})?$/gm);
              if (valid !== null) {
                return stValid = true;
              } else if (valid === null) {
                return stValid = false;
              }
            }
          });
        }
        if ((weightInput === 0) && (error === 1 || validArr.length > 0)) {
          CToast.show('Please enter the values');
        } else if ((weightInput === 1) && (this.firstText === 'selected' && kgValid === false)) {
          CToast.show('Please enter valid value,kg cannot be zero');
        } else if ((weightInput === 1) && (this.secondText === 'selected' && (stValid === false || lbValid === false))) {
          CToast.show('Please enter valid value,st cannot be zero');
        } else {
          valueInput = [];
          optionId = [];
          arryObj = [];
          _.each(this.data.options, (function(_this) {
            return function(opt) {
              var a, obj;
              obj = {};
              a = _this.val_answerValue[opt.option];
              if (!_.isUndefined(a) && a !== '') {
                obj['id'] = opt.id;
                obj['value'] = a.toString();
                return arryObj.push(obj);
              }
            };
          })(this));
          options = {
            "questionId": this.data.questionId,
            "options": arryObj,
            "value": "",
            "responseId": this.data.responseId
          };
          InputReturn = options;
        }
        return InputReturn;
      },
      getQuestion: function() {
        var options, param, questionnaireData, responseId, startQuestData;
        Storage.getQuestStatus('set', '');
        startQuestData = {};
        Storage.startQuestionnaire('set', startQuestData);
        questionnaireData = Storage.questionnaire('get');
        console.log('**************getQuestion**************');
        console.log(questionnaireData);
        if (!_.isEmpty(questionnaireData)) {
          this.display = 'loader';
          this.respStatus = questionnaireData.respStatus;
          this.responseId = questionnaireData.responseId;
          if (this.respStatus === 'lastQuestion') {
            param = {
              "questionId": '',
              "options": [],
              "value": "",
              "responseId": questionnaireData.responseId
            };
            return QuestionAPI.getPrevQuest(param).then((function(_this) {
              return function(data) {
                _this.display = 'noError';
                _this.checkQuestinarieStatus(data);
                console.log('previous data');
                console.log(_this.data);
                _this.variables();
                _this.data = [];
                _this.data = data;
                _this.questionLabel();
                _this.readonly = _this.data.editable;
                _this.pastAnswer();
                if (!_.isEmpty(_this.data.hasAnswer)) {
                  return _this.hasAnswerShow();
                }
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
                if (error === 'offline') {
                  Storage.getQuestStatus('set', 'offline');
                  return $location.path('dashboard');
                } else {
                  Storage.getQuestStatus('set', 'questionnarireError');
                  return $location.path('dashboard');
                }
              };
            })(this));
          } else {
            responseId = questionnaireData.responseId;
            options = {
              "responseId": responseId,
              "questionnaireId": questionnaireIdd,
              "patientId": RefCode
            };
            return QuestionAPI.getQuestion(options).then((function(_this) {
              return function(data) {
                _this.display = 'noError';
                _this.checkQuestinarieStatus(data);
                console.log('inside then');
                console.log(data);
                _this.data = data;
                _this.questionLabel();
                return _this.pastAnswer();
              };
            })(this), (function(_this) {
              return function(error) {
                _this.display = 'error';
                return _this.errorType = error;
              };
            })(this));
          }
        } else {
          return $location.path('dashboard');
        }
      },
      loadNextQuestion: function(param) {
        this.responseId = param.responseId;
        this.CSpinnerShow();
        return QuestionAPI.saveAnswer(param).then((function(_this) {
          return function(data) {
            _this.display = 'noError';
            console.log('******next question******');
            console.log(data);
            if (!_.isUndefined(data.status)) {
              return _this.checkQuestinarieStatus(data);
            } else {
              _this.variables();
              _this.data = [];
              _this.data = data;
              _this.questionLabel();
              _this.readonly = true;
              if (!_.isEmpty(_this.data.hasAnswer)) {
                _this.hasAnswerShow();
                _this.readonly = _this.data.editable;
              }
              return _this.pastAnswer();
            }
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
        var options, param, selectedvalue;
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
          param = this.vlaidateInput();
          if (param !== true) {
            this.loadNextQuestion(param);
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
            _this.checkQuestinarieStatus(data);
            console.log('previous data');
            console.log(_this.data);
            _this.variables();
            _this.data = [];
            _this.data = data;
            _this.questionLabel();
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
        var containsString, options, param, selectedvalue;
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
          containsString = 0;
          if (_.isEmpty(this.val_answerValue) || _.isUndefined(this.val_answerValue)) {
            containsString = 0;
          } else {
            _.each(this.val_answerValue, function(value) {
              if (value.toString() !== '') {
                return containsString = 1;
              }
            });
          }
          if (containsString === 0) {
            options = {
              "responseId": this.data.responseId,
              "questionId": this.data.questionId,
              "options": [],
              "value": ""
            };
            return this.loadPrevQuestion(options);
          } else {
            param = this.vlaidateInput();
            if (param !== true) {
              return this.loadPrevQuestion(param);
            }
          }
        }
      },
      onTapToRetry: function() {
        if (this.respStatus === 'noValue') {
          return $location.path('dashboard');
        } else {
          this.display = 'loader';
          return this.getQuestion();
        }
      },
      init: function() {
        console.log('insie questionnaire');
        return this.getQuestion();
      },
      closeModal: function() {
        $('#pauseModal').modal('hide');
        $('.modal-backdrop').addClass('hidden');
        $("body").removeClass("modal-open");
        return $location.path('dashboard');
      },
      showConfirm: function() {
        console.log('popup shown ');
        return $('#QuestionarieModal').modal('show');
      },
      CloseQUestionPopup: function() {
        $('#QuestionarieModal').modal('hide');
        $('.modal-backdrop').addClass('hidden');
        $("body").removeClass("modal-open");
        return $location.path('dashboard');
      },
      checkQuestinarieStatus: function(data) {
        var summaryData;
        if (!_.isUndefined(data.status)) {
          if (data.status === 'completed') {
            this.popTitle = 'This questionnaire was Completed';
            this.showConfirm();
            return this.display = 'completed';
          } else if (data.status === 'missed') {
            this.popTitle = 'This questionnaire was Missed';
            this.showConfirm();
            return this.display = 'completed';
          } else if (data.status === 'saved_successfully') {
            summaryData = {
              previousState: 'questionnaire',
              responseId: this.responseId
            };
            Storage.summary('set', summaryData);
            return $location.path('summary');
          }
        }
      },
      questionLabel: function() {
        var arr, kg;
        if (this.data.questionType === 'input') {
          arr = [];
          this.data.withoutkg = {};
          this.data.withkg = {};
          kg = {};
          _.each(this.data.options, (function(_this) {
            return function(value) {
              var bool, labelKg, str;
              str = value.option;
              str = str.toLowerCase();
              labelKg = ['kg', 'kgs'];
              bool = _.contains(labelKg, str);
              if (bool) {
                arr.push(1);
                return kg = value;
              }
            };
          })(this));
          if (arr.length > 0) {
            this.data.optionsLabel = true;
            this.data.withoutkg = _.without(this.data.options, kg);
            return this.data.withkg = kg;
          } else {
            return this.data.optionsLabel = false;
          }
        }
      },
      firstRow: function() {
        var a, edit;
        if (this.readonly === false && !_.isEmpty(this.data.hasAnswer)) {
          edit = true;
        } else {
          edit = false;
        }
        if (edit === false) {
          console.log('inside firstrow click');
          this.firstText = 'selected';
          this.secondText = 'notSelected';
          a = {};
          _.each(this.val_answerValue, (function(_this) {
            return function(val, key) {
              return a[key] = '';
            };
          })(this));
          return this.val_answerValue = a;
        }
      },
      secondRow: function() {
        var edit;
        if (this.readonly === false && !_.isEmpty(this.data.hasAnswer)) {
          edit = true;
        } else {
          edit = false;
        }
        if (edit === false) {
          console.log('inside second row click');
          this.firstText = 'notSelected ';
          this.secondText = 'selected';
          return _.each(this.data.options, (function(_this) {
            return function(value) {
              var bool, labelKg, str;
              str = value.option;
              str = str.toLowerCase();
              labelKg = ['kg', 'kgs'];
              bool = _.contains(labelKg, str);
              if (bool) {
                return _this.val_answerValue[value.option] = '';
              }
            };
          })(this));
        }
      }
    };
  }
]);
