angular.module('angularApp.questionnaire', []).controller('summaryController', [
  '$scope', 'QuestionAPI', '$routeParams', 'CToast', '$location', 'App', 'Storage', function($scope, QuestionAPI, $routeParams, CToast, $location, App, Storage) {
    return $scope.view = {
      data: [],
      display: 'loader',
      hideButton: null,
      responseId: '',
      init: function() {
        var param, summaryData;
        console.log('summaryyyy');
        summaryData = Storage.summary('get');
        console.log(summaryData);
        if (!_.isEmpty(summaryData)) {
          this.responseId = summaryData.responseId;
          this.hideButton = summaryData.previousState === 'questionnaire' ? true : false;
          console.log('hide');
          console.log(this.hideButton);
          param = {
            responseId: this.responseId
          };
          return QuestionAPI.getSummary(param).then((function(_this) {
            return function(data) {
              _this.data = data;
              _this.data.submissionDate = moment(_this.data.submissionDate).format('MMMM Do YYYY');
              _.each(_this.data, function(value) {
                var a;
                a = value.input;
                if (!_.isUndefined(a)) {
                  return value['type'] = 'input';
                } else {
                  return value['type'] = 'option';
                }
              });
              return _this.display = 'noError';
            };
          })(this), (function(_this) {
            return function(error) {
              _this.display = 'error';
              return _this.errorType = error;
            };
          })(this));
        } else {
          return $location.path('dashboard');
        }
      },
      submitSummary: function() {
        var param;
        param = {
          responseId: this.responseId
        };
        return QuestionAPI.submitSummary(param).then((function(_this) {
          return function(data) {
            var questionnaireData;
            CToast.show('submiteed successfully ');
            questionnaireData = {};
            Storage.questionnaire('set', questionnaireData);
            return $location.path('dashboard');
          };
        })(this), (function(_this) {
          return function(error) {
            console.log('error');
            console.log(error);
            return CToast.show('Error in submitting questionnarie');
          };
        })(this))["finally"](function() {});
      },
      back: function() {
        var questionnaireData;
        if (this.hideButton === false) {
          return $location.path('dashboard');
        } else {
          questionnaireData = {
            respStatus: 'lastQuestion',
            responseId: this.responseId
          };
          Storage.questionnaire('set', questionnaireData);
          return $location.path('questionnaire');
        }
      },
      onTapToRetry: function() {
        this.display = 'loader';
        return this.init();
      }
    };
  }
]);
