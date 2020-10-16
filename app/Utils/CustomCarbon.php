<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CustomCarbon
{

    static function countryTZtoUTC($date, $hours = null, $tz = null){

        empty($tz) ? $tz = Auth::user()->getTimezone() : null;

        if(!empty($hours)){
            $date = substr($date,0,10); //Get only the date part YYYY/MM/DD
            $date = $date . ' ' . $hours;
        }

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $date, $tz);
        // $date = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00', 'America/Sao_Paulo');
        $date->setTimezone('UTC');
        return $date;
    }

    static function UTCtoCountryTZ($date, $hours = null, $tz = null){

        empty($tz) ? $tz = Auth::user()->getTimezone() : null;

        if(!empty($hours)){
            $date = substr($date,0,10); //Get only the date part YYYY/MM/DD
            $date = $date . ' ' . $hours;
        }

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC');
        $date->setTimezone($tz);
        return $date;
    }

}