<?php

return [

	/*Mail Configuration*/
    
    'host'          => 'smtp.sendgrid.net',
    
    'username'          => 'shradha',
    
    'password'          => 'ajency#123',

    /****parse*******/
    'parse_sdk' => [
        'app_id' => env( 'APP_ID'),
        'rest_api_key' => env( 'REST_API_KEY'),
        'master_key' => env( 'MASTER_KEY')
    ],


];
 
