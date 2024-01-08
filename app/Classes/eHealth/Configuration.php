<?php

namespace App\Classes\eHealth;

use Illuminate\Support\Facades\Config;

class Configuration
{


    public static  string $ApiUrl   ;

    public  static string  $ApiVersion = 'v2';


    public function setApiUrl($url): void
    {
        self::$ApiUrl = $url;
    }

    public static function getApiUrl()
    {
        return self::$ApiUrl = Config::get('ehealth.api.domain');
    }






}
