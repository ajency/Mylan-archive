<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Parse\ParseClient;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
    	ParseClient::initialize(config('constants.parse_sdk.app_id'), config('constants.parse_sdk.rest_api_key'),config('constants.parse_sdk.master_key'));
    	ParseClient::setServerURL(config('constants.parseServerUrl'),'parse');
    	
    }
}




