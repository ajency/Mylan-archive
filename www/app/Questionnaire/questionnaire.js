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
                _this.display = 'error';
                return _this.errorType = error;
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
                  _this.title = 'This questionnaire was Completed';
                  _this.showConfirm();
                } else if (data.status === 'missed') {
                  _this.title = 'This questionnaire was Missed';
                  _this.showConfirm();
                }
              }
              _this.variables();
              _this.data = [];
              _this.data = data;
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
        var arryObj, error, optionId, options, selectedvalue, sizeOfField, sizeOfTestboxAns, validArr, valueArr, valueInput;
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
          valueArr = [];
          validArr = [];
          error = 0;
          sizeOfField = _.size(this.data.options);
          sizeOfTestboxAns = _.size(this.val_answerValue);
          if (sizeOfTestboxAns === 0) {
            error = 1;
          } else {
            console.log(this.val_answerValue);
            _.each(this.val_answerValue, function(value) {
              var valid;
              value = value.toString();
              console.log(value);
              if (value === null || value === '') {
                console.log('empty');
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
          if (error === 1 || validArr.length > 0) {
            CToast.show('Please enter the values');
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
            options = options = {
              "questionId": this.data.questionId,
              "options": arryObj
            };
            this.loadNextQuestion(options);
          }
        }
        if (this.data.questionType === 'multi-choice') {
          if (!_.contains(_.pluck(this.data.options, 'checked'), true)) {
            CToast.show('Please select atleast one answer');
          } else {
            selectedvalue = [];
            _.each(this.data.options, function(opt) {
              console.log('************');
              console.log(opt);
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
                  _this.title = 'This questionnaire was Completed';
                  _this.showConfirm();
                } else if (data.status === 'missed') {
                  _this.title = 'This questionnaire was Missed';
                  _this.showConfirm();
                }
              }
              _this.variables();
              _this.data = [];
              _this.data = data;
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
        var arryObj, optionId, options, selectedvalue, value, valueInput;
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
          arryObj = [];
          _.each(this.data.options, (function(_this) {
            return function(opt) {
              var a;
              a = _this.val_answerValue[opt.option];
              if (!_.isUndefined(a) && !_.isEmpty(a) && !_.isNull(a)) {
                valueInput.push(a);
                optionId.push(opt.id);
                obj['id'] = opt.id;
                obj['value'] = a;
                return arryObj.push(obj);
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
            "questionId": this.data.questionId,
            "options": arryObj
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
          _.each(this.data.hasAnswer.option, (function(_this) {
            return function(val) {
              return _this.val_answerValue[val.label] = Number(val.value);
            };
          })(this));
          console.log(this.val_answerValue);
          this.firstRowStatus = true;
          return this.secondRowStatus = true;
        } else {
          this.firstRowStatus = false;
          return this.secondRowStatus = false;
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
            this.title = 'This questionnaire was Completed';
            return this.showConfirm();
          } else if (data.status === 'missed') {
            this.title = 'This questionnaire was Missed';
            return this.showConfirm();
          }
        }
      },
      firstRow: function() {
        if (_.isEmpty(this.data.hasAnswer)) {
          console.log('first row');
          this.firstRowStatus = true;
          this.secondRowStatus = false;
          this.val_answerValue['ST'] = '';
          return this.val_answerValue['lbs'] = '';
        }
      },
      secondRow: function() {
        if (_.isEmpty(this.data.hasAnswer)) {
          console.log('second row');
          this.firstRowStatus = false;
          this.secondRowStatus = true;
          return this.val_answerValue['Kg'] = '';
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
