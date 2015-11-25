<?php

namespace App\Http\Middleware;

use Closure;

class APIAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        header("Access-Control-Allow-Origin: *");

        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type,  X-Authorization, X-API-KEY, Origin'
        ];

        $userId = (isset($reques['user_id']))?$reques['user_id']:0;
        $requestApiKey = \Request::header( 'X-API-KEY' );
        $requestXAuth = \Request::header( 'X-Authorization' );
        $apiKey = config('app.key');
        $x_auth = getUserApiKey( $userId ); 

        if(($apiKey!=$requestApiKey) || ($requestXAuth!=$x_auth))
          {
            echo '403 INVALID ACCESS';
            exit;
          }
            
        $response = $next($request);
        foreach($headers as $key => $value)
            $response->header($key, $value);
        
        return $response;
        
    }
}
