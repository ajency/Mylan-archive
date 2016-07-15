<!DOCTYPE html>
<html>
    <head>
        <title>403.</title>

        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,800' rel='stylesheet' type='text/css'>

        <style>
            body {
                font-family: 'Open Sans';
            }

            .content {
                text-align: center;
                position: relative;
            }

            .error-403{
                font-size: 150px;
                font-weight: 600;
                color: #ccc;
                opacity: .3;
                margin-bottom: 0;
            }

            .error-subtitle{
                position: absolute;
                top: 80px;
                left: -30px;
                right: 0;
                font-size: 16px;
                letter-spacing: .5px;
                
            }

            .actual-msg{
                font-size: 14px;
                word-spacing: .5px;
            }

            .cust-link{
                color: #05a8a5;
            }

            .content p{
                color: #5c5c5c;
            }
        </style>
    </head>
    <body>
        
        <div class="container">
            <div class="content">
                <h1 class="error-403">403.</h1>
                <p class="error-subtitle">ACCESS DENIED!</p>

                <p class="actual-msg">You don't have enough permissions to access this page</p>
                <a href="{{ url() }}" class="btn btn-link cust-link">Go back</a>
            </div>
        </div>
		<script>
			/*BACK Button overide*/
			window.onload = function () {
				if (typeof history.pushState === "function") {
					history.pushState("jibberish", null, null);
					window.onpopstate = function () {
						history.pushState('newjibberish', null, null);
						// Handle the back (or forward) buttons here
						// Will NOT handle refresh, use onbeforeunload for this.
					};
				}
				else {
					var ignoreHashChange = true;
					window.onhashchange = function () {
						if (!ignoreHashChange) {
							ignoreHashChange = true;
							window.location.hash = Math.random();
							// Detect and redirect change here
							// Works in older FF and IE9
							// * it does mess with your hash symbol (anchor?) pound sign
							// delimiter on the end of the URL
						}
						else {
							ignoreHashChange = false;   
						}
					};
				}
			}
		</script>
    </body>
</html>
