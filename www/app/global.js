(function() {
  var init;

  init = function(WIN) {
    WIN.MYLANPHONE = '1234567891';
    WIN.AUTH_URL = 'http://mytelemedicineapp.com/api/v1';
    WIN.AUTH_HEADERS = {
      headers: {
        "X-API-KEY": 'nikaCr2vmWkphYQEwnkgtBlcgFzbT37Y',
        "X-Authorization": '88628ceb47fa1a7ba718f5b36a96c2d0af88e1b5',
        "Content-Type": 'application/json'
      }
    };
    WIN.APP_ID = 'mylanliveappid';
    WIN.PARSE_URL = 'http://parseserver.mytelemedicineapp.com:1337/parse';
    return WIN.PARSE_HEADERS = {
      headers: {
        "X-Parse-Application-Id": APP_ID
      }
    };
  };

  init(window);

}).call(this);
