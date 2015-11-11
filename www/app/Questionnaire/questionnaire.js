angular.module('PatientApp.Quest', []).controller('questionnaireCtr', [
  '$scope', 'App', 'Storage', 'QuestionAPI', '$stateParams', '$window', function($scope, App, Storage, QuestionAPI, $stateParams, $window) {
    return $scope.view = {
      title: 'C-weight',
      data: [],
      go: '',
      response: '',
      getQuestion: function() {
        var options;
        options = {
          quizID: $stateParams.quizID
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
        return this.getQuestion();
      },
      nextQuestion: function() {
        var options;
        options = {
          quizID: $stateParams.quizID,
          questionId: this.data.questionId,
          answerId: this.go,
          action: 'submitted'
        };
        return QuestionAPI.saveAnswer(options).then((function(_this) {
          return function(data) {
            _this.response = data;
            console.log(_this.response);
            if (_this.response.type === 'nextQuestion') {
              console.log('next question');
              return $window.location.reload();
            } else {
              console.log('summary');
              return App.navigate('summary', {
                quizID: _this.response.quizID
              });
            }
          };
        })(this), (function(_this) {
          return function(error) {
            return console.log('err');
          };
        })(this));
      }
    };
  }
]).config([
  '$stateProvider', function($stateProvider) {
    return $stateProvider.state('questionnaire', {
      url: '/questionnaire:quizID',
      parent: 'parent-questionnaire',
      views: {
        "QuestionContent": {
          templateUrl: 'views/questionnaire/question.html',
          controller: 'questionnaireCtr'
        }
      }
    });
  }
]);
