<?php

namespace App\Utils;

use Carbon\Carbon;

class CustomCarbon
{

    static function UTCtoCountryTZ($date, $hours = '00:00:00', $tz){

        $date = $date . ' ' . $hours;
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $date, $tz);
        // $date = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' 00:00:00', 'America/Sao_Paulo');
        $date->setTimezone('UTC');
        return $date;
    }

}