<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Dashboard</title>

    <!-- Bootstrap -->
    <link href="{{ asset('patients/css/bootstrap.min.css') }}" rel="stylesheet">
     <link href="{{ asset('patients/css/font-awesome.min.css') }}" rel="stylesheet">
     <link href="{{ asset('patients/css/style.css') }}" rel="stylesheet">

    


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script src="{{ asset('patients/js/parse/parse-1.6.12.min.js') }}"></script>

    <script src="{{ asset('patients/js/global.js') }}"></script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ asset('patients/js/bootstrap.min.js') }}"></script>


    <script src="{{ asset('bower_components/angular/angular.min.js') }}"></script>
    <script src="{{ asset('bower_components/angular-route/angular-route.min.js') }}"></script>
    <script src="{{ asset('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('bower_components/underscore/underscore-min.js') }}"></script>
    <script src="{{ asset('bower_components/notifyjs/dist/notify.js') }}" type="text/javascript"></script>

    <script src="{{ asset('patients/js/app.js') }}"></script>

    <script src="{{ asset('patients/js/common/common.js') }}"></script>
    <script src="{{ asset('patients/js/common/angular-components.js') }}"></script>
    <script src="{{ asset('patients/js/common/error.js') }}"></script>
    <script src="{{ asset('patients/js/common/storage.js') }}"></script>

    <script src="{{ asset('patients/js/dashboard/dashboard.js') }}"></script>
    <script src="{{ asset('patients/js/dashboard/dashboard_api.js') }}"></script>
    <script src="{{ asset('patients/js/dashboard/start-questionnaire.js') }}"></script>
    <script src="{{ asset('patients/js/dashboard/directive.js') }}"></script>

    <script src="{{ asset('patients/js/notification/notification.js') }}"></script>
    <script src="{{ asset('patients/js/notification/notification-api.js') }}"></script>


    <script src="{{ asset('patients/js/questionnaire/summary.js') }}"></script>
    <script src="{{ asset('patients/js/questionnaire/questionnaire-api.js') }}"></script>
    <script src="{{ asset('patients/js/questionnaire/questionnaire.js') }}"></script>
    <script src="{{ asset('bower_components/parsleyjs/dist/parsley.js' ) }}" type="text/javascript"></script>

  </head>
  <body>
  

             @yield('content')
        
    <script type="text/javascript">
        $(document).ready(function(){
            var dataPlaceholders = document.querySelectorAll("input[placeholder]"),
    l = dataPlaceholders.length,
    
    // Set caret at the beginning of the input
    setCaret = function (evt) {
        if (this.value === this.getAttribute("data-placeholder")) {
            this.setSelectionRange(0, 0);
            evt.preventDefault();
            evt.stopPropagation();
            return false;
        }
    },
    
    // Clear placeholder value at user input
    clearPlaceholder = function (evt) {
        if (!(evt.shiftKey && evt.keyCode === 16) && evt.keyCode !== 9) {
            if (this.value === this.getAttribute("data-placeholder")) {
                this.value = "";
                this.className = "active";
                if (this.getAttribute("data-type") === "password") {
                    this.type = "password";
                }
            }
        } 
    },

    restorePlaceHolder = function () {
        if (this.value.length === 0) {
            this.value = this.getAttribute("data-placeholder");
            setCaret.apply(this, arguments);
            this.className = "";
            if (this.type === "password") {
                this.type = "text";
            }
        }
    },

    clearPlaceholderAtSubmit = function (evt) {
        for (var i=0, placeholder; i<l; i++) {
            placeholder = dataPlaceholders[i];
            if (placeholder.value === placeholder.getAttribute("data-placeholder")) {
                placeholder.value = "";
            }
        }
    };

    for (var i=0, placeholder, placeholderVal; i<l; i++) {
        placeholder = dataPlaceholders[i];
        placeholderVal = placeholder.getAttribute("placeholder");
        placeholder.setAttribute("data-placeholder", placeholderVal);
        placeholder.removeAttribute("placeholder");
        
        if (placeholder.value.length === 0) {
            placeholder.value = placeholderVal;
            if (placeholder.type === "password") {
                placeholder.type = "text";
            }
        }
        else {
            placeholder.className = "active";
        }
    
        // Apply events for placeholder handling         
        placeholder.addEventListener("focus", setCaret, false);
        placeholder.addEventListener("drop", setCaret, false);
        placeholder.addEventListener("click", setCaret, false);
        placeholder.addEventListener("keydown", clearPlaceholder, false);
        placeholder.addEventListener("keyup", restorePlaceHolder, false);
        placeholder.addEventListener("blur", restorePlaceHolder, false);
    
        // Clear all default placeholder values from the form at submit
        placeholder.form.addEventListener("submit", clearPlaceholderAtSubmit, false);
    }
        });
    </script>

  </body>
</html>