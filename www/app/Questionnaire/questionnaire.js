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
              "projectId": patientData.project_id,
              "hospitalId": patientData.hospital.id,
              "patientId": parseInt(patientData.patient_id)
            };
            return QuestionAPI.getQuestion(options).then(function(data) {
              console.log('inside then');
              console.log(data);
              _this.data = data.result;
              _this.display = 'noError';
              return $timeout(function() {
                console.log('timeoutt');
                return _this.infoBox = false;
              }, 30000);
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
        var options;
        console.log('nextQuestion');
        console.log(this.data.question.type);
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
            CSpinner.show('', 'Please wait..');
            return QuestionAPI.saveAnswer(options).then((function(_this) {
              return function(data) {
                console.log('inside save');
                console.log(data);
                return CToast.show('Your answer is saved');
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
