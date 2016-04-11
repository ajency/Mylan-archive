angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', '$window', 'Storage', 'CToast', 'CSpinner', '$q', '$timeout', '$ionicPlatform', '$ionicPopup', function($scope, App, QuestionAPI, $stateParams, $window, Storage, CToast, CSpinner, $q, $timeout, $ionicPlatform, $ionicPopup) {
    var onDeviceBack, onHardwareBackButton1;
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
      alertPopup: '',
      weightValue: 'selected',
      firstText: '',
      secondText: '',
      variables: function() {
        this.firstText = 'notSelected';
        this.secondText = 'notSelected';
        this.descriptiveAnswer = '';
        this.singleChoiceValue = '';
        return this.val_answerValue = {};
      },
      getQuestion: function() {
        Storage.getQuestStatus('set', '');
        this.display = 'loader';
        return Storage.setData('refcode', 'get').then((function(_this) {
          return function(refcode) {
            _this.refcode = refcode;
            return Storage.setData('patientData', 'get');
          };
        })(this)).then((function(_this) {
          return function(patientData) {
            var options, param, responseId;
            _this.respStatus = $stateParams.respStatus;
            if (_this.respStatus === 'lastQuestion') {
              param = {
                "questionId": '',
                "options": [],
                "value": ""
              };
              return Storage.setData('responseId', 'get').then(function(responseId) {
                param.responseId = responseId;
                return QuestionAPI.getPrevQuest(param).then(function(data) {
                  console.log('previous data');
                  console.log(_this.data);
                  _this.variables();
                  _this.data = [];
                  _this.data = data;
                  _this.checkQuestinarieStatus(data);
                  _this.questionLabel();
                  _this.readonly = _this.data.editable;
                  _this.pastAnswer();
                  if (!_.isEmpty(_this.data.hasAnswer)) {
                    _this.hasAnswerShow();
                  }
                  return _this.display = 'noError';
                }, function(error) {
                  _this.display = 'error';
                  return _this.errorType = error;
                });
              });
            } else if (_this.respStatus === 'firstQuestion') {
              param = {
                "questionnaireId": patientData.id
              };
              return Storage.setData('responseId', 'get').then(function(responseId) {
                param.responseId = responseId;
                return QuestionAPI.getFirstQuest(param).then(function(data) {
                  console.log('previous data');
                  console.log(_this.data);
                  _this.variables();
                  _this.data = [];
                  _this.data = data;
                  _this.checkQuestinarieStatus(data);
                  _this.questionLabel();
                  _this.readonly = _this.data.editable;
                  _this.pastAnswer();
                  if (!_.isEmpty(_this.data.hasAnswer)) {
                    _this.hasAnswerShow();
                  }
                  return _this.display = 'noError';
                }, function(error) {
                  _this.display = 'error';
                  return _this.errorType = error;
                });
              });
            } else if (_this.respStatus === 'noValue') {
              responseId = '';
              options = {
                "responseId": responseId,
                "questionnaireId": patientData.id,
                "patientId": _this.refcode
              };
              return QuestionAPI.getQuestion(options).then(function(data) {
                console.log('inside then');
                console.log(data);
                _this.data = data;
                _this.pastAnswer();
                Storage.setData('responseId', 'set', data.responseId);
                return _this.display = 'noError';
              }, function(error) {
                if (error === 'offline') {
                  Storage.getQuestStatus('set', 'offline');
                } else {
                  Storage.getQuestStatus('set', 'questionnarireError');
                }
                return App.navigate('dashboard', {}, {
                  animate: false,
                  back: false
                });
              });
            } else {
              responseId = $stateParams.respStatus;
              options = {
                "responseId": responseId,
                "questionnaireId": patientData.id,
                "patientId": _this.refcode
              };
              return QuestionAPI.getQuestion(options).then(function(data) {
                console.log('inside then');
                console.log(data);
                _this.data = data;
                _this.questionLabel();
                _this.checkQuestinarieStatus(data);
                _this.pastAnswer();
                Storage.setData('responseId', 'set', data.responseId);
                return _this.display = 'noError';
              }, function(error) {
                _this.display = 'error';
                return _this.errorType = error;
              });
            }
          };
        })(this));
      },
      init: function() {
        return this.getQuestion();
      },
      vlaidateInput: function() {
        var InputReturn, arryObj, error, kgValid, lbValid, optionId, options, sizeOfField, sizeOfTestboxAns, stValid, validArr, valueArr, valueInput, weightInput, weightKeys, weigthValueArray;
        InputReturn = true;
        valueArr = [];
        validArr = [];
        error = 0;
        sizeOfField = _.size(this.data.options);
        sizeOfTestboxAns = _.size(this.val_answerValue);
        kgValid = false;
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
              if (valid !== null) {
                kgValid = true;
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
        console.log('********inputt*********');
        console.log(weightInput);
        console.log(validArr);
        console.log(weightInput);
        console.log(this.firstText);
        console.log(this.secondText);
        if ((weightInput === 0) && (error === 1 || validArr.length > 0)) {
          CToast.show('Please enter the values');
        } else if ((weightInput === 1) && (this.firstText === 'selected' && kgValid === false)) {
          CToast.showLongBottom('Please enter valid value,kg cannot be zero');
        } else if ((weightInput === 1) && (this.secondText === 'selected' && (stValid === false || lbValid === false))) {
          CToast.showLongBottom('Please enter valid value,st cannot be zero');
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
            "value": ""
          };
          InputReturn = options;
        }
        return InputReturn;
      },
      loadNextQuestion: function(param) {
        return Storage.setData('responseId', 'get').then((function(_this) {
          return function(responseId) {
            CSpinner.show('', 'Please wait..');
            param.responseId = responseId;
            return QuestionAPI.saveAnswer(param).then(function(data) {
              if (_this.readonly === true) {
                CToast.show('Your answer is saved');
              }
              console.log('******next question******');
              console.log(data);
              if (!_.isUndefined(data.status)) {
                if (data.status === 'saved_successfully') {
                  App.navigate('summary', {
                    summary: responseId
                  });
                } else if (data.status === 'completed') {
                  _this.title = 'This questionnaire was completed';
                  _this.showConfirm();
                } else if (data.status === 'missed') {
                  _this.title = 'This questionnaire was missed';
                  _this.showConfirm();
                }
              }
              _this.variables();
              _this.data = [];
              _this.data = data;
              _this.questionLabel();
              App.resize();
              App.scrollTop();
              _this.readonly = true;
              if (!_.isEmpty(_this.data.hasAnswer)) {
                _this.hasAnswerShow();
                _this.readonly = _this.data.editable;
              }
              _this.pastAnswer();
              return _this.display = 'noError';
            }, function(error) {
              if (error === 'offline') {
                return CToast.showLongBottom('Please check your internet connection');
              } else if (error === 'server_error') {
                return CToast.show('Server error. Try again!');
              } else {
                return CToast.show('Server error. Try again!');
              }
            })["finally"](function() {
              return CSpinner.hide();
            });
          };
        })(this));
      },
      nextQuestion: function() {
        var options, param, selectedvalue;
        if (this.data.questionType === 'single-choice') {
          if (this.singleChoiceValue === '') {
            CToast.show('Please select your answer');
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
          param = this.vlaidateInput();
          console.log('******** param **********');
          console.log(param);
          if (param !== true) {
            this.loadNextQuestion(param);
          }
        }
        if (this.data.questionType === 'multi-choice') {
          if (!_.contains(_.pluck(this.data.options, 'checked'), true)) {
            CToast.show('Please select atleast one answer');
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
              "value": ""
            };
            this.loadNextQuestion(options);
          }
        }
        if (this.data.questionType === 'descriptive') {
          if (this.descriptiveAnswer === '') {
            return CToast.show('Please fill in the following');
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
              console.log(data);
              if (!_.isUndefined(data.status)) {
                if (data.status === 'completed') {
                  _this.title = 'This questionnaire was completed';
                  _this.showConfirm();
                } else if (data.status === 'missed') {
                  _this.title = 'This questionnaire was missed';
                  _this.showConfirm();
                }
              }
              _this.variables();
              _this.data = [];
              _this.data = data;
              _this.questionLabel();
              App.resize();
              App.scrollTop();
              _this.readonly = _this.data.editable;
              _this.pastAnswer();
              if (!_.isEmpty(_this.data.hasAnswer)) {
                return _this.hasAnswerShow();
              }
            }, function(error) {
              if (error === 'offline') {
                return CToast.show('Please check your internet connection');
              } else if (error === 'server_error') {
                return CToast.showLongBottom('Server error. Try again!');
              } else {
                return CToast.showLongBottom('Server error. Try again!');
              }
            })["finally"](function() {
              return CSpinner.hide();
            });
          };
        })(this));
      },
      prevQuestion: function() {
        var containsString, options, param, selectedvalue;
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
        if (this.respStatus === 'noValue') {
          return App.navigate('dashboard', {}, {
            animate: false,
            back: false
          });
        } else {
          this.display = 'loader';
          return this.getQuestion();
        }
      },
      isEmpty: function(pastAnswerObject) {
        return _.isEmpty(pastAnswerObject);
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
          _.each(this.data.hasAnswer.option, (function(_this) {
            return function(val) {
              return _this.val_answerValue[val.label] = Number(val.value);
            };
          })(this));
          return console.log(this.val_answerValue);
        }
      },
      navigateOnDevice: function() {
        if ($('.popup-container').hasClass('active')) {
          this.alertPopup.close();
          return App.navigate('dashboard', {}, {
            animate: false,
            back: false
          });
        } else {
          if (this.data.previous === false) {
            return App.navigate('dashboard', {}, {
              animate: false,
              back: false
            });
          } else {
            return $scope.view.prevQuestion();
          }
        }
      },
      showConfirm: function() {
        this.alertPopup = $ionicPopup.alert({
          title: 'Alert',
          cssClass: 'popupQuestion',
          template: this.title
        });
        return this.alertPopup.then(function(res) {
          if (res) {
            return App.navigate('dashboard', {}, {
              animate: false,
              back: false
            });
          }
        });
      },
      checkQuestinarieStatus: function(data) {
        if (!_.isUndefined(data.status)) {
          if (data.status === 'saved_successfully') {
            return App.navigate('summary', {
              summary: $stateParams.respStatus
            });
          } else if (data.status === 'completed') {
            this.title = 'This questionnaire was completed';
            return this.showConfirm();
          } else if (data.status === 'missed') {
            this.title = 'This questionnaire was missed';
            return this.showConfirm();
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
      }
    };
    onDeviceBack = function() {
      return $scope.view.navigateOnDevice();
    };
    onHardwareBackButton1 = null;
    $scope.$on('$ionicView.beforeEnter', function(event, viewData) {
      return $scope.view.reInit();
    });
    $scope.$on('$ionicView.enter', function() {
      console.log('$ionicView.enter questionarie');
      return onHardwareBackButton1 = $ionicPlatform.registerBackButtonAction(onDeviceBack, 1000);
    });
    return $scope.$on('$ionicView.leave', function() {
      console.log('$ionicView.leave');
      if (onHardwareBackButton1) {
        return onHardwareBackButton1();
      }
    });
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('questionnaire', {
      url: '/questionnaire:respStatus',
      parent: 'main',
      cache: false,
      views: {
        "appContent": {
          templateUrl: 'views/questionnaire/question.html',
          controller: 'questionnaireCtr'
        }
      }
    });
  }
]);
