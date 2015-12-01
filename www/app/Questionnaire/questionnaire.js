angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'QuestionAPI', '$stateParams', '$window', 'Storage', 'CToast', 'CSpinner', '$q', '$timeout', function($scope, App, QuestionAPI, $stateParams, $window, Storage, CToast, CSpinner, $q, $timeout) {
    $scope.view = {
      pastAnswerDiv: 0,
      title: 'C-weight',
      data: [],
      go: '',
      response: '',
      actionValue: {},
      errorType: 'No network connection',
      display: 'error',
      getLocal: function() {
        var defer;
        defer = $q.defer();
        Storage.getNextQuestion('get').then(function(details) {
          return defer.resolve(details);
        });
        return defer.promise;
      },
      getQuestion: function(questNo) {
        var data1;
        this.display = 'noError';
        data1 = '';
        return Storage.setData('patientData', 'get').then(function(patientData) {
          var options, param, url;
          options = {
            "projectId": patientData.project_id,
            "hospitalId": patientData.hospital.id,
            "patientId": parseInt(patientData.patient_id)
          };
          url = PARSE_URL + '/getQuestionnaire';
          param = options;
          App.sendRequest(url, param, PARSE_HEADERS).then(function(data) {
            console.log('****123***');
            console.log(data);
            $scope.view.data = data.data.result.question;
            this.data1 = data.data.result.question;
            console.log($scope.view.data);
            return this.display = 'noError';
          }, (function(_this) {
            return function(error) {
              return console.log('error');
            };
          })(this));
          console.log('data 11');
          return console.log(data1);
        });
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
        var error, sizeOfField, sizeOfTestboxAns;
        CSpinner.show('', 'Please wait..');
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
