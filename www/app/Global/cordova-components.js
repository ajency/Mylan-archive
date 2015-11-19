angular.module('PatientApp.Global').factory('CToast', [
  '$cordovaToast', 'App', function($cordovaToast, App) {
    var CToast, webview;
    CToast = {};
    webview = App.isWebView();
    CToast.show = function(content) {
      if (webview) {
        return $cordovaToast.showShortBottom(content);
      } else {
        return console.log(content);
      }
    };
    CToast.showLong = function(content) {
      if (webview) {
        return $cordovaToast.showLongCenter(content);
      } else {
        return console.log(content);
      }
    };
    CToast.showLongBottom = function(content) {
      if (webview) {
        return $cordovaToast.showLongBottom(content);
      } else {
        return console.log(content);
      }
    };
    return CToast;
  }
]);
